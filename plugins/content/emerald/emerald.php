<?php
/**
 * Emerald Restriction Plugin by MintJoomla
 * a plugin for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('joomla.event.plugin');
jimport('joomla.filesystem.file');

$emerald_file = JPATH_ROOT . '/components/com_emerald/api.php';
if(JFile::exists($emerald_file))
{
	include_once $emerald_file;
}

class plgContentEmerald extends JPlugin
{
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0)
	{
		$app = JFactory::getApplication();

		if($app->isAdmin())
		{
			return;
		}

		$contexts = array('com_content.article', 'com_content.category', 'com_content.featured');

		if(in_array($context, $contexts))
		{
			$replace = array(
				"/{EMERALD U=[^}]+}/iU",
				"/{EMERALD P=[^}]+}/iU",
				"/{EMERALD SKIP}/iU",
				"/{JCSBOT SKIP}/iU",
				"/{JCSBOT USER=[^}]+}/iU",
				"/{JCSBOT SUBSCRIPTION=[^}]+}/iU",
			);

			$article->text      = preg_replace($replace, "", @$article->text);
			$article->introtext = preg_replace($replace, "", @$article->introtext);
			$article->fulltext  = preg_replace($replace, "", @$article->fulltext);

			if($context == 'com_content.category')
			{

			}
		}
	}
}