<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controllerbase');

class CobaltControllerCustom extends JControllerLegacy
{



	public function expirealerts()
	{
		$db = JFactory::getDbo();

		$db->setQuery("SELECT * FROM `#__js_res_record` WHERE exalert = 0 AND extime < NOW()");
		$records = $db->loadObjectList();

		foreach($records AS $record)
		{
			$type = ItemsStore::getType($record->type_id);

			$sql = "UPDATE #__js_res_record SET exalert = 1";
			if($type->params->get('properties.item_expire_access'))
			{
				$sql .= ", access = " . $type->params->get('properties.item_expire_access');
			}
			$sql .= " WHERE id = " . $record->id;

			$db->setQuery($sql);
			$db->execute();

			CEventsHelper::notify('record', CEventsHelper::_RECORD_EXPIRED, $record->id, $record->section_id, 0, 0, 0, $record, 2);
		}
	}
}
