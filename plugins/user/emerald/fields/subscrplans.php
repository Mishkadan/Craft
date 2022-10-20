<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('checkbox');

/**
 * Provides input for TOS
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldSubscrplans extends JFormField
{
	protected $type = 'subscrplans';

	public function getInput()
	{
		$plans = (string)@$this->element['plans'];
		$groups = (string)@$this->element['groups'];

		return EmeraldSelectorHelper::render($this->name, $plans, $groups, $this->value, $this->required);

	}
}
