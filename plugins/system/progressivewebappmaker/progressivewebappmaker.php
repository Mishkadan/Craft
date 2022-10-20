<?php
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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
class PlgSystemProgressivewebappmaker extends JPlugin
{
    public function __construct($subject, $config)
    {
        parent::__construct($subject, $config);
        $lang  =JFactory::getLanguage();
        $lang->load('plg_system_progressivewebappmaker', JPATH_ADMINISTRATOR);
		//var_dump(JPATH_ADMINISTRATOR);
    }
    public function onExtensionAfterSave($context, $table)
    {
        $app = JFactory::getApplication();
        $chkPlugin=$table->element;

        if ($chkPlugin == 'progressivewebappmaker') {
            jimport('joomla.filesystem.file');
            $params = $this->params;
            $filePath = JPATH_PLUGINS. '/system/progressivewebappmaker/manifest.json';
            $fileString     = new StdClass();

            $fileString->name               = $params->get('name');
            $fileString->short_name         = $params->get('short_name');
            $customstart = $params->get('customstart');
				if(!$customstart){
					$fileString->start_url          = JURI::root();
				} else {
					$customstarturl = $params->get('customstarturl');
					$fileString->start_url          = $customstarturl;	
				}
            $fileString->theme_color        = $params->get('theme_color');
            $fileString->background_color   = $params->get('background_color');


            $appIcons = array();
            if (!empty($params->get('image_192'))) {
                $image_192Type  = 'image/' . explode('.', basename($params->get('image_192')))[1];
                $image_192URL   = JURI::root().$params->get('image_192');
                $appIcons[]     = array(
                    'src'   => $image_192URL,
                    'type'  => $image_192Type,
                    'sizes' => '192x192'
                );
            }
            if (!empty($params->get('image_512'))) {
                $image_512Type  = 'image/' . explode('.', basename($params->get('image_512')))[1];
                $image_512URL   = JURI::root().$params->get('image_512');
                $appIcons[]     = array(
                    'src'   => $image_512URL,
                    'type'  => $image_512Type,
                    'sizes' => '512x512'
                );
            }

            $fileString->display       = 'fullscreen'/*$params->get('display','standalone')*/;
            $fileString->icons         = $appIcons;
            //for push notifications : these settings are also saved in web push notification extension but if both(pwa and push notifictions) the applications are used then there are two manifest from which first linked will be used.
            //We need to link pwa manifest always before push notification :  this may be achived by reordering the plugins in Joomla.
            //TODO : Implement Better way to manage both the maniest files.
            $fileString->gcm_sender_id = $this->params->get('fcm_sender_id','');

            $fileString = json_encode($fileString);
            $result     = JFile::write($filePath, $fileString);
            if ($result) {
                chmod($filePath, 0644);
                return true;
            } else {
                return false;
            }
        }
    }
    public function onBeforeCompileHead()
    {
        $app = JFactory::getApplication();
        $document   = JFactory::getDocument();
        if ($app->issite()) {
            //add manifest
            $siteName       = $this->params->get('name');
            $themeColor     = $this->params->get('theme_color');
            $document->addHeadLink("plugins/system/progressivewebappmaker/manifest.json", 'manifest', 'rel');
            $customTag  = '<meta name="theme-color" content="'.$themeColor.'">';
            $document->addCustomTag($customTag);
            
			// appletouchadd
			$appletouchadd       = $this->params->get('appletouchadd');
			if($appletouchadd)
			{
			$appletouch57       = $this->params->get('touch_57');
			$customAppTag57  = '<link rel="apple-touch-icon" sizes="57x57" href="'.JURI::root().''.$appletouch57.'">';	
			$document->addCustomTag($customAppTag57);
			$appletouch180       = $this->params->get('touch_180');
			$customAppTag180  = '<link rel="apple-touch-icon" sizes="180x180" href="'.JURI::root().''.$appletouch180.'">';	
			$document->addCustomTag($customAppTag180);
			}
			$document->addScriptDeclaration(
                '
                    var cacheName     = "'.$siteName.'";
                    var filesToCache  = [
                        "/",
                        "/index.html"
                    ];
                    var pwamakerSiteUrl     = "'.JURI::root().'";
                '
            );
			$pwa_maker = '/plugins/system/progressivewebappmaker/assets/js/pwa_maker.js';
            $document->addScript(JURI::root() . $pwa_maker.'?'.filemtime(JPATH_BASE. $pwa_maker));

            /*** PWA BANNERS ***/
            $pwa_banner = '/plugins/system/progressivewebappmaker/assets/js/pwa_banners.min.js';
            $document->addScript(JURI::root() . $pwa_banner.'?'.filemtime(JPATH_BASE. $pwa_banner));
            $browser = JBrowser::getInstance();
            $browserType = $browser->getBrowser();
            $_vars = '';
            if ($browserType == 'safari') {
                $pwapopup = '<div class="ios-prompt">'
                                .'<small>&times;</small>'
                                .'<span>Для установки приложения нажмите</span>'
                                    .'<img src="images/pwa_icons/banner/menu.png"><br>'
                                .'<span>а затем <b>Добавить на главный экран</b></span>'
                            .'</div>';
                $_vars .= "var safari = 'true'; ";
            } else {
                $pwapopup =  '<div class="block__install" id="BlockInstall">'
                                .'<div class="inner">'
                                   .' <div class="close" id="BlockInstallClose">'
                                       .' <span>+</span>'
                                    .'</div>'
                                    .'<div class="logo">'
                                        .'<img src="/images/L1.jpg" />'
                                    .'</div>'
                                    .'<div class="name">'
                                        .'<span class="title">CRAFT</span>'
                                        .'<span class="description">Приложение на рабочий экран</span>'
                                    .'</div>'
                                    .'<div class="cta">'
                                        .'<button id="BlockInstallButton" class="btn btn-outline">Установить</button>'
                                    .'</div>'
                                .'</div>'
                            .'</div>';
                $_vars .= "var safari = 'false'; ";
            }
            $_vars .= "var pwapopup = '" . $pwapopup. "'; ";
            $document->addScriptDeclaration($_vars);
            $style = '#pwamaker_pwa-loader-overlay {
                display:none;
                position:fixed;
                top:0px;
                left:0px;
                bottom:0px;
                right:0px;
                z-index:999999;
                background:rgba(54, 25, 25, 0.2) none repeat scroll 0 0;
                text-align: center;
            }
            #pwamaker_pwa-loader{
                top:50%;
                position:relative;
                color:#fff;
                font-size: 18px;
                z-index: 9999999;
            }
            #pwamaker_pwa_offline_bar {
                display:none;
				background-color: red;
				position: fixed;
				bottom: 0;
				left: 0;
				right: 0;
				max-height: 60px;
				text-align: center;
				z-index: 9999999;
				font-size: 20px;
				font-weight: bold;
				color: #fff;
				padding: 30px 10px;
				opacity: 0.6;
            }';
            if ($this->params->get('pwaloader_enable') && ($this->params->get('pwaloader_type') == 'default' || ($this->params->get('pwaloader_type') == 'custom' && !$this->params->get('wkpwaloader_custom')) )) {
                $loaderColor = $this->params->get('wkpwaloader_color','#3da9f2');
                $style .=
                    '
                    .pwamaker-spinner{
                        width:72px;
                        height:72px;
                        display:inline-block;
                        box-sizing:border-box;
                        position:relative
                    }
                    .pwamaker-skeleton{
                        border-radius:50%;
                        border-top:solid 6px '.$loaderColor.';
                        border-right:solid 6px transparent;
                        border-bottom:solid 6px transparent;
                        border-left:solid 6px transparent;
                        animation:pwamaker-skeleton-animate 1s linear infinite
                    }
                    .pwamaker-skeleton:before{
                        border-radius:50%;
                        content:" ";
                        width:72px;
                        height:72px;
                        display:inline-block;
                        box-sizing:border-box;
                        border-top:solid 6px transparent;
                        border-right:solid 6px transparent;
                        border-bottom:solid 6px transparent;
                        border-left:solid 6px '.$loaderColor.';
                        position:absolute;
                        top:-6px;
                        left:-6px;
                        transform:rotateZ(-30deg)
                    }
                    .pwamaker-skeleton:after{
                        border-radius:50%;
                        content:" ";
                        width:72px;
                        height:72px;
                        display:inline-block;
                        box-sizing:border-box;
                        border-top:solid 6px transparent;
                        border-right:solid 6px '.$loaderColor.';
                        border-bottom:solid 6px transparent;
                        border-left:solid 6px transparent;
                        position:absolute;
                        top:-6px;
                        right:-6px;
                        transform:rotateZ(30deg)
                    }
                    @keyframes pwamaker-skeleton-animate{
                        0%{
                            transform:rotate(0);
                            opacity:1
                        }
                        50%{
                            opacity:.8
                        }
                        100%{
                            transform:rotate(360deg);
                            opacity:1
                        }
                    }

                ';
            }

            $document->addStyleDeclaration($style);
            //check for service worker and move to root if necessary
            $serviceWorkerFile  = JPATH_PLUGINS.'/system/progressivewebappmaker/assets/js/pwa_maker_service_worker.js';
            if (!JFile::exists(JPATH_SITE . '/pwa_maker_service_worker.js')) {
                JFile::copy($serviceWorkerFile, JPATH_SITE.'/pwa_maker_service_worker.js');
            }
        }
    }
    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        if ($app->issite()) {
            $dataToAppend   = '<div id="pwamaker_pwa_offline_bar">'.JText::_('PLG_SYSTEM_PROGRESSIVEWEBAPPMAKER_OFFLINE_SITE_TEXT').'</div>';
            if ($this->params->get('pwaloader_enable', 1)) {
                $dataToAppend .='<div id="pwamaker_pwa-loader-overlay"><div id="pwamaker_pwa-loader">';
                if ($this->params->get('pwaloader_type', 'default') == 'custom' && $this->params->get('wkpwaloader_custom', '')) {
                    $dataToAppend.='<div class="pwamaker_pwa-loader-custom"><img src="'.$this->params->get('wkpwaloader_custom','').'"></div>';
                } else {
                    $dataToAppend.='<div class="pwamaker-spinner pwamaker-skeleton"></div>';
                }
                $dataToAppend.='</div></div>';
            }
            $client     = $client = new JApplicationWebClient();
            $browser    = $client->browser;
            $modalBody  = JResponse::getBody();
            $body       = preg_replace('/<\/body>/', $dataToAppend."</body>", $modalBody);
            JResponse::setBody($body);
        }
    }
}
