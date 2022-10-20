<?php
/**
 * Emerald Granter Plugin by MintJoomla
 * a plugin for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');

class plghikashopemeraldInstallerScript
{
	function install($parent)
	{
		$db = JFactory::getDBO();

		$query = "CREATE TABLE IF NOT EXISTS `#__emerald_hikashop_orders` (
			  `order_id` int(10) NOT NULL DEFAULT '0',
			  `ctime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->execute();

		return TRUE;
	}

	public function uninstall($parent)
	{
	}
}