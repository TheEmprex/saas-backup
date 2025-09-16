// Centralized fetch helper with CSRF, credentials, and JSON handling
// Exports default function and assigns window.apiFetch for inline usage.

function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

async function apiFetch(input, options = {}) {
  const headers = new Headers(options.headers || {})
  headers.set('Accept', headers.get('Accept') || 'application/json')
  const csrf = getCsrfToken()
  if (csrf) headers.set('X-CSRF-TOKEN', csrf)

  const init = {
    method: options.method || 'GET',
    credentials: options.credentials || 'same-origin',
    headers,
    signal: options.signal,
    body: options.body,
  }

  // If body is a plain object/string and not FormData, send as JSON
  if (init.body && !(init.body instanceof FormData)) {
    if (typeof init.body === 'object') {
      headers.set('Content-Type', headers.get('Content-Type') || 'application/json')
      init.body = JSON.stringify(init.body)
    }
  }

  const response = await fetch(input, init)

  let data = null
  const ct = response.headers.get('content-type') || ''
  if (ct.includes('application/json')) {
    try { data = await response.json() } catch (_) { data = null }
  } else {
    // Fallback to text when not JSON
    try { data = await response.text() } catch (_) { data = null }
  }

  if (!response.ok) {
    if (response.status === 401 || response.status === 419) {
      // Broadcast an event that callers can listen to
      window.dispatchEvent(new CustomEvent('auth:required', { detail: { status: response.status } }))
    }
    const error = new Error(`Request failed: ${response.status}`)
    error.response = response
    error.data = data
    error.status = response.status
    throw error
  }

  return { ok: true, status: response.status, data, response }
}

window.apiFetch = window.apiFetch || apiFetch
export default apiFetch
