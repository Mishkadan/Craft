<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class EmeraldTableEmCoupon extends JTable
{

	function __construct(&$db)
	{
		parent::__construct('#__emerald_coupons', 'id', $db);
	}

	function check()
	{
		if(!$this->id)
		{
			$db  = JFactory::getDBO();
			$sql = "SELECT id FROM #__emerald_coupons WHERE `value` = '{$this->value}'";
			$db->setQuery($sql);
			if($db->loadResult())
			{
				$this->setError(JText::_('E_COUPON_EXIST'));

				return FALSE;
			}
		}

		if(!empty($this->plan_ids))
		{
			$this->plan_ids = json_encode($this->plan_ids);
		}

		return parent::check();
	}

}