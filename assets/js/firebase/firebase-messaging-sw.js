/** Again import google libraries */
importScripts("https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js");

/** Your web app's Firebase configuration 
 * Copy from Login 
 *      Firebase Console -> Select Projects From Top Naviagation 
 *      -> Left Side bar -> Project Overview -> Project Settings
 *      -> General -> Scroll Down and Choose CDN for all the details
*/
var config = {
    apiKey: "AIzaSyDHNyw2o1I8HBhXgWvYrSm86ZnF9ELgsF0",
    authDomain: "keralapush.firebaseapp.com",
    databaseURL: "https://stackcoder-c9aaf.firebaseio.com",
    projectId: "keralapush",
    storageBucket: "keralapush.appspot.com",
    messagingSenderId: "944763642805",
    appId: "1:944763642805:web:e22bd4aa4114f70e1d2ffc",
    measurementId: "G-0L3L7HD6G0"
};
firebase.initializeApp(config);

// Retrieve an instance of Firebase Data Messaging so that it can handle background messages.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) 
{
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = payload.data.title;
    const notificationOptions = 
    {
        body: payload.data.body,
        icon: payload.data.icon,
        image: payload.data.image,
        click_action: BASE_URL+'admin/orders', // To handle notification click when notification is moved to notification tray
        data: 
        {
            click_action: BASE_URL+'admin/orders'
        }
    };

    self.addEventListener('notificationclick', function(event) 
    {
        console.log("HELOOOOO"+event.notification.data.click_action);
        if (!event.action) 
        {
            // # Was a normal notification click
            console.log('Notification Click.');
            self.clients.openWindow(event.notification.data.click_action, '_blank')
            event.notification.close();
            return;
        }else
        {
            event.notification.close();
        }
    });
    return self.registration.showNotification(notificationTitle,notificationOptions);
});