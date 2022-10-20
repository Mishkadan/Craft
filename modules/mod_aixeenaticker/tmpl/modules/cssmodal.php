<?php

	/*------------------------------------------------------------------------
	# Aixeena Ticker (module)
	# ------------------------------------------------------------------------
	# version		4.0.0
	# author    	Top Position
	# copyright 	Copyright (c) 2018 Top Position All rights reserved.
	# @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Website		http://posicionamientoenbuscadoreswebseo.es/
	-------------------------------------------------------------------------
	*/
	// no direct access
	defined('_JEXEC') or die;
	 
	//include module common CSS
	$document->addStyleSheet($route_assets.'/aixeenamodals/css/style.css');
	
	$IE6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) ? true : false;
    $IE7 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') ) ? true : false;
    $IE8 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8') ) ? true : false;

	if (($IE6 == 1) || ($IE7 == 1) || ($IE8 == 1)) $document->addStyleSheet($route_assets.'/aixeenamodals/css/styleIE08.css');
	
	// include the theme
	if($params->get('theme',1)) $document->addStyleSheet($route_assets.'/aixeenamodals/css/themes/theme'.$params->get('theme',1).'.css');
	
	//include individual effect css
	if($params->get('effect',1)) $document->addStyleSheet($route_assets.'/aixeenamodals/css/modalcsseffects/modaleffect'.$params->get('effect',1).'.css');
	if($params->get('effectoff',1)) $document->addStyleSheet($route_assets.'/aixeenamodals/css/modalcsseffects/modaleffectoff'.$params->get('effectoff',1).'.css');
		
	$addCSSmodal = '';
	$addCSSo = '';
	$modal_content = '';
	$modal_content_h3 = '';
	$modal_content_buttons = '';	
	
	//Custom content
	if($params->get('background','')) $modal_content .= '
		background-color: '.$params->get('background','').';';
	if($params->get('color','')) $modal_content .= '
		color:'.$params->get('color','').';';
	if($modal_content) $modal_content = '
	.aixeena-modal-'.$module->id.' .md-content {'.$modal_content.'
	}';
	
	$modal_content .= '
	.aixeena-modal-'.$module->id.' .md-content-content a{
		text-decoration: '.$params->get('link_decoration','none').';';
	
	if($params->get('link_color','')) $modal_content .= '
		color:'.$params->get('link_color','').';';
	$modal_content .= '
	}';
	
	$modal_content .= '
	.aixeena-modal-'.$module->id.' .md-content-content a:hover{
		text-decoration: '.$params->get('link_hover_decoration','underline').';';
	if($params->get('link_color_hover','')) $modal_content .= '
		color:'.$params->get('link_color_hover','').';';
	$modal_content .= '
	}';
	
	//Custom h3
	if($params->get('backgroundheader','')) $modal_content_h3 .= '
		background-color: '.$params->get('backgroundheader','').';';
	if($params->get('colorheader','')) $modal_content_h3 .= '
		color:'.$params->get('colorheader','').';';
	if($modal_content_h3) $modal_content_h3 = '
	.aixeena-modal-'.$module->id.' .md-content h3 {'.$modal_content_h3.'
	}';
	
	//Custom buttons
	if($params->get('backgroundbuttons','')) $modal_content_buttons .= '
		background-color: '.$params->get('backgroundbuttons','').';';
	if($params->get('colorbuttons','')) $modal_content_buttons .= '
		color:'.$params->get('colorbuttons','').';';
	if($modal_content_buttons) $modal_content_buttons = '
	.aixeena-modal-'.$module->id.' .md-buttons {'.$modal_content_buttons.'
	}';

	$modalbackgroundimg 		= $params->get('modalbackgroundimg','');
	$modalbackgroundimgmode 	= $params->get('modalbackgroundimgmode',1);
	
	if($modalbackgroundimg&&$modalbackgroundimgmode==2) $addCSSmodal =  '
	.aixeena-modal-'.$module->id.' .md-content{
		background-image: url("'.JURI::base(true).'/'.$modalbackgroundimg.'");
		background-repeat:repeat;
	}
	';
	if($modalbackgroundimg&&$modalbackgroundimgmode==1) $addCSSmodal =  '
	.aixeena-modal-'.$module->id.' .md-content{
		background-image: url("'.JURI::base(true).'/'.$modalbackgroundimg.'");
		background-repeat:no-repeat;
		background-position:center center;
		  -webkit-background-size: cover;
		  -moz-background-size: cover;
		  -o-background-size: cover;
		  background-size: cover;
	}
	';
		
	// overlay custom design 
	
	$overlayCSS = '';
	
	$overlay 		= $params->get('overlay','#000000');
	$overlayop 		= $params->get('overlayop','0.8');
	$backgroundimg 	= $params->get('backgroundimg','');
	$backgroundimgmode 	= $params->get('backgroundimgmode',1);
	
	if($backgroundimg&&$backgroundimgmode==2) $addCSSo =  '
		background-image:url("'.JURI::base(true).'/'.$backgroundimg.'");
		
		
		background-repeat:repeat;';
	if($backgroundimg&&$backgroundimgmode==1) $addCSSo =  '
		background-image: url("'.JURI::base(true).'/'.$backgroundimg.'");
		background-size:100% 100%;
		background-repeat:no-repeat;	';
	
	if($params->get('customoverlaydesign',1)) $overlayCSS = '
		#overlay-'.$module->id.' {
			background-color: '.$overlay.';'.$addCSSo.'
			opacity: '.$overlayop.';
		}';
	
	$modalCSS = '';
	if($params->get('customdesign',1)) $modalCSS = $addCSSmodal.$modal_content.$modal_content_h3.$modal_content_buttons;
	
	
	$modalCSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $modalCSS);
    /* remove tabs, spaces, newlines, etc. */
    $modalCSS = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $modalCSS);
	$overlayCSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $overlayCSS);	
    /* remove tabs, spaces, newlines, etc. */
    $overlayCSS = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $overlayCSS);	

	if($params->get('includecss',1) && ($params->get('customdesign',1) || $params->get('customoverlaydesign',1)))  $document->addCustomTag("
<style type=".'"text/css"'.'>'.$modalCSS.$overlayCSS.'
</style>');

	
?>