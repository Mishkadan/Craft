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
	
		
	$icon = '';
	if($params->get('iconlist_webtickertitle','none')!='none') $icon ='<span class="aixeena-glyphicon '.$params->get('iconlist_webtickertitle','').'"></span> ';
	
	if($params->get('webtickertitle_link','')) echo '<div class="maks2-title">'.$icon.'<a href="'.$params->get('webtickertitle_link','').'" >'.$params->get('webtickertitle',$module->title).'</a></div>';
	else echo '<div class="maks2-title">'.$icon.$params->get('webtickertitle',$module->title).'</div>';
	

	
	?>