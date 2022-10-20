<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

class plgMintToolset extends JPlugin
{
	function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}

	function onToolsGetIcons($context)
	{
		$folders = glob(JPATH_PLUGINS."/mint/toolset/tools/*");

		$tools = array();

		foreach($folders AS $folder)
		{
			if(!is_dir($folder))
			{
				continue;
			}
			$tool = basename($folder);
			$meta = json_decode(file_get_contents($folder.'/meta.json'));

			$tools[$tool] = $meta;
		}

		return $tools;
	}

	public function onToolGetForm($context, $form)
	{
		// return form by name
	}

	function onToolExecute($name, $params)
	{
		// execute your pligin
	}
}