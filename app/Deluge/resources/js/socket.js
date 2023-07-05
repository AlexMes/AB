import Echo from "laravel-echo";
import io from "socket.io-client";

window.io = io;

if (window.UID !== null) {
    // Setup Echo connection
    window.Echo = new Echo({
        namespace: "App.CRM.Events",
        broadcaster: "socket.io",
        host: window.location.hostname
    });

    window.Echo.private("App.User." + window.UID)
        .listen("NewLeadAssigned", event => {
            if (Notification.permission === "granted") {
                new Audio("/crm/sounds/money.mp3").play();
                const notification = new Notification(
                    "Вам назначен новый лид!",
                    {
                        body: event.fullname,
                        dir: "auto",
                        icon:
                            "https://tailwindui.com/img/logos/workflow-mark-on-dark.svg",
                        requireInteraction: true
                    }
                );
                notification.onclick = () => {
                    notification.close();
                    window.location = event.url;
                };
            }
        })
        .listen("Callback", event => {
            if (Notification.permission === "granted") {
                const notification = new Notification(
                    `Перезвон в ${event.callback_at_time}`,
                    {
                        body: `Перезвонить ${event.fullname} в ${event.callback_at_time} ${event.callback_at_date}`,
                        dir: "auto",
                        icon:
                            "https://tailwindui.com/img/logos/workflow-mark-on-dark.svg",
                        requireInteraction: true
                    }
                );
                notification.onclick = () => {
                    notification.close();
                    window.location = event.url;
                };
            }
        });
}

// Ask user permissions to send desktop notifications
Notification.requestPermission();
