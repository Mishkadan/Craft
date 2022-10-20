<?php
/*
* @package 		com_pushnotification - Push Notification
* @version		1.0.2
* @created		Jan 2020
* @author		ExtensionCoder.com
* @email		developer@extensioncoder.com
* @website		https://www.extensioncoder.com
* @support		https://www.extensioncoder.com/support.html
* @copyright	Copyright (C) 2019-2020 ExtensionCoder. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_pushnotification'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Pushnotification', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('PushnotificationHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'pushnotification.php');

$controller = BaseController::getInstance('Pushnotification');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
