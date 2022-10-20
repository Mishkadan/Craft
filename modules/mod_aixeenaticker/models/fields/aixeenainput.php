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

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldAixeenainput extends JFormField {

    protected $type = 'Aixeenainput';


	 public function getInput() {
	 
		return '<input size="'.$this->maxlength.'" type="text" name="'.$this->name.'" id="'.$this->id.'" style="width:'.$this->size.'px;"  class="'.$this->class.'"> <span class="sufijo">xxxx'.$this->size.'</span>';
	
	}




}
?>