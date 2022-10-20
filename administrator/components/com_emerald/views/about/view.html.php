<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

/**
 * View information about cobalt.
 *
 * @package        Cobalt
 * @subpackage     com_cobalt
 * @since          6.0
 */
class EmeraldViewAbout extends JViewLegacy
{

	public function display($tpl = NULL)
	{
		$this->addToolbar();

		$file = JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'emerald.xml';
		$data = JApplicationHelper::parseXMLInstallFile($file);

		$this->data    = $data;
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('XML_TOOLBAR_TITLE_ABOUT'), 'systeminfo.png');
		JHtmlSidebar::addEntry(
			'<img src="' . JUri::root(TRUE) . '/media/mint/icons/16/information.png" align="absmiddle"> ' .
			JText::_('About'),
			'index.php?option=com_emerald&view=about',
			JFactory::getApplication()->input->getCmd('view', 'about') == 'about'
		);
		JHtmlSidebar::addEntry(
			'<img src="' . JUri::root(TRUE) . '/media/mint/icons/16/gear.png" align="absmiddle"> ' .
			JText::_('Configuration'),
			'index.php?option=com_config&view=component&component=com_emerald'
		);
		JHtmlSidebar::addEntry(
			'<img src="' . JUri::root(TRUE) . '/media/mint/icons/16/lifebuoy.png" align="absmiddle"> ' .
			JText::_('Forum'),
			'http://support.mintjoomla.com/en/'
		);
	}
}
