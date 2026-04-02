importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
const firebaseConfig = {
   apiKey: "AIzaSyCQ2odKIHO8VRf3f19xWJGcGrbsNSk9MjI",
  authDomain: "bookhub-7ae4a.firebaseapp.com",
  projectId: "bookhub-7ae4a",
  storageBucket: "bookhub-7ae4a.firebasestorage.app",
  messagingSenderId: "198004101288",
  appId: "1:198004101288:web:34b607d70fe5196ba8f29d",
  measurementId: "G-V29VCCF482"
};

firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.image || '/logo.png'
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
