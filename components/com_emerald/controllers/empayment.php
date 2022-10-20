<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class EmeraldControllerEmPayment extends MControllerAdmin
{

	public function &getModel($name = 'EmPayment', $prefix = 'EmeraldModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => TRUE), $config = array());

		return $model;
	}

	public function getinvoicetext()
	{
		echo EmeraldInvoiceHelper::text();
		JFactory::getApplication()->close();
	}

	public function getinvoiceform()
	{
		echo EmeraldInvoiceHelper::form();
		JFactory::getApplication()->close();
	}

	public function coupon()
	{
		$app = JFactory::getApplication();
		$app->setUserState('last-emerald-coupon', $app->input->getString('coupon'));
		$app->redirect(JUri::getInstance()->toString());
	}

	public function send()
	{
		$app        = JFactory::getApplication();
		$this->plan = EmeraldApi::getPreparedPlan($this->input->get('sid'));
		$user_id    = JFactory::getUser()->get('id');

		if(!$user_id && $this->plan->params->get('properties.rds', 0))
		{
			$user_id = $this->_rds();
		}

		EmeraldApi::send($user_id, $this->input->get('sid'), $this->input->get('processor'), $this->input->get('coupon'));
	}

	private function _rds()
	{
		$email   = $this->input->getString('email');
		$element = new SimpleXMLElement('<xml required="true"></xml>');
		$rule    = new JFormRuleEmail($element, $email);

		if(!$rule->test($element, $email))
		{
			$msg = JText::_('EMAILISNOTCORRECT');

			$this->setRedirect(JRoute::_('index.php?option=com_emerald&view=empayment&sid=' . $this->plan->id), $msg);
			$this->redirect();
		}

		$db = JFactory::getDbo();

		$db->setQuery("SELECT id FROM #__users WHERE email = '{$email}'");
		$result = $db->loadObject();

		if($result)
		{
			return $result->id;
		}

		$username = $email;

		if(!$this->plan->params->get('properties.rds_email_login', 0))
		{
			$parts    = explode('@', $email);
			$username = $parts[0];

			do
			{
				$db->setQuery("SELECT id FROM #__users WHERE username = '" . $db->escape($username) . "'");
				$result = $db->loadResult();
				if($result)
				{
					$username .= '_';
				}
			}
			while($result);

		}

		$data['name']      = $username;
		$data['username']  = $username;
		$data['email1']    = $email;
		$data['email2']    = $email;
		$pass              = JUserHelper::genRandomPassword();
		$data['password1'] = $pass;

		$lang = JFactory::getLanguage();
		$lang->load('com_users');

		MModelBase::addIncludePath(JPATH_ROOT . '/components/com_users/models');
		$model = MModelBase::getInstance('Registration', 'UsersModel');

		//В модели Registration этого нет, модель вызывается не из родного компонента.
		//Формы не находит, ошибки не выдает, см. строка 128.
		JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');

		$return = $model->register($data);

		if(!$return)
		{
			$this->setError($model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_emerald&view=empayment&sid=' . $this->plan->id), $model->getError());
			$this->redirect();
		}

		$com_user = JComponentHelper::getParams('com_users');

		if($com_user->get('useractivation') == 0)
		{
			$options             = array();
			$options['remember'] = 1;
			$options['return']   = NULL;
			$options['silent']   = TRUE;
			$options['lifetime'] = 1;

			$credentials             = array();
			$credentials['username'] = $username;
			$credentials['password'] = $pass;

			$url = JFactory::getSession()->get('emerald_access_url', NULL);
			$result = JFactory::getApplication()->login($credentials, $options);
			JFactory::getSession()->set('emerald_access_url', $url);
		}

		if(!is_int($return))
		{
			$query = $db->getQuery(TRUE);
			$query->select('id');
			$query->from('#__users');
			$query->where('username = ' . $db->Quote($username));
			$db->setQuery($query);

			return $db->loadResult();
		}

		return $return;
	}
}