<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT.'/components/com_emerald/views/emselector/html.php';
include_once JPATH_ROOT.'/components/com_emerald/models/emselector.php';

class EmeraldSelectorHelper
{
	public static function render($name, $plan_ids = 0, $group_ids = 0, $default = array(), $required = false, $layout = 'default')
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_emerald', JPATH_BASE);

		$session = JFactory::getSession();
		if(empty($default['plan_id']) && $session->get('try_this_plan')) {
			$default['plan_id'] = $session->get('try_this_plan');
		}

		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_ROOT . '/components/com_emerald/views/emselector/tmpl', 1);

		$template = JFactory::getApplication()->getTemplate();
		$paths->insert(JPATH_ROOT . '/templates/'.$template.'/html/com_emerald/emselector', 2);

		$view = new EmeraldViewsEmSelectorHtml(new EmeraldModelsEmSelector(), $paths);
		$view->setLayout($layout);
		$view->name = $name;
		$view->required = $required;
		$view->plans = $plan_ids;
		$view->groups = $group_ids;
		$view->default = $default;

		return $view->render();
	}
}
