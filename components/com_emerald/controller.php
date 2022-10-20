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

jimport('mint.mvc.controller.base');

class EmeraldController extends MControllerBase
{

	public function display($cachable = FALSE, $urlparams = array())
	{
		$app            = JFactory::getApplication();
		$user           = JFactory::getUser();
		$emerald_config = JComponentHelper::getParams('com_emerald');
		$is_moder       = in_array($emerald_config->get('moderate'), $user->getAuthorisedViewLevels());
		$redirect       = FALSE;
		$sid            = $this->input->getCmd('sid');

		if(!$this->input->getCmd('view'))
		{
			$this->input->set('view', 'emlist');
		}

		/*** Legacy support ***/
		if(substr($this->input->getCmd('view'), 0, 2) != 'em')
		{
			$this->input->set('view', 'em' . $this->input->getCmd('view'));
		}
		/***    ****/

		if(!$user->get('id') && $this->input->getCmd('view', '') != 'emlist')
		{
			if(!($this->input->getCmd('view') == 'empayment' && $sid &&
				EmeraldApi::getPreparedPlan($sid) &&
				EmeraldApi::getPreparedPlan($sid)->params->get('properties.rds', 0)))
			{
				$session = JFactory::getSession();
				$session->set('try_this_plan', $sid);
				JError::raiseWarning(100, JText::_('EMR_REDIRECT'));
				$redirect = JRoute::_(JComponentHelper::getParams('com_emerald')->get('general_login_url') .
					'&return=' . urlencode(base64_encode(JUri::getInstance()->toString())), FALSE);
			}
		}

		if($user->get('id') && !$is_moder && !in_array(
				$this->input->getCmd('view', ''),
				array(
					'emlist', 'emhistory',
					'empayment', 'emmua', 'eminvoice', 'embill'
				)
			)
		)
		{
			$app->enqueueMessage(JText::_('EMR_REDIRECT'), 'warning');
			$redirect = JFactory::getURI()->base();
		}

		if($redirect)
		{
			JFactory::getApplication()->redirect($redirect, FALSE);

			return;
		}

		parent::display($cachable = FALSE, $urlparams = array());
	}
}

?>
