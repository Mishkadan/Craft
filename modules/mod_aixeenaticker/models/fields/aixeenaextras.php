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
defined('_JEXEC') or die('Restricted access');


class JFormFieldAixeenaextras extends JFormField {
	
	protected $type = 'Aixeenaextras';

	protected function getInput(){ return '';
	
	;}

	protected function getLabel(){
	
	
	
		JHtml::stylesheet(	'modules/mod_aixeenanews/models/fields/aixeenamodules.css');
		JHtml::stylesheet(	'modules/mod_aixeenanews/assets/aixeenabuttons/'.'aixeenabuttons.css');
		JHtml::script('modules/mod_aixeenanews/models/fields/aixeenamodules.js');

		return '';
	}
}
?>