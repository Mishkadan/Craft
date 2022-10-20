<?php

	/*------------------------------------------------------------------------
	# mod_aixeenawebticker.php - Aixeena Web Ticker (module)
	# ------------------------------------------------------------------------
	# version		4.0.0
	# author    	Ciro Artigot for Aixeena
	# copyright 	Copyright (c) 2018 Top Position All rights reserved.
	# @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Website		http://posicionamientoenbuscadoreswebseo.es/
	
	-------------------------------------------------------------------------
	*/
	// no direct access

	
	defined('_JEXEC') or die;
 
	echo '
	<div class="aixeena_webticker_content aixwebtick-'.$params->get('template','blank').' aixwebtick-'.$params->get('fixedmode','default').'" id="aixwebtick-'.$module->id.'">	
		<div class="tickercontainer">
			<div class="mask">
				<ul id="aixeena-ul-webticker-'.$module->id.'" class="newsticker aixeena-ul-webticker itemdatalayout-'.$params->get('itemdatalayout','vertical').'" data-title="El titulo" style="width:1000000000px">
	';	
	
	if($params->get('customdatalist',0)) {
	 	echo $params->get('customdata',''); 
	 } 
	 else 
	 {
		 if($list) {
			foreach ($list as $item) { 
				include('config.php');	
				include('default-webticker-item.php');	
			}	
		}
	 }

echo '	
				</ul>';
				
if($params->get('moduletitle',0))	include 'webtickertitle.php';
		
			echo '	
				<div class="maks2-left"></div>
				<div class="maks2-right"></div>';
			
			if($params->get('show_more',0)) include 'morelink.php';
			
			echo '
			</div>
		</div>
	</div>
';
?>