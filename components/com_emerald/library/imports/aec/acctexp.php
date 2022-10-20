<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.mail.helper');
jimport('joomla.mail.mail');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

require_once(JPATH_ROOT . '/components/com_emerald/api.php');
$app = JFactory::getApplication();

if($app->input->getCmd('task') != 'paypalnotification')
{
	JError::raiseWarning(404, 'Old membership extension is depricated');
	$app->redirect(EmeraldApi::getLink('emlist'));
}

$controller = JControllerLegacy::getInstance('Emerald');
$controller->execute($app->input->getCmd('task'));
$app->redirect();
$controller->redirect();