<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class EmeraldControllerEmSales extends MControllerAdmin
{
	public function &getModel($name = 'EmSale', $prefix = 'EmeraldModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => TRUE));

		return $model;
	}

	protected function postDeleteHook(MModelBase $model, $id = NULL)
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$id  = implode(',', $cid);
		$db  = JFactory::getDbo();

		$db->setQuery("DELETE FROM `#__emerald_url_history` WHERE subscription_id IN({$id})");
		$db->execute();

		$db->setQuery("DELETE FROM `#__emerald_coupons_history` WHERE subscription_id IN({$id})");
		$db->execute();
	}
}
