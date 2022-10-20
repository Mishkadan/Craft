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
	if($params->get('iconlist_more','none')!='none') $icon ='<span class="aixeena-glyphicon '.$params->get('iconlist_more','').'"></span> ';
	echo '<div class="mask2-more">'.$icon.'<a href="'.$params->get('more_link','').'" >'.$params->get('more_text','Read more').'</a></div>';
		
		
	
	?>