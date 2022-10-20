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


class JFormFieldAixeenabuttons extends JFormField {
	
	protected $type = 'Aixeenabuttons';

	protected function getInput(){return null;}

	protected function getLabel(){
	
		JHtml::stylesheet(	$this->element['path'].'aixeenabuttons.css');
		
		return null;
	}
}
?>