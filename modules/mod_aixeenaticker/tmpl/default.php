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
	
	$app             = JFactory::getApplication();
	$doc             = JFactory::getDocument();
		
	JHtml::addIncludePath(JURI::base(true). '/components/com_content/helpers');
	
	include('modules/params.php'); // params module
	
	// CSS
	if($CSS) $document->addStyleSheet($route_assets.'/css/aixeena-ticker.css');
	if($CSS) $document->addStyleSheet($route_assets.'/css/aixwebticker-'.$template.'.css');
	
	if($CSS && $addCSS) $document->addCustomTag("
<style type=".'"text/css"'.'>'.$addCSS.'</style>');


	if($js) {
		$document->addScript($route_assets.'/js/webticker.'.$doc->direction.'.js');	
		$document->addCustomTag("	
		<script type=".'"text/javascript"'.">		
		jQuery(window).load(function(){
			jQuery('#aixeena-ul-webticker-".$module->id."').liScroll({travelocity: ".$params->get('travelocity','0.08').", begin:".$params->get('begin',1).", mode:".$params->get('scrollmode',1)."});
		});		
		</script>");
	}
		
	echo '<div id="aixwebtickerall-'.$module->id.'" class="dir-'.$doc->direction.'">';
	include('layouts/default-webticker.php');
	echo '</div>';
	
	//echo '<div style="padding:5px; text-align: right; font-size:12px;">Desarrollado por el <a href="https://iddigitalschool.com/master-marketing-digital-comunicacion-y-redes-sociales-presencial/" title="Master Marketing Digital">Master Marketing Digital</a> Top Position'

?>