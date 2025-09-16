<button id="enable-push-btn"
        class="hidden md:inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-indigo-600 hover:bg-indigo-700 text-white transition"
        type="button">
    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
    </svg>
    Enable Alerts
</button>
<script>
(function(){
  const btn = document.getElementById('enable-push-btn');
  if (!btn) return;

  const showBtnIfSupported = () => {
    const supported = 'serviceWorker' in navigator && 'PushManager' in window;
    if (!supported) return;
    btn.classList.remove('hidden');
  };

  const getUsePWA = () => (window.__usePWA ? window.__usePWA : null);

  showBtnIfSupported();

  btn.addEventListener('click', async () => {
    try {
      const usePWA = getUsePWA();
      if (!usePWA) {
        alert('PWA features are not available yet. Please try again after the page finishes loading.');
        return;
      }
      const { requestNotificationPermission, subscribeToPush, showNotification } = usePWA();
      const granted = await requestNotificationPermission();
      if (!granted) { alert('Please allow notifications to receive alerts'); return; }
      const res = await subscribeToPush();
      if (res.ok || res.existing) {
        showNotification('Push enabled', { body: 'You will receive alerts for new activity.' });
        btn.textContent = 'Alerts Enabled';
        btn.disabled = true;
        btn.classList.add('opacity-60');
      } else {
        alert('Failed to enable push: ' + (res.reason || 'unknown'));
      }
    } catch (e) {
      console.error(e);
      alert('Could not enable notifications');
    }
  });
})();
</script>

