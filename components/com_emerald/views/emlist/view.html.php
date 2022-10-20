<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.mvc.view.base');
class EmeraldViewEmList extends MViewBase
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$model = $this->getModel();
		$user = JFactory::getUser();

		$this->mparams = ($app->getMenu()->getActive() ? $app->getMenu()->getActive()->params : new JRegistry());
		$this->_prepareDocument();

		$plan_ids = $app->input->getString('id', false);
		$group_ids = $plan_ids ? null : $this->mparams->get("groups");

		if(($plan_ids || $group_ids) && $this->mparams->get('link', 0)) {
			JError::raiseNotice(100, JText::sprintf('EM_NOT_ALL_PLANS', EmeraldApi::getLink('emlist')));
		}

		$items = $model->getPlans($plan_ids, $group_ids);

		if(count($items) <= 0 && !$user->get('id'))
		{
			JFactory::getApplication()->redirect(
				JRoute::_(JComponentHelper::getParams('com_emerald')->get('general_login_url') .
					'&return=' . urlencode(base64_encode(JUri::getInstance()->toString())), FALSE)
			);
		}

		$prepare = EmeraldHelper::preparePlans($items, $model);
		$this->cats = $prepare['cats'];
		$this->items = $prepare['plans'];

		$this->params = JComponentHelper::getParams('com_emerald');
		$this->usersubs = EmeraldHelper::getUserPlans();

		$this->menu = Mint::loadLayout('links', JPATH_COMPONENT .'/layouts');

		parent::display($tpl);
	}

	private function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$this->addTemplatePath(JPATH_COMPONENT.'/views/elements/');

		$this->mparams->set('page_title', $this->mparams->get('page_title', JText::_('EPURCHASENEW')));
		$doc->setTitle($this->mparams->get('page_title'));

		if($meta_key = $this->mparams->get('menu-meta_keywords'))
		{
			$doc->setMetaData('keywords', $meta_key);
		}

		if($meta_desc = $this->mparams->get('menu-meta_description'))
		{
			$doc->setMetaData('description', $meta_desc);
		}

		//$pathway = $app->getPathway();
		//$pathway->addItem($this->mparams->get('page_title'));
	}
}

?>