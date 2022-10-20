<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . '/components/com_cobalt/api.php';
require_once JPATH_ROOT . '/components/com_community/libraries/core.php';
require_once JPATH_ROOT . '/components/com_community/libraries/karma.php';

class plgMintJomsocial extends JPlugin
{
	public function onKarma($actor, $target, $options, $record)
	{
		$event = $options['type'];
		$section = ItemsStore::getSection($record->section_id);

		if($actor && (int)$section->params->get('events.event.' . $event.'.karma1') != 0)
		{
			$points = $section->params->get('events.event.' . $event.'.karma1');
			$user = CFactory::getUser($actor);
			$points += $user->getKarmaPoint();
			$user->_points = $points;
			$user->save();
		}

		if($event == CEventsHelper::_RECORD_EDITED && $actor == $target)
			return;

		if($target && (int)$section->params->get('events.event.' . $event.'.karma2') != 0)
		{
			$points = $section->params->get('events.event.' . $event.'.karma2');
			$user = CFactory::getUser($target);
			$points += $user->getKarmaPoint();
			$user->_points = $points;
			$user->save();
		}

	}

	public function onActivity($actor, $target, $options, $record)
	{
		$name = $this->_getAppName($record);
		if (!$name)
		{
			return;
		}
		$act = new stdClass();
		$act->cmd = 'cobalt.' . $options['type'];
		$act->actor = $actor;
		$act->target = ($target && $target != $actor ? $target : 0);
		$act->content = '';
		$act->app = $name;
		$act->cid = $options['record_id'];
		$act->params = json_encode($options);
		$act->title = 'EVENT_' . strtoupper($options['type']) . '_PERS';

		CFactory::load('libraries', 'activities');
		CActivityStream::add($act);
	}

	public function onRecordDelete($record)
	{
		$db = JFactory::getDBO();

		$query	= 'DELETE FROM ' . $db->quoteName( '#__community_activities' ) . ' '
		. 'WHERE ' . $db->quoteName( 'app' ) . ' LIKE "cobalt%" '
		. 'AND ' . $db->quoteName( 'cid' ) . '=' . $db->Quote( $record->id ) ;

		$db->setQuery( $query );
		$db->execute();
	}

	private function _getAppName($record)
	{
		static $names = array();

		if(isset($names[$record->section_id]))
		{
			return $names[$record->section_id];
		}
		$db = JFactory::getDbo();
		$db->setQuery('SELECT extension_id, element, params FROM #__extensions WHERE element LIKE "cobalt%" AND enabled=1 AND type="plugin" AND folder="community"');
		$plugins = $db->loadObjectList();
		foreach ($plugins AS $plg)
		{
			$params = new JRegistry($plg->params);
			if($params->get('section_id') == $record->section_id)
			{
				$names[$record->section_id] = $plg->element;
				return $names[$record->section_id];
			}
		}
		return false;
	}

	public function onNotification($user_id, $options)
	{

		/*$array = array(
		'type' => $event,
		'params' => $params,
		'record_id' => $record_id,
		'section_id' => $section_id,
		'cat_id' => $cat_id,
		'comment_id' => $comment_id,
		'field_id' => $field_id,
		'eventer' => $user->id
		);*/
	}
}