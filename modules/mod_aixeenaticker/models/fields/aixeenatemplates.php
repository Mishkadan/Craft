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


class JFormFieldAixeenatemplates extends JFormField {
	
	protected $type = 'Aixeenatemplates';
	protected function getInput(){return '
	
		<a name="menuaixnews" class="aixancla">&nbsp;&nbsp;</a>
		<ul class="nav nav-tabs nav-stacked">
			<li><a href="#filteroptions">Horizontal</a></li>
			<li><a href="#orderingoptions">Vertical</a></li>
			<li><a href="#templateoptions">List</a></li>
			<li><a href="#description">Buttons</a></li>
			<li><a href="#customparams">Carousel</a></li>
		</ul>
	
	'
	
	;}

	protected function getLabel(){

		return 'Templates params';
	}
}
?>