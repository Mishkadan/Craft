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

	$document->addScript($route_assets.'/js/aixeenamodals.js');
	$close_button = '';
	$modalscript = '';

	if($params->get('show_expand_close',1)) $close_button = '<button class="btn" data-dismiss="modal" aria-hidden="true">'.$params->get('close_text','Close').'</button>';



	foreach ($list as $item) { 



			$modalbuttons = '';
			
			if($params->get('show_expand_readmore',1) || $params->get('show_expand_close',1)) include 'modal_buttons.php';

			$mod_title = '';
			if($params->get('show_expand_header',1)) $mod_title = $item->title;		

			

			echo '

			<div class="premodal-md-effect-'.$params->get('effect','1').' premodal-md-effectoff-'.$params->get('effectoff','1').'">

			<div class="md-modal aixeena-modal-'.$module->id.' md-modal-initial theme-'.$params->get('theme',1).' md-effect-'.$params->get('effect','1').' md-effectoff-'.$params->get('effectoff','1').'" id="aixeena-modal-'.$module->id.'-'.$item->id.'"  data-height="'.$params->get('fixedheight','400').'" data-width="'.$params->get('fixedwidth','600').'"  data-sizemode="'.$params->get('sizemode', 1).'" data-module="'.$module->id.'" style="visibility:hidden">

				<div class="md-content">

					<h3 class="h3content">'.$mod_title.' <span class="aixeena-xbutton pull-right aixeena-glyphicon aixeena-glyphicon-remove md-close"></span></h3>

					<div class="md-content-content" >';
						include('expand-item.php');
			echo '

					</div> '.$modalbuttons.'
				</div>
			</div>
			</div>
			<div class="md-overlay overlay-theme-'.$params->get('theme',1).'" style="display:none;" id="overlay-'.$module->id.'"></div>';		
	}



	if($modalscript) $document->addCustomTag("	

	<script type=".'"text/javascript"'.">

	jQuery(function() {	".$modalscript."});

	</script>");





?>

