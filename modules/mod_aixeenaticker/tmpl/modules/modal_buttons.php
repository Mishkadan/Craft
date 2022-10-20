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

	$icon = '';

	if($params->get('iconlist_exmore','')&&$params->get('iconlist_exmore','')!='none') $icon ='<span class="aixeena-glyphicon '.$params->get('iconlist_exmore','').'"></span> ';
	$modalbuttons .= '<a href="'.$item->link.'" class="aixeenanews-lreadmore  '.$params->get('pull_exmore','pull-right').' aixeena-btn aixeena-'.$params->get('btn_exmore','btn-info').' aixeena-'.$params->get('btnsize_exmore','').'">'.$icon. $params->get('expandmore_text',"Read more").'</a>'; 
	
	
	$icon = '';

	if($params->get('iconlist_exclose','')&&$params->get('iconlist_exclose','')!='none') $icon ='<span class="aixeena-glyphicon '.$params->get('iconlist_exclose','').'"></span> ';
	$modalbuttons .= '<a href="javascript:void(null);" class="md-close  '.$params->get('pull_exclose','pull-right').' aixeena-btn aixeena-'.$params->get('btn_exclose','btn-danger').' aixeena-'.$params->get('btnsize_exclose','').'">'.$icon. $params->get('close_text',"Close").'</a>'; 


	$modalbuttons = '		<div class="md-buttons">'.$modalbuttons.'<div class="clearfix"></div></div>';

		



	



?>