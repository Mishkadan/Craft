<?php
/**
 *
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 *
 */
defined('_JEXEC') or die('Restricted access');

class EmeraldViewEmAnalytics extends MViewBase
{
	function  display($tpl = null)
	{
		JFactory::getDocument()->addScript(JUri::root(TRUE).'/components/com_emerald/library/js/hightcharts.js');
		JFactory::getDocument()->addScript(JUri::root(TRUE).'/components/com_emerald/library/js/options.js');

		$this->menu = Mint::loadLayout('links', JPATH_COMPONENT .'/layouts');

		return parent::display($tpl);
	}
}