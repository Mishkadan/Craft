<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class plgSearchEmerald extends JPlugin
{

	function onContentSearchAreas()
	{
		$area_name = JText::_($this->params->get('area_name', 'JEMERALD'));

		$areas = array('emerald' => $area_name);

		return $areas;
	}

	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		$out = array();
		$text = trim($text);

		if($text == '')
		{
			return $out;
		}

		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return $out;
			}
		}

		require_once JPATH_SITE . '/components/com_emerald/api.php';

		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$limit = $this->params->def('search_limit', 50);

		$query = $db->getQuery(TRUE);
		$query->select('p.*, g.name AS section');
		$query->from('#__emerald_plans AS p');
		$query->where('p.published = 1');
		$query->where('g.published = 1');
		$query->where('p.access IN (' . $groups . ')');
		$query->where('g.access IN (' . $groups . ')');
		$query->leftJoin('#__emerald_plans_groups AS g ON g.id = p.group_id');

		$search = $db->quote($db->escape($text));

		switch ($phrase)
		{
			case 'exact':
				$query->where("p.name LIKE '%{$text}%'");
				break;

			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();

				foreach ($words as $word)
				{
					$word = $db->quote('%' . $db->escape($word, true) . '%', false);
					$wheres2 = array();
					$wheres2[] = 'p.name LIKE ' . $word;
					$wheres[] = implode(' OR ', $wheres2);
				}

				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				$query->where($where);
				break;
		}

		switch($ordering)
		{
			case 'oldest':
				$order = 'p.ctime ASC';
			break;

			case 'alpha':
				$order = 'p.name ASC';
			break;

			case 'newest':
			default:
				$order = 'p.ctime DESC';
		}
		$query->order($order);

		$db->setQuery($query, 0, $limit);
		$result = $db->loadObjectList();
		settype($result, 'array');

		foreach($result as $key => $plan)
		{
			$params = new JRegistry($plan->params);
			$out[$key] = new stdClass();
			$out[$key]->title = $plan->name;
			$out[$key]->text = EmeraldHelper::_($params->get('descriptions.description'));
			$out[$key]->created = $plan->ctime;
			$out[$key]->href = EmeraldApi::getLink('list', FALSE, $plan->id);
  			$out[$key]->section = JText::_($plan->section);
			$out[$key]->browsernav = 0;
		}

		return $out;
	}
}
