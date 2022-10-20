/***
 * Craft PWA popups 19/08/2022
 * @ global var safari : boolean
 * @ global var pwapopup : pwaformbanner
 */
jQuery(document).ready(function ($) {

    $('body').append(pwapopup);

        var cookieBlockInstallCookieHide = getCookie('BlockPopupPWA'),
            installbtn = $('#BlockInstallButton'),
            closebtn = $('#BlockInstallClose'),
            pwa = $('#BlockInstall'),
            iosPrompt = $(".ios-prompt"),
            deferredPrompt;

        /*** ANDROID ***/

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        if (!cookieBlockInstallCookieHide) {
            deferredPrompt = e;
            showAddToHomeScreen();
        }
    });

        function addToHomeScreen() {
            pwa.hide();
            deferredPrompt.prompt();
            deferredPrompt.userChoice
                .then(function (R) {
                    if (R.outcome === 'accepted') {
                        console.log('Подтверждено');
                    } else {
                        console.log('Слился...');
                    }
                    deferredPrompt = null;
                });
        }

    function showAddToHomeScreen() {
        pwa.show();
        installbtn.on('click', addToHomeScreen);
    }

    closebtn.on('click', function (e) {
        pwa.hide();
        setCookie('BlockPopupPWA', 1, 14);
    });


     /******* IOS *******/
    const isIos = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(userAgent);
    }

    const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

    if (isIos() && !isInStandaloneMode() && !cookieBlockInstallCookieHide) {

        showIosInstall();
    }

    function showIosInstall() {
        iosPrompt.show();
        iosPrompt.find('small').on('click', () => {
            iosPrompt.hide();
            setCookie('BlockPopupPWA', 1, 14);
        });
    }


/*** COOKIE ***/
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

/***
function checkCookie() {
    var user = getCookie("username");
    if (user != "") {
        alert('Добро пожаловать снова, ' + user);
    } else {
        user = prompt('Введите Ваше имя', "");
        if (user != "" && user != null) {
            setCookie("username", user, 365);
        }
    }
}***/
});
