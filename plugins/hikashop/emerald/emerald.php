<?php
/**
 * Emerald Granter Plugin by MintJoomla
 * a plugin for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

include_once JPATH_ROOT . '/components/com_emerald/api.php';
include_once JPATH_ROOT . '/administrator/components/com_hikashop/helpers/helper.php';

class plgHikashopEmerald extends JPlugin
{
	function onAfterOrderCreate(&$order, &$send_email)
	{
		return $this->onAfterOrderUpdate($order, $send_email);
	}

	function onAfterOrderUpdate(&$order, &$send_email)
	{
		if(empty($order->order_id))
		{
			return;
		}

		$db = JFactory::getDbo();
		$db->setQuery("SELECT order_id FROM #__emerald_hikashop_orders WHERE order_id = " . $order->order_id);
		$processed = $db->loadResult();

		if(!$processed)
		{
			$db->setQuery("SELECT id FROM #__emerald_subscriptions WHERE gateway = 'HikaShop' AND gateway_id LIKE '{$order->order_id}-%'");
			$processed = $db->loadResult();
		}

		if($processed)
		{
			if($order->order_status == 'cancelled')
			{
				$db->setQuery("UPDATE #__emerald_subscriptions SET published = 0 WHERE gateway_id LIKE '{$order->order_id}-%' AND gateway = 'HikaShop'");
				$db->execute();
			}
			if($order->order_status == 'confirmed')
			{
				$db->setQuery("UPDATE #__emerald_subscriptions SET published = 1 WHERE gateway_id LIKE '{$order->order_id}-%' AND gateway = 'HikaShop'");
				$db->execute();
			}

			return;
		}

		$class      = hikashop_get('class.order');
		$full_order = $class->loadFullOrder($order->order_id, FALSE, FALSE);

		if($full_order->order_status != 'confirmed')
		{
			return;
		}

		$db->setQuery(sprintf("INSERT INTO #__emerald_hikashop_orders (order_id, ctime) VALUES (%d, '%s')",
			$order->order_id, JDate::getInstance()->toSql()));
		$db->execute();

		$db->setQuery("SELECT * FROM #__emerald_plans_actions WHERE `type` = 'hikashop'");

		if(!$actions = $db->loadObjectList())
		{
			return;
		}

		$assosiations_cat = $assosiations = $plans = $ids = array();


		foreach($actions AS $action)
		{
			$params = json_decode($action->action, TRUE);

			if(!empty($params['products']))
			{
				foreach($params['products'] AS $product_id)
				{
					$assosiations[$product_id][] = $action->plan_id;
				}
			}
			if(!empty($params['category']))
			{
				foreach($params['category'] AS $cat_id)
				{
					$assosiations_cat[$cat_id][] = $action->plan_id;
				}
			}

		}

		if(empty($assosiations) && empty($assosiations_cat))
		{
			return;
		}

		foreach($full_order->products as $product)
		{
			if(!empty($assosiations[$product->product_id]))
			{
				$plans = $assosiations[$product->product_id];
			}

			$cats           = $this->_getProductCategories($product->product_id);
			$intersect_cats = array_intersect($cats, array_keys($assosiations_cat));


			if(!empty($intersect_cats))
			{
				foreach($intersect_cats AS $category_id)
				{
					$plans = array_merge($plans, $assosiations_cat[$category_id]);
				}
			}

			$plans = array_values($plans);
			$plans = array_unique($plans);

			if(empty($plans))
			{
				continue;
			}


			foreach($plans AS $plan_id)
			{
				$publish = 1;
				if($this->params->get('auto', 1) == 0 && (@$full_order->order_subtotal + @$full_order->order_full_price) == 0)
				{
					$publish = 0;
				}
				$ids[] = EmeraldApi::addSubscription($full_order->customer->id, $plan_id, $publish, 'HikaShop', 0, $full_order->order_id . '-' . $product->product_id);
			}
			if(!empty($ids))
			{
				$db->setQuery("UPDATE #__emerald_hikashop_orders SET subscriptions = '" . json_encode($ids) . "' WHERE order_id = " . $order->order_id);
				$db->execute();
			}
		}
	}

	function onUserAccountDisplay(& $buttons)
	{
		$buttons[] = array(
			'link'        => EmeraldApi::getLink('history'),
			'level'       => 0,
			'image'       => 'subscription',
			'text'        => JText::_('EMR_TITLEHISTROOY'),
			'description' => JText::_('EMR_TITLEHISTROOY')
		);

		return TRUE;
	}

	function onAfterOrderDelete($elements)
	{
		if(!$elements)
		{
			return;
		}

		if(!is_array($elements))
		{
			$elements = array($elements);
		}

		$id = implode(',', $elements);
		$db = JFactory::getDbo();

		$db->setQuery("DELETE FROM `#__emerald_hikashop_orders` WHERE order_id IN({$id})");
		$db->execute();

		return TRUE;
	}

	private function _getProductCategories($product_id)
	{
		$db = JFactory::getDbo();

		$db->setQuery("SELECT category_id FROM #__hikashop_product_category WHERE product_id = " . $product_id);
		$cats = $db->loadColumn();

		return $cats;
	}
}
