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
class PushnotificationViewSendmessages extends \Joomla\CMS\MVC\View\HtmlView
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

		PushnotificationHelper::addSubmenu('sendmessages');

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

		JToolBarHelper::title(Text::_('COM_PUSHNOTIFICATION_TITLE_SENDMESSAGES'), 'sendmessages.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/sendmessage';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('sendmessage.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					JToolbarHelper::custom('sendmessages.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('sendmessage.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('sendmessages.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('sendmessages.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'sendmessages.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('sendmessages.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('sendmessages.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'sendmessages.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('sendmessages.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_pushnotification');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_pushnotification&view=sendmessages');
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
	// get all messages
	public function getAllMessages($appid,$restapikey)
	{
			//$restapikey		=	$this->getRestAPIKey($appname);
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://onesignal.com/api/v1/notifications?app_id=".$appid."&limit=1000&offset=0",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
				"authorization: Basic ".$restapikey."",
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);
			$response_arr = json_decode($response);
			
						if ( array_key_exists('errors', $response_arr) ){
							$app = JFactory::getApplication();
							$message = JText::sprintf('Some error occurred during the connection',$appname);
							$url = 'index.php?option=com_pushnotification&view=users&appid='.$appid.'';
							$app->enqueueMessage($message, 'Warning');
							$app->redirect($url, false);
						} else {	
							return $response_arr;
						}
	}

// submit new notification
	public function submitNewNotification( $mtitle, $mbody, $wuntill, $urlback, $appid, $rest_api_exist )
	{
			$restapikey		=	$rest_api_exist;
			// start to send
		$content = array(
			"en" => $mbody
			);
		$heading = array(
			"en" => $mtitle
			);
		//$deliver

		if ( $wuntill !== 'imm' ){
		$fields = array(
			'app_id' => "".$appid."",
			'included_segments' => array('All'),
			'contents' => $content,
			'headings' => $heading,
			'url' => $urlback,
			'send_after' => $wuntill
			//'web-buttons' => array("id"=>"read-more-button", "text"=>"Read more", "icon"=>"http://melltoo.me/wp-content/uploads/2014/10/read-more-button2.png", "url"=>"https://www.extensionbase.com")
		);
		} else {
		$fields = array(
			'app_id' => "".$appid."", // flipbookeasy
			'included_segments' => array('All'),
			'contents' => $content,
			'headings' => $heading,
			'url' => $urlback
			//'web-buttons' => array("id"=>"read-more-button", "text"=>"Read more", "icon"=>"http://melltoo.me/wp-content/uploads/2014/10/read-more-button2.png", "url"=>"https://www.extensionbase.com")
		);			
		}
		$fields = json_encode($fields);
			//print("\nJSON sent:\n");
			//print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic '.$restapikey.''));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		$response_arr	=	json_decode($response);
				$app = JFactory::getApplication();
				$message = JText::sprintf('COM_PUSHNOTIFICATION_MSGSUCCESS',$response_arr->recipients);
				//$url = 'index.php?option=com_pushnotification&view=messages&appid='.$appid.'';
				$app->enqueueMessage($message);
				//$app->redirect($url, false);	
	}	
}
