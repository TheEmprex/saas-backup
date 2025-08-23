// Deprecated standalone Echo initializer. Using shared Echo configured in resources/js/echo.js.
// This file now only exposes a no-op to avoid duplicate initializations.

function connectToMessaging() {
    console.warn('connectToMessaging is deprecated. Use the shared Echo instance and subscribe directly.')
}

window.connectToMessaging = connectToMessaging;

