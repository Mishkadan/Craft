<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();


class jc_com_cobalt extends JCommentsPlugin
{
	function getObjectInfo($id, $language = NULL)
	{
		$info = new JCommentsObjectInfo();

		$helper = JPATH_ROOT . '/components/com_cobalt/library/php/helpers/helper.php';

		if(is_file($helper)) {
			require_once($helper);

			$db = JFactory::getDbo();
			$query = $db->getQuery(TRUE);

			$query->select('*');
			$query->from('#__js_res_record');
			$query->where('id = ' . (int)$id);
			$db->setQuery($query);

			$record = $db->loadObject();

			if(!empty($record)) {
				$info->title = $record->title;
				$info->access = $record->access;
				$info->userid = $record->user_id;
				$info->link = JRoute::_(Url::record($record));
			}
		}

		return $info;
	}
}