// firebase-config-client.js
// Replace the config with your project's settings
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_PROJECT_ID.appspot.com",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Request permission and get token
function registerMessaging() {
    messaging.requestPermission()
        .then(function() {
            console.log('Notification permission granted.');
            return messaging.getToken();
        })
        .then(function(token) {
            console.log('FCM Token:', token);
            saveToken(token);
        })
        .catch(function(err) {
            console.log('Unable to get permission to notify.', err);
        });
}

// Send token to server
function saveToken(token) {
    $.ajax({
        url: window.BASE_URL + '/student/notifications/register-token',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            token: token,
            device_type: 'web'
        },
        success: function(response) {
            console.log('Token saved successfully');
        },
        error: function(xhr) {
            console.log('Error saving token:', xhr);
        }
    });
}

// Handle foreground messages
messaging.onMessage((payload) => {
    console.log('Message received. ', payload);
    // Show user a toast or alert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: payload.notification.title,
            text: payload.notification.body,
            icon: 'info',
            image: payload.notification.image,
        });
    } else {
        alert(payload.notification.title + ': ' + payload.notification.body);
    }
});

// Auto-register on page load if user is logged in
$(document).ready(function() {
    if (firebase.apps.length > 0) {
        registerMessaging();
    }
});
