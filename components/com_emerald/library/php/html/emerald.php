<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;


abstract class JHtmlEmerald
{
	/**
	 * A cached array of the groups
	 *
	 * @var    array
	 */
	protected static $groups = null;
	protected static $plans = null;


	public static function groups()
	{
		if (empty(self::$groups))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('g.id AS value, g.name AS text');
			$query->from($db->quoteName('#__emerald_plans_groups') . ' AS g');
			$query->order('g.name ASC');

			$db->setQuery($query);
			self::$groups = $db->loadObjectList();
		}

		return self::$groups;
	}

	public static function plans()
	{
		if (empty(self::$groups))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('p.id AS value, p.name AS text');
			$query->from($db->quoteName('#__emerald_plans') . ' AS p');
			$query->order('p.name ASC');

			$db->setQuery($query);
			self::$groups = $db->loadObjectList();
		}

		return self::$groups;
	}

}