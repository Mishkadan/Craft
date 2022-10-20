<?php
/**
 *
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 *
 */
defined('_JEXEC') or die('Restricted access');

class EmeraldViewsEmSelectorHtml extends JViewHtml
{
	function render()
	{
		$this->params  = JComponentHelper::getParams('com_emerald');

		$layout = $this->getLayout();
		$this->{'_' . $layout}();

		return parent::render();
	}

	private function _list()
	{
		include_once JPATH_ROOT.'/components/com_emerald/models/emlist.php';
		$model = new EmeraldModelEmList();
		$items       = $model->getPlans($this->plans, $this->groups);
		$prepare     = EmeraldHelper::preparePlans($items);
		$this->cats  = $prepare['cats'];
		$this->items = $prepare['plans'];
	}

	private	function _confirm()
	{
		include_once JPATH_ROOT.'/components/com_emerald/models/empayment.php';
		$model = new EmeraldModelEmPayment();

		$app = JFactory::getApplication();

		$this->plan = EmeraldApi::getPreparedPlan($app->input->get('id'));
		$this->coupon  = EmeraldHelperCoupon::getCoupon($app->input->get('coupon'), $this->plan->id, $this->plan->total, TRUE);
		$this->coupons = $model->getCouponsNumber($app->input->get('id', 0));
	}

	private	function _info()
	{
		$this->usersubs = EmeraldHelper::getUserPlans();
	}
	private function _default()
	{
	}
	function getName()
	{
		return 'emselector';
	}

}
