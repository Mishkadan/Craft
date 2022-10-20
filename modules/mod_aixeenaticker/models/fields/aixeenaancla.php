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

class JFormFieldAixeenaancla extends JFormField {

    protected $type = 'Aixeenaancla';

	protected function getInput() {return '
	
		<a name="'.$this->element['ancla'].'"  class="aixancla">&nbsp;&nbsp;</a>
		<a href="#menuaixnews" title="Go up" class="goup"><span class="aixeena-glyphicon aixeena-glyphicon-circle-arrow-up"></span></a> ';}

	protected   function getLabel(){
		return null;
	}


}
?>