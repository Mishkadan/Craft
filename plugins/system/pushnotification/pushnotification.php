<?php
/*
* @package 		pushnotification - Push Notification Plugin
* @version		1.0.2
* @created		Jan 2020
* @author		ExtensionCoder.com
* @email		developer@extensioncoder.com
* @website		https://www.extensioncoder.com
* @support		https://www.extensioncoder.com/support.html
* @copyright	Copyright (C) 2019-2020 ExtensionCoder. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
defined('_JEXEC') or die;
class plgSystemPushNotification extends JPlugin
{
	public function onAfterInitialise()
			{
				if (JFactory::getApplication()->isAdmin()) return;
				$appid = $this->params->get('appid','');
				//var_dump($appid);
				$selecthttp = $this->params->get('selecthttp','http');
				$jAp = JFactory::getApplication();  // fix the doc error
				$pformat = $jAp->input->get('format');  // format
					if ( $pformat !== 'raw' )
					{

						if ( $selecthttp == 'http' )
						{
							
							$document = JFactory::getDocument();
							$document->addScript('https://cdn.onesignal.com/sdks/OneSignalSDK.js');
							$document->addScriptDeclaration('
										  var OneSignal = window.OneSignal || [];
										  OneSignal.push(function() {
											OneSignal.init({
											  appId: "'.$appid.'",
											});
										  });							
										');
						} 
						else 
						{

							$document = JFactory::getDocument();
							$manifestfile	= '<link rel="manifest" href="manifest.json">';
							$document->addCustomTag($manifestfile);
							$document->addScript('https://cdn.onesignal.com/sdks/OneSignalSDK.js');
							$document->addScriptDeclaration('
										  var OneSignal = window.OneSignal || [];
										  OneSignal.push(function() {
											OneSignal.init({
											  appId: "'.$appid.'",
											});
										  });
										');
						}
					}
				}
}
