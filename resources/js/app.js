import './echo';

// Tell the browser to listen to our specific radio frequency
window.Echo.channel('radar-channel')
    .listen('PingEvent', (e) => {
        // When it hears the signal, make a popup!
        alert("📡 RADAR ALERT: " + e.message);
    });