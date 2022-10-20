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
	
	$ordering_items = $params->get('ordering_items','category, title, date, author');
	$order_array = explode(',', $ordering_items);
	
		
	echo '<li style="visibility:hidden;">';
	
	foreach($order_array as $listitem) {
	
		$listitem =  trim($listitem);
		if($listitem=='category') 	echo $category;
		if($listitem=='title') 		echo $title;
		if($listitem=='date') 		echo $thedate;
		if($listitem=='author') 	echo $author;
	}
	
		echo '</li><li style="visibility:hidden;" class="aixwebticker-separator">'.$params->get('item_separator','|').'</li>';
	
?>