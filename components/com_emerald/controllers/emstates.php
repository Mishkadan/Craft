<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class EmeraldControllerEmStates extends MControllerAdmin
{
	public function &getModel($name = 'EmState', $prefix = 'EmeraldModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => TRUE));

		return $model;
	}
}
