import Echo from "laravel-echo";
import Pusher from "pusher-js";

Pusher.logToConsole = true;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: "your-pusher-app-key",
    cluster: "mt1",
    forceTLS: true,
});

// Function to connect and listen for messages
function connectToMessaging(userId) {
    // Subscribe to private user messages
    window.Echo.private(`messages.user.${userId}`)
        .listen("MessageSent", (e) => {
            console.log("Message received: ", e.message);
            // TODO: Handle new message received
        })
        .listenForWhisper("typing", (e) => {
            console.log("User typing: ", e);
            // TODO: Show typing indicator
        });

    // Subscribe to online status
    window.Echo.channel("user-status")
        .listen("UserOnlineStatusChanged", (e) => {
            console.log("User status changed: ", e);
            // TODO: Update user status
        });
}

window.connectToMessaging = connectToMessaging;

