<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.mvc.model.base');
class EmeraldModelEmMua extends MModelList
{
	public function  getSubscrMUA($sid)
	{
		$sql = "SELECT sub.*, us.username, IF(sub.extime > NOW() OR sub.extime = '0000-00-00 00:00:00', 0, 1) AS expired FROM #__emerald_subscriptions as sub
		JOIN #__users us ON sub.user_id = us.id WHERE sub.parent = '$sid'";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}