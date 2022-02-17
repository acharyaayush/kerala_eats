/** Your web app's Firebase configuration 
 * Copy from Login 
 *      Firebase Console -> Select Projects From Top Naviagation 
 *      -> Left Side bar -> Project Overview -> Project Settings
 *      -> General -> Scroll Down and Choose CDN for all the details
*/
var firebaseConfig = {
    apiKey: "AIzaSyDHNyw2o1I8HBhXgWvYrSm86ZnF9ELgsF0",
    authDomain: "keralapush.firebaseapp.com",
    databaseURL: "https://stackcoder-c9aaf.firebaseio.com",
    projectId: "keralapush",
    storageBucket: "keralapush.appspot.com",
    messagingSenderId: "944763642805",
    appId: "1:944763642805:web:e22bd4aa4114f70e1d2ffc",
    measurementId: "G-0L3L7HD6G0"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// alert("session_id"+session_id);
/**
 * We can start messaging using messaging() service with firebase object
 */
var messaging = firebase.messaging();

/** Register your service worker here
 *  It starts listening to incoming push notifications from here
 */
 // alert(BASE_URL+'assets/js/firebase/firebase-messaging-sw.js');
navigator.serviceWorker.register(BASE_URL+'assets/js/firebase/firebase-messaging-sw.js')
.then(function (registration) {
    /** Since we are using our own service worker ie firebase-messaging-sw.js file */
    messaging.useServiceWorker(registration);
 
//alert("registration"+registration);
 
// console.log(registration);
    /** Lets request user whether we need to send the notifications or not */
    messaging.requestPermission()
        .then(function () {
            /** Standard function to get the token */
            messaging.getToken()
            .then(function(token) {
                /** Here I am logging to my console. This token I will use for testing with PHP Notification */
                // alert('updating token'+token);
                console.log("Consoling token here"+token);
                // alert("token"+token);

                $.ajax({
                    url: BASE_URL+'admin/update_device_token/',//merchant detail
                    data: { 
                        token: token,
                    },
                    type: 'post',
                });
                /** SAVE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
            })
            .catch(function(error) {
                // alert('Error while fetching the token ' + error);
                /** If some error happens while fetching the token then handle here */
                updateUIForPushPermissionRequired();
                console.log('Error while fetching the token ' + error);
            });
        })
        .catch(function (error) {
            // alert('Permission denied ' + error);
            /** If user denies then handle something here */
            console.log('Permission denied ' + error);
        })
})
.catch(function () {
    console.log('Error in registering service worker');
});

/** What we need to do when the existing token refreshes for a user */
messaging.onTokenRefresh(function() {
    // alert('11111');
    messaging.getToken()
    .then(function(renewedToken) {
        console.log(renewedToken);
        /** UPDATE TOKEN::From here you need to store the TOKEN by AJAX request to your server */
    })
    .catch(function(error) {
        /** If some error happens while fetching the token then handle here */
        console.log('Error in fetching refreshed token ' + error);
    });
});

// Handle incoming messages
messaging.onMessage(function(payload) {
    // alert('2222222'+BASE_URL);
    console.log("Conslole from firebase.js"+payload);
    const notificationTitle = 'New order received';
    const notificationOptions = {
        body: '\r\nA new order has been placed.',
        icon: BASE_URL+'assets/img/favicon.png',
        // click_action: BASE_URL+'admin/orders/',
        // click_action: BASE_URL+'admin/orders/', // To handle notification click when notification is moved to notification tray
          data: {
              click_action: BASE_URL+'admin/orders/'
          }
    };
    var notification = new Notification(notificationTitle,notificationOptions);
    // return self.registration.showNotification(notificationTitle, notificationOptions);
});