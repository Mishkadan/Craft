<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 *
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class EmeraldControllerEmHistory extends MControllerAdmin
{
	public function cancels()
	{
		$cid   = JFactory::getApplication()->input->get('id');
		$app   = JFactory::getApplication();
		$table = JTable::getInstance('EmSubscription', 'EmeraldTable');

		$table->load($cid);

		if(!$table->id)
		{
			$app->enqueueMessage(JText::_('EM_SUBSCR_NOTFOUND'), 'warning');
			$app->redirect(EmeraldApi::getLink('emhistory'));
		}

		if($table->activated == 1)
		{
			$app->enqueueMessage(JText::_('EM_SUBSCR_IS_ACTIVE'), 'warning');
			$app->redirect(EmeraldApi::getLink('emhistory'));
		}

		if($table->delete($cid))
		{
			$app->enqueueMessage(JText::_('EM_CANCEL_CUBSCRIPTION'));
			$app->redirect(EmeraldApi::getLink('emhistory'));
		}


		$app->redirect(EmeraldApi::getLink('emhistory'));
	}
}
