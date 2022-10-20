<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class EmeraldImportAkeebasubs extends EmeraldImport
{
	public $result = array();

	public function run($params)
	{
		$plans = JTable::getInstance('EmPlan', 'EmeraldTable');
		$db    = JFactory::getDbo();

		$db->setQuery('SELECT * FROM #__akeebasubs_levels');
		$levels = $db->loadObjectList();

		foreach($levels AS $level)
		{
			$params = array(
				'properties'   => array(
					'price'     => $level->price,
					'days'      => $level->duration,
					'days_type' => 'days',
				),
				'descriptions' => array(
					'description' => $level->description
				)
			);

			if($level->only_once)
			{
				$params['properties']['purchase_limit_user'] = 1;
			}

			$save = array(
				'name'      => $level->title,
				'group_id'  => $this->getGroupId($level->akeebasubs_levelgroup_id),
				'published' => $level->enabled,
				'ctime'     => $level->created_on,
				'access'    => $level->access,
				'params'    => json_encode($params),
				'ordering'  => $level->ordering
			);

			$plans->bind($save);
			$plans->check();
			if($plans->store())
			{
				$this->result['plans']++;
			}

			$this->getSubscritpions($level->akeebasubs_level_id, $plans);

			$plans->reset();
			$plans->id = NULL;
		}

		return $this->result;
	}

	private function getSubscritpions($old_plan_id, $plan)
	{
		$subscriptions = JTable::getInstance('EmSubscription', 'EmeraldTable');

		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM `#__akeebasubs_subscriptions` WHERE `enabled` = 1 AND `akeebasubs_level_id` = " . (int)$old_plan_id);
		$subscrs = $db->loadObjectList();

		foreach($subscrs as $subscr)
		{
			$save = array(
				'user_id'           => $subscr->user_id,
				'plan_id'           => $plan->id,
				'published'         => 1,
				'invoice_num' => 	$this->getInvoiceNumber((int)$subscr->akeebasubs_subscription_id),
				'ctime'             => $subscr->publish_up,
				'extime'            => $subscr->publish_down,
				'created'           => $subscr->created_on,
				'gateway_id'        => $subscr->processor_key,
				'gateway'           => $subscr->processor,
				'price'             => $subscr->net_amount,
				'activated'         => 1,
				'access_limit'      => 0,
				'access_count'      => 0,
				'access_count_mode' => 0
			);

			$subscriptions->bind($save);
			$subscriptions->check();
			if($subscriptions->store())
			{
				$this->result['subscriptions']++;
			}

			EmeraldHelper::activateSubscription($subscriptions);

			$subscriptions->reset();
			$subscriptions->id = NULL;
		}
	}

	private function getInvoiceNumber($id)
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT invoice_no FROM #__akeebasubs_invoices WHERE akeebasubs_subscription_id = {$id}");

		return (int)$db->loadResult();
	}

	private function getGroupId($id)
	{
		static $ids = array();

		settype($id, 'integer');

		if(isset($ids[$id]))
		{
			return $ids[$id];
		}

		$db = JFactory::getDbo();

		$save = array(
			'params'   => json_encode(array('properties' => array('template' => 'default'))),
			'ctime'    => JDate::getInstance()->toSql(),
			'access'   => 1,
			'language' => '*',
			'ordering' => 1
		);

		if($id)
		{
			$db->setQuery('SELECT * FROM #__akeebasubs_levelgroups WHERE akeebasubs_levelgroup_id = ' . $id);
			$group = $db->loadObject();

			$save['name']      = $group->title;
			$save['published'] = $group->enabled;
		}
		else
		{
			$save['name']      = 'Default group';
			$save['published'] = 1;
		}

		$groups = JTable::getInstance('EmGroup', 'EmeraldTable');
		$groups->bind($save);
		$groups->check();
		$groups->store();
		$groups->reorder();

		$this->result['groups']++;

		$ids[$id] = $groups->id;

		return $ids[$id];
	}


	public function check()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SHOW TABLES LIKE "%_akeebasubs_levels"');
		$result = $db->loadResult();
		if(!$result)
		{
			JError::raiseWarning(403, JText::_('EMERALD8_TABLES_NOTEXIST'));

			return FALSE;
		}
		$db->setQuery('SELECT COUNT(*) FROM #__akeebasubs_levels');
		$result = $db->loadResult();
		if(!$result)
		{
			JError::raiseWarning(403, JText::_('EMERALD8_TABLE_PLAN_EMPTY'));

			return FALSE;
		}

		return TRUE;
	}
}
