importScripts('https://www.gstatic.com/firebasejs/7.16.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.16.0/firebase-messaging.js');

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

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();
//messaging.usePublicVapidKey('BM0dCGba4ZebhnheLVOTFxk5W4Srkfr0HNZrrdczN1gpxKLkdxCGCOtXw9huI3tpID7VRfgm1zKkIN_ONLFf3tI');

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon,
        fcm_options: {
            link: payload.notification.click_action,
        }
    };
    return self.registration.showNotification(notificationTitle,
        notificationOptions);
});