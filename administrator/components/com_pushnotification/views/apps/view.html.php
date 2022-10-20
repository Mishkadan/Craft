<?php
/*
* @package 		com_pushnotification - Push Notification
* @version		1.0.2
* @created		Jan 2020
* @author		ExtensionCoder.com
* @email		developer@extensioncoder.com
* @website		https://www.extensioncoder.com
* @support		https://www.extensioncoder.com/support.html
* @copyright	Copyright (C) 2019-2020 ExtensionCoder. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

use \Joomla\CMS\Language\Text;

/**
 * View class for a list of Pushnotification.
 *
 * @since  1.6
 */
class PushnotificationViewApps extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
        
        

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		PushnotificationHelper::addSubmenu('apps');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = PushnotificationHelper::getActions();

		JToolBarHelper::title(Text::_('COM_PUSHNOTIFICATION_TITLE_APPS'), 'apps.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/app';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('app.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					JToolbarHelper::custom('apps.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('app.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('apps.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('apps.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'apps.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('apps.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('apps.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'apps.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('apps.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_pushnotification');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_pushnotification&view=apps');
	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
		);
	}

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
	
	// check applications
	public function checkApps()
	{
			$params = JComponentHelper::getParams('com_pushnotification');
			$authkey = $params->get('uakey');
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/apps");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
								   'Authorization: Basic '.$authkey.''));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

				$response = curl_exec($ch);
				$response = json_decode($response, true);
				curl_close($ch);
				
						if ( array_key_exists('errors', $response) ){
							$app = JFactory::getApplication();
							$message = JText::_('COM_PUSHNOTIFICATION_AUTHKEYNOT');
							$url = 'index.php?option=com_config&view=component&component=com_pushnotification';
							$app->enqueueMessage($message, 'Error');
							$app->redirect($url, false);
						} else {	
							return $response;
						}
	}	
	// check plugin
	public function isPluginInstalled($pname)
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE name = '".$pname."'");
		$is_enabled = $db->loadResult();
		return $is_enabled;
	}	
	// check app rest api key
	public function isRestApiExist($appid)
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT restapikey FROM #__pushnotification_config WHERE appid = '".$appid."'");
		$restapikey = $db->loadResult();
		return $restapikey;
	}
	// submit app id
	public function saveUpdateRestApi($submitrestapi,$submitappid,$submitupdate,$userId)
	{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
		
		if($submitupdate=='save')
		{
			// Create and populate an object.
			$appsave = new stdClass();
			$appsave->appid = $submitappid;
			$appsave->restapikey=$submitrestapi;
			$appsave->created_by=$userId;
			$appsave->modified_by=$userId;
			$result = JFactory::getDbo()->insertObject('#__pushnotification_config', $appsave);			
			
		} else 
		{
			// Create an object for the record we are going to update.
			$appsave = new stdClass();
			$appsave->appid = $submitappid;
			$appsave->restapikey = $submitrestapi;
			$appsave->created_by=$userId;
			$appsave->modified_by=$userId;
			$result = JFactory::getDbo()->updateObject('#__pushnotification_config', $appsave, 'appid');			
			
		}
		
	}	

}
