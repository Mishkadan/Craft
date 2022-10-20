/*
* @package 		progressivewebappmaker - Progressive Web App Maker
* @version		1.0.5
* @created		Jan 2020
* @author		ExtensionCoder.com
* @email		developer@extensioncoder.com
* @website		https://www.extensioncoder.com
* @support		https://www.extensioncoder.com/support.html
* @copyright	Copyright (C) 2019-2020 ExtensionCoder. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
function onNetworkChangeStatus(e){
    var o=jQuery("#pwamaker_pwa_offline_bar");
    navigator.onLine?o.hide():o.show()
}
jQuery.noConflict(),
    jQuery(window).load(function() {
        "serviceWorker" in navigator && navigator.serviceWorker.register(pwamakerSiteUrl+"pwa_maker_service_worker.js",
            {scope:pwamakerSiteUrl}).then(function(e){
               // console.log("ServiceWorker registration successful with scope: ",e.scope)
            }).catch(function(e){
                console.error("ServiceWorker registration failed: "+e)})
    }),
    window.addEventListener("load",function(){
        window.addEventListener("offline",onNetworkChangeStatus),
            window.addEventListener("online",onNetworkChangeStatus),
            onNetworkChangeStatus(!1)
    }),
    jQuery(function(){
        jQuery(window).on("beforeunload",function(){
            jQuery("#pwamaker_pwa-loader-overlay").length>0&&jQuery("#pwamaker_pwa-loader-overlay").show()
        })
    });