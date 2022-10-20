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

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

if(
	substr($app->input->get('task'), 0, 7) !== 'emajax.' &&
	substr($app->input->get('task'), 0, 9) !== 'emcharts.' &&
	substr($app->input->get('task'), 0, 7) !== 'emcron.' &&
	substr($app->input->get('task'), 0, 6) !== 'plans.')
{
	echo '<link rel="stylesheet" href="'.JUri::root(TRUE).'/components/com_emerald/assets/font/css/emerald.css" type="text/css">';
	echo '<link rel="stylesheet" href="'.JUri::root(TRUE).'/components/com_emerald/assets/emerald.css" type="text/css">';
}

include_once __DIR__.'/api.php';

$controller = MControllerBase::getInstance('Emerald');
$controller->execute($app->input->getCmd('task'));
$controller->redirect();

EmeraldHelper::copyright();