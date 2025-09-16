// Global Page Transitions (View Transitions API with graceful fallback)
// - Fades and slightly translates content between full-page navigations
// - Respects prefers-reduced-motion
// - Avoids interfering with in-page anchors, downloads, external links, modified clicks
// - Keeps scroll position logic simple: scroll to top on nav

(function () {
  // Disable transitions on auth pages to avoid interfering with login/register
  try {
    if (document.body.classList && document.body.classList.contains('auth-page')) return;
  } catch (_) {}
  // Also disable on Filament/admin pages or when explicitly opted out at body level
  try {
    const p = window.location.pathname || '';
    if (document.body.hasAttribute('data-no-page-transitions')) return;
    if (/^\/(filament-admin|admin)\b/.test(p)) return;
  } catch (_) {}

  // Only run when explicitly enabled on the page
  const enabled = !!(document && document.body && document.body.hasAttribute('data-enable-page-transitions'));
  if (!enabled) return;

  // Clean up any stuck animation state from previous navigations (safety)
  try {
    const el0 = document.querySelector('[data-page-transition-root]') || document.body;
    if (el0 && el0.classList && el0.classList.contains('ov-animating')) {
      el0.classList.remove('ov-animating');
    }
  } catch (_) {}

  // Hard-disable transitions on Messages pages to avoid any interaction blockers
  try {
    const here = window.location.pathname || '';
    if (/^\/messages\b/.test(here)) return;
  } catch (_) {}

  const root = () => document.querySelector('[data-page-transition-root]') || document.body;
  const supportsViewTransitions = () => typeof document.startViewTransition === 'function';
  // Track intended direction and click origin for nicer transitions
  let nextDirection = 'down';
  let lastClick = { x: 0.5, y: 0.5 };

  // Simple in-memory prefetch cache
  const ovCache = new Map();
  const CACHE_TTL_MS = 10000;
  function cacheGet(url) {
    const item = ovCache.get(url);
    if (!item) return null;
    if (Date.now() - item.ts > CACHE_TTL_MS) { ovCache.delete(url); return null; }
    return item.html;
  }
  function cacheSet(url, html) { ovCache.set(url, { html, ts: Date.now() }); }

  const prefersReducedMotion = () => {
    try {
      return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch (_) {
      return false;
    }
  };

  function shouldIntercept(event, anchor) {
    // Never intercept events already handled
    if (event && (event.defaultPrevented || event.cancelBubble)) return false;
    if (!anchor) return false;

    // Only intercept explicit navigation links.
    // Require either class="ov-nav" or data-ov-transition attribute.
    // This prevents interference with tabs, dashboards, and JS-controlled links.
    const isExplicitNav = anchor.classList.contains('ov-nav') || anchor.hasAttribute('data-ov-transition');
    if (!isExplicitNav) return false;

    // Skip anchors used by JS/tab systems
    const linkRole = (anchor.getAttribute('role') || '').toLowerCase();
    if (linkRole === 'tab') return false;
    if (anchor.hasAttribute('data-toggle') || anchor.hasAttribute('aria-controls')) return false;
    if (anchor.hasAttribute('onclick')) return false;
    if ((anchor.getAttribute('href') || '').trim().toLowerCase().startsWith('javascript:')) return false;
    if (anchor.closest('[data-tabs],[role="tablist"],[data-controller],[data-action],[data-livewire],[wire\\:navigate]')) return false;

    // Destination URL checks (skip auth-related pages entirely)
    try {
      const dest = new URL(anchor.href, window.location.href);
      const p = dest.pathname || '';
      if (/^\/(custom\/)?login\b/.test(p)) return false;
      if (/^\/(custom\/)?register\b/.test(p)) return false;
      if (/^\/logout\b/.test(p)) return false;
      if (/^\/password\//.test(p)) return false;
      if (/^\/email\//.test(p)) return false;
      if (/^\/messages\b/.test(p)) return false;
      if (/^\/(filament-admin|admin)\b/.test(p)) return false;
    } catch(_) {}
    
    // Don't intercept if this is a form submission or inside a form
    if (anchor.closest('form') || anchor.tagName.toLowerCase() === 'button' || anchor.type === 'submit') return false;
    
    // Skip links that act as tabs or JS toggles
    const targetRole = (anchor.getAttribute('role') || '').toLowerCase();
    if (targetRole === 'tab' || anchor.hasAttribute('data-toggle') || anchor.hasAttribute('aria-controls')) return false;

    // Don't intercept if the event target is a form element
    if (event.target && (event.target.tagName.toLowerCase() === 'button' || event.target.type === 'submit' || event.target.closest('form'))) return false;
    
    // Only same-origin normal navigations
    const url = new URL(anchor.href, window.location.href);
    const sameOrigin = url.origin === window.location.origin;
    const samePage = url.pathname === window.location.pathname && url.search === window.location.search && url.hash === '';

    if (!sameOrigin) return false;

    // Skip if target="_blank" or download or has rel="external"
    if (anchor.target && anchor.target.toLowerCase() === '_blank') return false;
    if (anchor.hasAttribute('download')) return false;
    if (anchor.rel && anchor.rel.includes('external')) return false;

    // Modified clicks: new tab, etc.
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) return false;

    // In-page anchors (hash only) use default behavior
    if (anchor.hash && (url.pathname === window.location.pathname)) return false;

    // Avoid intercepting when explicitly opted out
    if (anchor.hasAttribute('data-no-transition')) return false;

    // Don't re-navigate to the same page without hash
    if (samePage) return false;

    return true;
  }

  async function fetchHtml(url, { useCache = false, prefetch = false } = {}) {
    if (useCache) {
      const cached = cacheGet(url);
      if (cached) return cached;
    }
    const response = await fetch(url, { headers: { 'X-Requested-With': 'fetch' } });
    if (!response.ok) throw new Error('Navigation failed');
    const html = await response.text();
    // Store in cache unless explicitly disabled
    if (useCache || prefetch) cacheSet(url, html);
    return html;
  }

  function rehydrate(target) {
    // Re-initialize Alpine on newly injected DOM
    try { if (window.Alpine && typeof window.Alpine.initTree === 'function') window.Alpine.initTree(target); } catch (_) {}
    // Re-initialize Livewire (v3)
    try { if (window.Livewire && typeof window.Livewire.restart === 'function') window.Livewire.restart(); } catch (_) {}
    // Notify any listeners that navigation finished
    try { document.dispatchEvent(new CustomEvent('ov:navigated')); } catch (_) {}
  }

  function markPageTitle(rootEl) {
    try {
      const title = rootEl?.querySelector('h1, [data-ov-page-title]');
      if (title) title.style.viewTransitionName = 'ov-title';
    } catch (_) {}
  }

  function swapContent(nextHtml) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(nextHtml, 'text/html');

    // Replace <title>
    const newTitle = doc.querySelector('title');
    if (newTitle) document.title = newTitle.textContent || document.title;

    // Extract the next root content
    const currentRoot = root();
    const nextRoot = doc.querySelector('[data-page-transition-root]');

    if (!currentRoot || !nextRoot) {
      // Fallback: replace <body> content and sync body attributes/classes
      try {
        document.body.className = doc.body.className || '';
        // Copy over arbitrary data attributes from new body to current body
        for (const attr of Array.from(document.body.attributes)) {
          if (attr.name.startsWith('data-')) document.body.removeAttribute(attr.name);
        }
        for (const attr of Array.from(doc.body.attributes)) {
          if (attr.name.startsWith('data-')) document.body.setAttribute(attr.name, attr.value);
        }
      } catch(_) {}
      document.body.innerHTML = doc.body.innerHTML;
      // Rehydrate entire body content since we replaced it wholesale
      rehydrate(document.body);
      return;
    }

    currentRoot.replaceChildren(...Array.from(nextRoot.childNodes));
    rehydrate(currentRoot);
  }

  async function navigateWithTransition(url) {
    // Prepare scroll position on the content scroller (not window)

    if (prefersReducedMotion()) {
      const html = await fetchHtml(url);
      swapContent(html);
      window.history.pushState({}, '', url);
      return;
    }

    if (supportsViewTransitions()) {
      // Ensure the root participates in view transition
      const el = root();
      if (el) {
        el.style.viewTransitionName = 'ov-page';
        el.classList.add('ov-animating');
        // Pass the direction and transform origin for CSS to consume
        el.dataset.ovDirection = nextDirection;
      }

      // Mark current page title for shared element transition
      if (el) markPageTitle(el);

      const vt = document.startViewTransition(async () => {
        const html = await fetchHtml(url, { useCache: true });
        swapContent(html);
        // Mark new page title
        const newRoot = root();
        if (newRoot) markPageTitle(newRoot);
        // Reset scroller to top
        const scroller = newRoot || root();
        if (scroller) scroller.scrollTop = 0;
        window.history.pushState({}, '', url);
      });
      await vt.finished.catch(() => {});

      if (el) {
        el.style.viewTransitionName = '';
        el.classList.remove('ov-animating');
        delete el.dataset.ovDirection;
      }
      return;
    }

  // CSS fallback: add classes to root (transform + subtle fade)
  const el = root();
  const map = { right: 'pt-enter-right', left: 'pt-enter-left', up: 'pt-enter-up', down: 'pt-enter-down' };
  const enterClass = map[nextDirection] || 'pt-enter-soft';
  try {
    el.classList.add('ov-animating');

    // Fetch first (likely cached), then swap and animate only the new state
    const html = await fetchHtml(url, { useCache: true });
    swapContent(html);
    // Reset scroller to top
    if (el) el.scrollTop = 0;
    // Ensure styles are applied before starting animation
    await new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r)));
    el.classList.add(enterClass);
    await new Promise((r) => setTimeout(r, 190));
    el.classList.remove(enterClass);
  } finally {
    if (el) el.classList.remove('ov-animating');
  }
  }

  // Initial reveal on full page load
  (function initialReveal(){
    try {
      if (!enabled) return;
      if (prefersReducedMotion()) return;
      const el = root();
      if (!el) return;
      el.classList.add('pt-initial');
      // Default direction on first paint
      el.dataset.ovDirection = 'down';
      setTimeout(() => {
        el.classList.remove('pt-initial');
        delete el.dataset.ovDirection;
      }, 160);
    } catch (_) {}
  })();

  // Passive prefetch on hover / pointerdown for sidebar links
  function canPrefetch(anchor) {
    if (!anchor) return false;
    if (navigator.connection && navigator.connection.saveData) return false;
    try {
      const url = new URL(anchor.href, window.location.href);
      if (url.origin !== window.location.origin) return false;
      return anchor.classList.contains('ov-nav');
    } catch (_) { return false; }
  }
  function prefetchUrl(anchor) {
    if (!canPrefetch(anchor)) return;
    const url = anchor.href;
    if (cacheGet(url)) return;
    fetchHtml(url, { prefetch: true }).catch(() => {});
  }

  document.addEventListener('mouseover', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    prefetchUrl(a);
  }, { passive: true });
  document.addEventListener('pointerdown', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    prefetchUrl(a);
  }, { passive: true });
  document.addEventListener('touchstart', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    prefetchUrl(a);
  }, { passive: true });

  // Sidebar ripple (Apple-like) effect on click
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a.ov-nav');
    if (!a) return;
    const rect = a.getBoundingClientRect();
    const x = ('clientX' in e) ? (e.clientX - rect.left) : rect.width / 2;
    const y = ('clientY' in e) ? (e.clientY - rect.top)  : rect.height / 2;
    const ripple = document.createElement('span');
    ripple.className = 'ov-ripple';
    ripple.style.left = `${x}px`;
    ripple.style.top  = `${y}px`;
    a.appendChild(ripple);
    // Remove after animation
    setTimeout(() => ripple.remove(), 350);
  }, { passive: true });

  // Sidebar active underline indicator (removed in favor of per-link underline)
  function updateSidebarIndicator(targetLink = null) {
    return;
    const scrollEl = document.querySelector('[data-ov-sidebar-scroll]');
    if (!scrollEl) return;
    let indicator = scrollEl.querySelector('.ov-sidebar-indicator');
    if (!indicator) {
      indicator = document.createElement('div');
      indicator.className = 'ov-sidebar-indicator';
      scrollEl.appendChild(indicator);
    }
    const active = targetLink || document.querySelector('a.ov-nav[aria-current="page"]') || document.querySelector('a.ov-nav[href="'+location.pathname+'"]');
    if (!active) return;
    const rectA = active.getBoundingClientRect();
    const rectS = scrollEl.getBoundingClientRect();
    const y = (rectA.top - rectS.top) + scrollEl.scrollTop;
    const h = rectA.height;
    indicator.style.height = h + 'px';
    indicator.style.transform = `translateY(${y}px)`;
  }

  // Initialize indicator and hover tracking
  // (no global indicator updates needed with per-link underline)

  // Global click interceptor
  window.addEventListener('click', (event) => {
    const anchor = event.target.closest('a');
    if (!anchor) return;
    if (!shouldIntercept(event, anchor)) return;

    // Determine direction by nav intent
    const isSidebarNav = anchor.classList.contains('ov-nav');
    if (isSidebarNav) {
      // Prefer per-link override
      const attrDir = anchor.getAttribute('data-ov-direction');
      if (attrDir === 'left' || attrDir === 'right' || attrDir === 'up' || attrDir === 'down') {
        nextDirection = attrDir;
      } else {
        // Heuristic fallback
        const href = anchor.getAttribute('href') || '';
        if (/\/marketplace\//.test(href) || /\bmessages\b/.test(href) || /\bdashboard\b/.test(href)) {
          nextDirection = 'right';
        } else if (/\bprofile\b/.test(href) || /\breviews\b/.test(href) || /\bcontracts\b/.test(href)) {
          nextDirection = 'left';
        } else {
          nextDirection = 'right';
        }
      }
    } else {
      nextDirection = 'down';
    }

    // Compute transform origin from click position relative to root
    try {
      const el = root();
      const rect = el.getBoundingClientRect();
      const cx = 'clientX' in event ? event.clientX : (event.touches ? event.touches[0].clientX : rect.left + rect.width / 2);
      const cy = 'clientY' in event ? event.clientY : (event.touches ? event.touches[0].clientY : rect.top + rect.height / 2);
      const ox = Math.max(0, Math.min(100, ((cx - rect.left) / Math.max(1, rect.width)) * 100));
      const oy = Math.max(0, Math.min(100, ((cy - rect.top) / Math.max(1, rect.height)) * 100));
      lastClick = { x: ox / 100, y: oy / 100 };
      // Expose as CSS vars for ::view-transition-* to use
      document.documentElement.style.setProperty('--ov-origin-x', ox + '%');
      document.documentElement.style.setProperty('--ov-origin-y', oy + '%');
      // Also set on the root for fallback animations
      el.style.transformOrigin = `${ox}% ${oy}%`;
    } catch (_) {}

    event.preventDefault();
    navigateWithTransition(anchor.href).catch(() => {
      window.location.href = anchor.href;
    });
  });

  // Handle back/forward
  window.addEventListener('popstate', () => {
    // Back/forward should feel like sliding from the left
    nextDirection = 'left';
    // Reload with transition when supported; otherwise let the browser handle it
    if (supportsViewTransitions() && !prefersReducedMotion()) {
      navigateWithTransition(window.location.href).catch(() => {
        window.location.reload();
      });
    } else {
      window.location.reload();
    }
  });
})();

