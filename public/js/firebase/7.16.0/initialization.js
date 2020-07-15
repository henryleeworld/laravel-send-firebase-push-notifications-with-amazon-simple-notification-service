var firebaseConfig = {
    apiKey: "AIzaSyCnNUyhsWgYC3xUGtVv65KEkUIw6-CeeIM",
    authDomain: "core-site-274306.firebaseapp.com",
    databaseURL: "https://core-site-274306.firebaseio.com",
    projectId: "core-site-274306",
    storageBucket: "core-site-274306.appspot.com",
    messagingSenderId: "485535666009",
    appId: "1:485535666009:web:81b4f570f86007e744c648",
    measurementId: "G-80TEH3V91N"
};

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js').then(function(registration) {
        console.log('ServiceWorker registration successful with scope: ', registration.scope);
        messaging.useServiceWorker(registration);
    }).catch(function(err) {
        console.log('ServiceWorker registration failed: ', err);
    });
}

messaging.usePublicVapidKey('BM0dCGba4ZebhnheLVOTFxk5W4Srkfr0HNZrrdczN1gpxKLkdxCGCOtXw9huI3tpID7VRfgm1zKkIN_ONLFf3tI');
requestPermission();

messaging.onMessage((payload) => {
    console.log('Message received. ', payload);
    if (Notification.permission === 'granted') {
        notifyMe(payload);
    }
});

messaging.onTokenRefresh(() => {
    messaging.getToken().then((refreshedToken) => {
        console.log('Token refreshed.');
        setTokenSentToServer(false);
        sendTokenToServer(refreshedToken);
        resetUI();
    }).catch((err) => {
        console.log('Unable to retrieve refreshed token ', err);
    });
});

function deleteToken() {
    messaging.getToken().then((currentToken) => {
        messaging.deleteToken(currentToken).then(() => {
            console.log('Token deleted.');
            setTokenSentToServer(false);
            resetUI();
        }).catch((err) => {
            console.log('Unable to delete token. ', err);
        });
    }).catch((err) => {
        console.log('Error retrieving Instance ID token. ', err);
        showToken('Error retrieving Instance ID token. ', err);
    });

}

function isTokenSentToServer() {
    return window.localStorage.getItem('sentToServer') === '1';
}

function notifyMe(payload) {
    if (!("Notification" in window)) {
        console.log("This browser does not support desktop notification");
    } else if (Notification.permission === "granted") {
        showWin(payload);
    } else if (Notification.permission !== 'denied' || Notification.permission === "default") {
        Notification.requestPermission(function(permission) {
            if (permission === "granted") {
                showWin(payload);
            };
        });
    };
};

function requestPermission() {
    console.log('Requesting permission...');
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            resetUI();
        } else {
            console.log('Unable to get permission to notify.');
        }
    });
}

function resetUI() {
    messaging.getToken().then((currentToken) => {
        if (currentToken) {
            console.log(currentToken);
            sendTokenToServer(currentToken);
        } else {
            console.log('No Instance ID token available. Request permission to generate one.');
            setTokenSentToServer(false);
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
        setTokenSentToServer(false);
    });
}

function sendTokenToServer(currentToken) {
    if (!isTokenSentToServer()) {
        console.log('Sending token to server...');
        var xhr = new XMLHttpRequest();
        csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        xhr.open('POST', 'device/token', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                console.log(response);
            }
        };
        xhr.send(encodeURI('token=' + currentToken));
        setTokenSentToServer(true);
    } else {
        console.log('Token already sent to server so won\'t send it again ' +
            'unless it changes');
    }
}

function showWin(payload) {
    console.log('onMessage: ', payload.notification);
    var notification = new Notification(payload.notification.title, {
        body: payload.notification.body,
        icon: payload.notification.icon
    });
    notification.onclick = function(e) {
        e.preventDefault(); // prevent the browser from focusing the Notification's tab
        window.open(payload.notification.click_action);
    };
    /*
    setTimeout(function() {
        notification.close()
    }, 5000);
    */
}

function setTokenSentToServer(sent) {
    window.localStorage.setItem('sentToServer', sent ? '1' : '0');
}