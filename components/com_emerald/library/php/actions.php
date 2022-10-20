<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class EmeraldAction extends JObject
{
	/**
	 * @var \Joomla\Registry\Registry
	 */
	public $params;

	/**
	 * @var JUser
	 */
	public $user;

	public function __construct($data)
	{
		$this->params  = new JRegistry($data->action);
		$this->plan_id = $data->plan_id;
		$this->user = JFactory::getUser();
	}

	/**
	 * On success activation by admin or payment gateway
	 * @param $subscription
	 */
	public function onSuccess($subscription)
	{

	}

	/**
	 * When subscription is created
	 * @param JTable $subscription
	 */
	public function onCreate($subscription)
	{

	}

	/**
	 * Activation when user logged in
	 * @param $subscription
	 */
	public function onActive($subscription)
	{
	}

	public function onDisactive($subscription)
	{
	}

	public function getDescription()
	{
	}
}