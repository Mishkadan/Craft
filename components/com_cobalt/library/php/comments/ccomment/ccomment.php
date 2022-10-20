<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/cobaltcomments.php';
jimport('joomla.filesystem.file');

/**
 * Class CobaltCommentsCComment
 *
 * @since  5.0.1
 */
class CobaltCommentsCComment extends CobaltComments
{
	/**
	 * Creates a count number
	 *
	 * @param   object  $type  - the cobalt type
	 * @param   object  $item  - the item object
	 *
	 * @return int|void
	 */
	public function getNum($type, $item)
	{
		if (self::enable())
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*) AS count')->from('#__comment')->where('contentid = ' . $db->quote($item->id) . ' AND component = ' . $db->q('com_cobalt'));
			$db->setQuery($query);

			return $db->loadObject()->count;
		}

		return 0;
	}

	/**
	 * Initialises the component
	 *
	 * @param   object  $type  - the cobalt type
	 * @param   object  $item  - the item object
	 *
	 * @return bool|mixed|string|void
	 */
	public function getComments($type, $item)
	{
		if (self::enable())
		{
			return ccommentHelperUtils::commentInit('com_cobalt', $item);
		}

		return;
	}

	/**
	 * Gets the last comment?
	 *
	 * @param   object  $type  - the cobalt type
	 * @param   object  $item  - the item object
	 *
	 * @return bool|mixed|string|void
	 */
	public function getLastComment($type, $item)
	{
		return;
	}

	/**
	 * Gets an index?
	 *
	 * @param   object  $type  - the cobalt type
	 * @param   object  $item  - the item object
	 *
	 * @return string|void
	 */
	public function getIndex($type, $item)
	{
		if (self::enable())
		{
			$db = JFactory::getDbo();

			$db->setQuery("SELECT comment FROM #__comment WHERE published = 1 AND contentid = {$item->id} AND component = 'com_cobalt");
			$list = $db->loadColumn();

			return implode(', ', $list);
		}
	}

	/**
	 * Checks if the ccomment component is installed on the site
	 *
	 * @return bool
	 */
	private static function enable()
	{
		$path = JPATH_ROOT . '/components/com_comment/helpers/utils.php';

		if (JFile::exists($path))
		{
			JLoader::discover('ccommentHelper', JPATH_SITE . '/components/com_comment/helpers');

			return true;
		}

		return true;
	}
}
