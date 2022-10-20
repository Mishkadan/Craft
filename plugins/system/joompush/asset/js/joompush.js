jQuery( document ).ready(function() {
	var config = {
		apiKey: "AIzaSyCcVDN60TctSavmON-n5m2JZ8XZDBm_HsM",
		authDomain: "craft-f6d1b.firebaseapp.com",
		projectId: "craft-f6d1b",
		storageBucket: "craft-f6d1b.appspot.com",
		messagingSenderId: "113922536772",
		appId: "1:113922536772:web:c2faba2d053d1037819311",
		authDomain: project_id + ".firebaseapp.com",
		databaseURL: "https://" + project_id + ".firebaseio.com",
		storageBucket: project_id + ".appspot.com",
		messagingSenderId: messagingSenderId,
	};

	firebase.initializeApp(config);

	// Retrieve Firebase Messaging object.
	const messaging = firebase.messaging();

	// [START refresh_token]
    // Callback fired if Instance ID token is updated.
	messaging.onTokenRefresh(() => {
		messaging.getToken().then((refreshedToken) => {
			console.log('Token refreshed.');
			// Indicate that the new Instance ID token has not yet been sent to the
			// app server.
			setTokenSentToServer(false);
			// Send Instance ID token to app server.
			sendTokenToServer(refreshedToken);
			// [START_EXCLUDE]
			jpInit();
			// [END_EXCLUDE]
		}).catch((err) => {
			console.log('Unable to retrieve refreshed token ', err);
			showToken('Unable to retrieve refreshed token ', err);
		});
	});

	// Initialize script
	setTimeout(function (){jpInit();}, 3000);


	// Main Function
	function jpInit() {
//console.log(window.localStorage);
		if (!isTokenSentToServer()) {

		//	var subscribe = confirm('У вас не включено получение уведомлений чата и событий, включить сейчас?');
		//	if (subscribe) {
				// On load register service worker
				if ('serviceWorker' in navigator) {
					navigator.serviceWorker.register(sw_url).then((registration) => {
						// Successfully registers service worker
						console.log('ServiceWorker register success: ', registration.scope);
						messaging.useServiceWorker(registration);
					})
						.then(() => {
							// Requests user browser permission
							return messaging.requestPermission();
						})
						.then(() => {
							// Gets token
							return messaging.getToken();
						})
						.then((token) => {
							//console.log(token);
							if (token) {
								sendTokenToServer(token);
							} else {
								setTokenSentToServer(false);
							}
						})
						.catch((err) => {
							console.log('ServiceWorker registration failed: ', err);
						});
				}
		//	}
		}}
});

function sendTokenToServer(token) {
	 if (!isTokenSentToServer()) {
	  console.log('Sending token to server...');
	  var storeurl = baseurl + 'index.php?option=com_joompush&task=mynotifications.setSubscriber';
	  jQuery.ajax({
		type: 'post',
		url: storeurl,
		data: {key: token, IsClient: isClient, Userid: userid},
		success: (data) => {
			alert('Теперь вы можете получать все уведомления!');
		  console.log('Success ', data);
		  setTokenSentToServer(true);
		},
		error: (err) => {
		  console.log('Error ', err);
		}
	  });
	} else {
	  console.log('Token already sent to server so won\'t send it again ' +
		  'unless it changes');
	}
}

function isTokenSentToServer() {
	return window.localStorage.getItem('PushSentToServer') === '1';
}

function setTokenSentToServer(sent) {
	window.localStorage.setItem('PushSentToServer', sent ? '1' : '0');
}
