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
	
	if($params->get('show_expand_intro',0)) echo '
	<div class="introtext">'.$item->introtext.'</div>';
	if($params->get('show_expand_full',1)) echo '
	<div class="fulltext">'.$item->fulltext.'</div>';
		
?>