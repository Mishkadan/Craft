<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();


class EmeraldFieldsHelper
{
	static public function getSubscriptionFields($subscription)
	{
		$defaults = json_decode($subscription->fields, TRUE);

		if(count($defaults) == 0)
		{
			return array();
		}

		$plan = EmeraldApi::getPlan($subscription->plan_id, TRUE);

		if(!$plan->params->get('properties.fields'))
		{
			return array();
		}

		$model  = $model = MModelBase::getInstance('EmPayment', 'EmeraldModel');
		$fields = $model->getAddonFields($plan, $defaults);

		return $fields;
	}

	public static function load_lang($field)
	{
		$lang = JFactory::getLanguage();
		$tag  = $lang->getTag();
		if($tag != 'en-GB')
		{
			if(!JFile::exists(JPATH_BASE . "/language/{$tag}/{$tag}.com_emerald_field_{$field}.ini"))
			{
				$tag == 'en-GB';
			}
		}

		$lang->load('com_emerald_field_' . $field, JPATH_ROOT, $tag, TRUE);
	}
}
