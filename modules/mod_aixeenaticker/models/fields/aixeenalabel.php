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

class JFormFieldAixeenalabel extends JFormField {

    protected $type = 'Aixeenalabel';

	protected function getInput() {return '<div class="aixseparator '.$this->class.'">&nbsp;</div>';}

	protected   function getLabel(){
		return null;
	}


}
?>