<?php
/**
 * Emerald by MintJoomla
* a component for Joomla! 3.0 CMS (http://www.joomla.org)
* Author Website: http://www.mintjoomla.com/
* @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die();

class EmeraldModelEmTax extends MModelAdmin
{

	public function getTable($type = 'EmTax', $prefix = 'EmeraldTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_emerald.tax', 'tax', array(
			'control' => 'jform',
			'load_data' => $loadData
		));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_emerald.edit.emtax.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function canDelete($record)
	{
		return true;
	}

	protected function canEditState($record)
	{
		return true;
	}
}