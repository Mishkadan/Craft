<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');
jimport('joomla.application.component.controllerform');

class CobaltControllerTools extends JControllerForm
{
	public $model_prefix = 'CobaltBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function save($key = NULL, $urlVar = NULL)
	{

		$name = $this->input->get('name');
		$uri  = JFactory::getURI();

		$params = new JRegistry('');
		if(@$_POST['jform'])
		{
			$params->loadArray(@$_POST['jform']);
		}

		$file = JPATH_PLUGINS . '/mint/toolset/tools/' . $name . '/exec.php';

		if(JFile::exists($file))
		{
			include $file;
		}
		else
		{
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('mint');
			$dispatcher->trigger('onToolExecute', array(
				$name,
				$params
			));
		}


		$form_data = JPATH_PLUGINS . '/mint/toolset/tools/' . $name . '/data.json';
		$content = $params->toString();
		JFile::write($form_data, $content);
		
		$this->setRedirect($uri->toString());
		$this->redirect();
		//parent::display();
	}
}
