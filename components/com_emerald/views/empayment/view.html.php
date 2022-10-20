<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.mvc.view.base');
include_once JPATH_ROOT . '/components/com_emerald/models/eminvoiceto.php';

/**
 * @property mixed title
 */
class EmeraldViewEmPayment extends MViewBase
{
	function display($tpl = NULL)
	{
		$app   = JFactory::getApplication();
		$user  = JFactory::getUser();
		$model = $this->getModel();
		$id    = $app->input->getInt('sid');

		if(!$id)
		{
			$app->enqueueMessage(JText::_('EMR_ERR_NOPLANCONFIRM'), 'warning');
			$app->redirect(EmeraldApi::getLink('emlist', FALSE));
		}

		$this->plan       = EmeraldApi::getPreparedPlan($id);
		$this->com_params = JComponentHelper::getParams('com_emerald');

		if(empty($this->plan->id))
		{
			JError::raiseNotice(100, JText::_('EMR_CANNOTPURCH'));
			$app->redirect(EmeraldApi::getLink('emlist', FALSE));
		}

		if(!in_array($this->plan->access_pay, $user->getAuthorisedViewLevels()))
		{
			$app->enqueueMessage(JText::_('EMR_CANNOTPURCHACCESS'), 'warning');
			$app->redirect(EmeraldApi::getLink('emlist', FALSE));
		}

		$this->title = JText::_('EMR_CONFIRMSUBSCR');

		$pathway = $app->getPathway();
		$pathway->addItem($this->title);

		$doc = JFactory::getDocument();
		$doc->setTitle($this->title);

		$this->params = JComponentHelper::getParams('com_emerald');
		$this->user   = $user;

		if($this->params->get('use_invoice', 0) == 1 && $app->input->getInt('invoice') ==  -1)
		{
			$iid = EmeraldApi::saveBillAddress();
			$app->setUserState('com_emerald.invoiceto.selector', $iid ? $iid : -1);
		}

		if($this->params->get('use_invoice', 0))
		{
			$inv_model      = new EmeraldModelsEmInvoiceTo();
			$this->inv_list = $inv_model->getList();
		}

		$this->fields = array();
		$this->addons = array();

		if($this->plan->params->get('properties.fields'))
		{
			$this->fields = $model->getAddonFields($this->plan);
		}

		foreach($this->fields AS $field)
		{
			if($add = $field->affectPrice($this->plan)) {
				$this->addons[$field->getPaymentLabel()] = $add;
			}
			if($field->getError(0))
			{
				$app->enqueueMessage($field->getError(), 'error');
			}
		}

		if(!$this->plan->is_donation)
		{
			if($app->input->get('coupon'))
			{
				$app->setUserState('last-emerald-coupon', $app->input->get('coupon'));
			}
			$this->coupon  = EmeraldHelperCoupon::getCoupon($app->getUserState('last-emerald-coupon'), $this->plan->id, $this->plan->total, TRUE);
			$this->coupons = $model->getCouponsNumber($id);
		}

		else
		{
			$this->coupon  = NULL;
			$this->coupons = 0;
		}

		parent::display($tpl);

	}
}