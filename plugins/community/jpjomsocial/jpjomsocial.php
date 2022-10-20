<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Commun	ity.JoomPush
 *
 * @copyright   Copyright (C) 2018 Weppsol Technologies, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


if (JComponentHelper::getComponent('com_joompush', true)->enabled)
{
	require_once JPATH_ROOT . '/components/com_joompush/helpers/jpush.php';
	require_once JPATH_ROOT . '/components/com_joompush/helpers/joompush.php';
}

class plgCommunityJpjomsocial extends CApplications {
	
	function __construct( $subject, $params )
	{
		parent::__construct( $subject, $params );
		$this->loadLanguage();
		
		$this->db = JFactory::getDbo();
	}
	
	function onNotificationAdd($notification)
	{
        // craft make push for chat
	    if($notification->cmd_type = 'push') {

	        /*** log before construct push
            $log = date('Y-m-d H:i:s') . ' incoming data to push ';
            $log .= str_replace(array('	', PHP_EOL), '', print_r($notification, true));
            file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
            ***/
            $db = JFactory::getDbo();
            $jpquery = $db->getQuery(true);
            $jpquery->select('*');
            $jpquery->from($db->quoteName('#__joompush_subscribers'));

            // condition for group notification or user
            if (is_array($notification->target)) {
                $taret_users = explode(',', (string)$notification->target);

                if ($taret_users) {
                    $jpquery->where($db->quoteName('user_id') . ' IN (' . $taret_users . ')' );
                }
            } else {
                $jpquery->where($db->quoteName('user_id') . ' = '. $db->quote($notification->target));
            }

            $db->setQuery($jpquery);
            $joompush_subscribers = $db->loadObjectList();

            $joompush_key = array();

            // Prepare data for push notification
            if ($joompush_subscribers) {
                foreach ($joompush_subscribers as $jpsubscriber) {
                    $joompush_key[] = $jpsubscriber->key;
                }
            }

            $params = json_decode($notification->params);

            $config = JFactory::getConfig();

            $content_const = array();

            $content = $notification->content;

            preg_match_all("/{(.*?)}/", $content, $matches);

            $content_const = $matches[1];

            if ($content_const)
            {
                foreach($content_const as $const)
                {
                    if (isset($params->$const))
                    {
                        $content = str_replace('{' . $const . '}', $params->$const , $content);
                    }
                }
            }

            $content = str_replace('{', '' , $content);
            $content = str_replace('}', '' , $content);

            $cuser = CFactory::getUser($notification->actor);
            $actor_push_icon = $cuser->getThumbAvatar();
            $actor_push_icon = $notification->thumb ? str_replace(JURI::root(), '', $notification->thumb) : str_replace(JURI::root(), '', $actor_push_icon);
            $notification_title = JFactory::getUser()->name;
            $notification_msg 	= $content;
            $jpnotification_url = $notification->url;
            $track_code 		= md5(uniqid(rand(), true));
            $pushMsg 					= new stdClass;
            $pushMsg->template          = new stdClass;
            $pushMsg->template->icon 	= $actor_push_icon;
            $pushMsg->template->url 	= $jpnotification_url;
            $pushMsg->code				= $track_code;
            $pushMsg->template->title	= $notification_title;
            $pushMsg->template->message	= $notification_msg;
            $pushMsg->key 				= $joompush_key;

            // Push notification
            if($pushMsg->key[0])  {

                $JoompushHelpersJpush = new JoompushHelpersJpush;
                $result = $JoompushHelpersJpush::jpush($pushMsg);

                // log  push message
                $log = '*********************************************'.PHP_EOL;
                $log .= date('Y-m-d H:i:s') .' '.JFactory::getUser()->id.'('.$notification_title.') send push  to '.$notification->target.PHP_EOL;
                $log .= str_replace(array('	', PHP_EOL), '', print_r(json_decode($result), true));
                $log .= str_replace(array('	', PHP_EOL), '', print_r($pushMsg, true));
                file_put_contents(__DIR__ . '/pushlog.log', $log . PHP_EOL, FILE_APPEND);

                return true;
            } else return false;
        }

		// If notification id present
		if (isset($notification->id) /* $notification->cmd_type == 'push' */)
		{

			 // query object initialize
			$jpquery = $this->db->getQuery(true);

			
			// Build the query
			
			$jpquery->select('*');
			$jpquery->from($this->db->quoteName('#__joompush_subscribers'));
				
			// condition for group notification or user
				
			if (is_array($notification->target))
			{ 
				$taret_users = explode(',', $notification->target);
				
				if ($taret_users)
				{
					$jpquery->where($this->db->quoteName('user_id') . ' IN (' . $taret_users . ')' );
				}
			}
			else
			{
				$jpquery->where($this->db->quoteName('user_id') . ' = '. $this->db->quote($notification->target));
			}
	
			$this->db->setQuery($jpquery);

			$joompush_subscribers = $this->db->loadObjectList(); // Get JoomPush Subscribers
			
			$joompush_key = array();
			
			// Prepare data for joompush notification 
			
			if ($joompush_subscribers)
			{
				foreach($joompush_subscribers as $jpsubscriber)
				{
					$joompush_key[] = $jpsubscriber->key;
				}
			
				if ($joompush_key)
				{
					$cuser = CFactory::getUser($notification->actor);
					
					$actor_push_icon = $cuser->getThumbAvatar(); 
					$actor_push_icon = str_replace(JURI::root(), '', $actor_push_icon);
				
					$params = json_decode($notification->params);
					
					$config = JFactory::getConfig();
					
					$content_const = array();
					
					// Set notification message
					
					$content = $notification->content;
					
					preg_match_all("/{(.*?)}/", $content, $matches);
					
					$content_const = $matches[1];
					
					if ($content_const)
					{
						foreach($content_const as $const)
						{
							if (isset($params->$const))
							{
								$content = str_replace('{' . $const . '}', $params->$const , $content);
							}
						}
					}
					
					$content = str_replace('{', '' , $content);
					$content = str_replace('}', '' , $content);

					//$notification_title = $config->get( 'sitename' );
					$notification_title = JFactory::getUser()->name ? : $config->get( 'sitename' ); // craft add name from to push
					$notification_msg 	= $content;		
					$jpnotification_url = $params->url;
					$track_code 		= md5(uniqid(rand(), true));
					$pushMsg 					= new stdClass;
					$pushMsg->template          = new stdClass;
					$pushMsg->template->icon 	= $actor_push_icon;
					$pushMsg->template->url 	= $jpnotification_url;
					$pushMsg->code				= $track_code;
					$pushMsg->template->title	= $notification_title;
					$pushMsg->template->message	= $notification_msg;
					$pushMsg->key 				= $joompush_key;
					
					// Push notification
					$JoompushHelpersJpush = new JoompushHelpersJpush;
					
					// $result = $JoompushHelpersJpush::jtopicPush($pushMsg);

                    $result = $JoompushHelpersJpush::jpush($pushMsg);

                    $log = date('Y-m-d H:i:s') . ' jomsocial standart notification';
                    $log .= str_replace(array('	', PHP_EOL), '', print_r($result, true));
                    file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

				}
			}
		}
		return;
	}
}

