<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');
class EmeraldController extends JControllerLegacy
{
	function __construct()
	{
		$app = JFactory::getApplication();

		if(!$app->input->get('view'))
		{
			$app->input->set('view', 'plans');
		}

		parent::__construct();
	}

	function display($cachable = FALSE, $urlparams = FALSE)
	{
		$this->_path['view'] = array(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_emerald' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);

		$this->input->set('view', 'about');
		$view = $this->getView('about', 'html', '', array('base_path' => JPATH_ADMINISTRATOR . '/components/com_emerald'));
		$view->setLayout('default');
		$view->display();
	}
}