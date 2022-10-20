<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');
jimport('joomla.form.form');

class CobaltBModelTools extends JModelAdmin
{
	var $_tools = array();
	var $_form = array();

	function __construct()
	{
		$this->_id    = JFactory::getApplication()->input->getInt('id');
		$this->option = 'com_cobalt';
		parent::__construct();
	}

	public function getTools()
	{
		if(empty($this->_tools))
		{
			$this->_fetchTools();
		}

		return $this->_tools;
	}

	public function getTool()
	{
		if(empty($this->_tools))
		{
			$this->_fetchTools();
		}

		$tool = $this->_tools[JFactory::getApplication()->input->get('name')];
		$tool->name = JFactory::getApplication()->input->get('name');

		return $tool;
	}

	public function getToolForm()
	{
		$tool = $this->getTool();

		$form_file = JPATH_PLUGINS . '/mint/toolset/tools/' . $tool->name . '/form.xml';
		$form_data = JPATH_PLUGINS . '/mint/toolset/tools/' . $tool->name . '/data.json';

		if(JFile::exists($form_file))
		{
			if(!JFile::exists($form_data))
			{
				$a = '{}';
				JFile::write($form_data, $a);
			}

			$params = new JRegistry();
			$params->loadFile($form_data);

			$form_object = JForm::getInstance('plg_toolset.form', $form_file, array('control' => 'jform'));
			$form        = MEFormHelper::renderFieldset($form_object, $tool->name, $params, NULL, FORM_STYLE_TABLE);

			return $form;
		}

		$form = '';

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('mint');
		$form = $dispatcher->trigger('onToolGetForm', array(
			'com_cobalt.tools',	$form, null, null
		));

		return implode('', array_values($form));
	}

	public function getForm($data = array(), $loadData = TRUE)
	{
		// Get the form.
		$form = $this->loadForm('com_cobalt.config', 'config', array(
			'control'   => 'jform',
			'load_data' => $loadData
		));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}
	private function _fetchTools()
	{
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('mint');
		$results = $dispatcher->trigger('onToolsGetIcons', array('com_cobalt.tools'));
		$total   = array();

		foreach($results As $result)
		{
			$total = array_merge($total, $result);
		}

		$this->_tools = $total;
		settype($this->_tools, 'array');

		foreach($this->_tools AS $key => $tool)
		{
			$this->_tools[$key]->id = substr(md5($key), 0, 5);
		}
	}
}