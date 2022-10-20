<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filter.filteroutput');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.date');

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'html');

$controller	= JControllerLegacy::getInstance('Emerald');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
<div style="clear:both;"></div>
<p>
<center class="small">&copy; 2012 <a href="http://www.mintjoomla.com">MintJoomla</a></center>
</p>