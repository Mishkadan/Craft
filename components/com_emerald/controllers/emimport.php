<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.mvc.controller.form');
include_once JPATH_ROOT.'/components/com_emerald/helpers/imports.php';

class EmeraldControllerEmImport extends MControllerForm
{
	public function run()
	{
		$params = $this->input->get('params', array(), 'array');
		$params = new JRegistry($params);

		$type = $this->input->get('type');

		$obj = EmeraldImportsHelper::createImportObject($type);

		$result = $obj->run($params);

		JError::raiseNotice(200, JText::sprintf('IMPORT_SUCCESS', $result['plans'], $result['subscriptions']));

		$this->setRedirect(JRoute::_('index.php?option=com_emerald&view=imports'), JText::_('IMPORT_COMPLETE'));
	}
}



