<?php
/**
 * Emerald Granter Plugin by MintJoomla
 * a plugin for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

require_once JPATH_ROOT . '/components/com_emerald/api.php';

class plgUserEmerald extends JPlugin
{

	public function onUserAfterDelete($user, $succes, $msg)
	{
		if(!$succes)
		{
			return FALSE;
		}

		$db = JFactory::getDbo();

		$db->setQuery('DELETE FROM ' . $db->quoteName('#__emerald_subscriptions') . ' WHERE ' . $db->quoteName('user_id') . ' = ' . (int)$user['id']);
		$db->execute();

		$db->setQuery('DELETE FROM ' . $db->quoteName('#__emerald_coupons_history') . ' WHERE ' . $db->quoteName('user_id') . ' = ' . (int)$user['id']);
		$db->execute();

		$db->setQuery('DELETE FROM ' . $db->quoteName('#__emerald_url_history') . ' WHERE ' . $db->quoteName('user_id') . ' = ' . (int)$user['id']);
		$db->execute();

		return TRUE;
	}

	public function onUserBeforeSave($user, $isnew, $data)
	{
		if(!$this->params->get('register'))
		{
			return TRUE;
		}

		if(JFactory::getApplication()->isAdmin())
		{
			return TRUE;
		}

		$id = @$data['emerald']['subscrplans']['plan_id'];

		if($this->params->get('require') && !$id && $isnew)
		{
			throw new Exception(JText::_('EM_PLEASESELECTPLAN'));
		}

		if(!$id)
		{
			return TRUE;
		}

		$plan = EmeraldApi::getPreparedPlan($id);

		if(empty($plan->id))
		{
			return TRUE;
		}

		$coupon = EmeraldHelperCoupon::getCoupon(@$data['emerald']['subscrplans']['coupon'], $plan->id, $plan->total, TRUE, FALSE);
		$total  = $plan->total;

		if(!empty($coupon->discount_total))
		{
			$total -= $coupon->discount_total;
		}
		elseif($plan->discount)
		{
			$total -= $plan->discount;
		}

		if($total > 0 && empty($data['emerald']['subscrplans']['gateway']))
		{
			throw new Exception(JText::_('EM_PLEASESELECTGATEWAY'));
		}

		RETURN TRUE;
	}

	function onUserAfterSave($user, $isnew, $success, $msg)
	{
		$app   = JFactory::getApplication();
		$db    = JFactory::getDBO();
		$count = 0;

		$sql = "SELECT id FROM #__emerald_plans	WHERE grant_new = 1 AND published = '1'";
		$db->setQuery($sql);
		$plans_to_grant_ids = $db->loadColumn();

		if(!empty($plans_to_grant_ids))
		{
			$user_plans = EmeraldHelper::getUserPlans($user['id']);

			foreach($plans_to_grant_ids AS $plan_id)
			{
				if(in_array($plan_id, $user_plans))
				{
					continue;
				}

				EmeraldApi::addSubscription($user['id'], $plan_id, 1, 'grant_new', 0);
				$count++;
			}

			if($count > 0)
			{
				$app->enqueueMessage(JText::plural('EMR_GRANT_NEW', $count));
			}
		}

		if($this->params->get('register') && !empty($user['emerald']['subscrplans']['plan_id']))
		{
			if($app->isAdmin())
			{
				if(EmeraldApi::addSubscription($user['id'], $user['emerald']['subscrplans']['plan_id'], 1, 'admin', $user['emerald']['subscrplans']['price'], time()))
				{
					$app->enqueueMessage(JText::_('EM_SUBSCRIPTIONHASBEENSET'));
				}
			}
			else
			{
				$session = JFactory::getSession();
				$plan = array();
				$plan['user_id'] = $user['id'];
				$plan['plan_id'] = $user['emerald']['subscrplans']['plan_id'];
				$plan['gateway'] = $user['emerald']['subscrplans']['gateway'];
				$plan['coupon'] = $user['emerald']['subscrplans']['coupon'];
				$session->set('emerald_reg_plan', $plan);
				$session->set('emerald_reg_pass', $user['password1']);
				$session->set('emerald_reg_name', $user['username']);
			}
		}

		if($app->isAdmin() || $count == 0)
		{
			return TRUE;
		}

		return TRUE;
	}

	function onUserLogin($user, $options)
	{
		$app = JFactory::getApplication();
		if($app->isAdmin())
		{
			return TRUE;
		}

		$user_id = JFactory::getUser()->get('id');
		if($user_id && JFactory::getSession()->get('emerald_access_url'))
		{
			$redirect = JFactory::getSession()->get('emerald_access_url');
			JFactory::getSession()->set('emerald_access_url', NULL);
			$app->redirect($redirect);
		}

		$db  = JFactory::getDBO();
		$sql = "SELECT id FROM #__emerald_plans	WHERE grant_reg = 1 AND published = '1'";
		$db->setQuery($sql);
		$plans_to_grant_ids = $db->loadColumn();

		if(empty($plans_to_grant_ids))
		{
			return TRUE;
		}

		$sql = "SELECT id FROM #__users WHERE email = " . $db->quote($user['email']);
		$db->setQuery($sql);
		$user_id = $db->loadResult();

		if(empty($user_id))
		{
			return TRUE;
		}

		$user_plans = EmeraldHelper::getUserPlans($user_id);
		$count      = 0;

		foreach($plans_to_grant_ids AS $plan_id)
		{
			if(in_array($plan_id, $user_plans))
			{
				continue;
			}

			EmeraldApi::addSubscription($user_id, $plan_id, 1, 'grant_all', 0);
			$count++;
		}

		if($count == 0)
		{
			return TRUE;
		}


		//TASK correct redirect
		//$app->enqueueMessage(JText::plural('EMR_GRANT_ALL', $count));
		//$app->redirect(EmeraldApi::getLink('history'));



		return TRUE;
	}


	public function onContentPrepareForm($form, $data)
	{
		if(!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return FALSE;
		}

		$name = $form->getName();

		if(!in_array($name,
			array(
				 'com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration'
			))
		)
		{
			return TRUE;
		}

		JFactory::getLanguage()->load('plg_user_emerald', JPATH_BASE . '/administrator');

		JForm::addFormPath(__DIR__ . '/profiles');
		$form->loadFile('profile', FALSE);


		$plans  = ($this->params->get('plans') ? implode(',', $this->params->get('plans')) : '');
		$groups = ($this->params->get('groups') ? implode(',', $this->params->get('groups')) : '');

		$form->setFieldAttribute('subscrplans', 'plans', $plans, 'emerald');
		$form->setFieldAttribute('subscrplans', 'groups', $groups, 'emerald');

		if($name == 'com_users.registration' && $this->params->get('register'))
		{
			if($this->params->get('require'))
			{
				$form->setFieldAttribute('subscrplans', 'required', 'true', 'emerald');
			}
		}
		else
		{
			$form->removeField('subscrplans', 'emerald');
		}

		return TRUE;
	}

	private function sendemails($data)
	{
		$params         = JComponentHelper::getParams('com_users');
		$config         = JFactory::getConfig();
		$useractivation = $params->get('useractivation');
		$sendpassword   = $params->get('sendpassword', 1);
		$db             = JFactory::getDbo();
		$query          = $db->getQuery(TRUE);

		// Compile the notification mail values.
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$data['siteurl']  = JUri::root();

		JFactory::getLanguage()->load('com_user', JPATH_ROOT);


		// Handle account activation/confirmation emails.
		if($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri              = JUri::getInstance();
			$base             = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], FALSE);

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if($sendpassword)
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}
		}
		elseif($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri              = JUri::getInstance();
			$base             = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], FALSE);

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if($sendpassword)
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}
		}
		else
		{

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if($sendpassword)
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_BODY',
					$data['name'],
					$data['sitename'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['siteurl']
				);
			}
		}

		// Send the registration email.
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

		// Send Notification mail to administrators
		if(($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1))
		{
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBodyAdmin = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);

			// Get all admin users
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('sendEmail') . ' = ' . 1);

			$db->setQuery($query);

			$rows = $db->loadObjectList();

			// Send mail to all superadministrators id
			foreach($rows as $row)
			{
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
			}
		}
	}
}