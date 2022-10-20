<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Joompush
 * @author     Weppsol <contact@weppsol.com>
 * @copyright  Copyright (c) 2017 Weppsol Technologies. All rights reserved.
 * @license    GNU GENERAL PUBLIC LICENSE V2 OR LATER.
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Class JoompushController
 *
 * @since  1.6
 */
class JoompushController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $app  = JFactory::getApplication();
        $view = $app->input->getCmd('view', 'subscribers');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
	
	public function sendNotifications()
	{
		$config = JFactory::getConfig();  

		$jDate = JFactory::getDate();  

		$site_offset = $config->get('offset');    

		$jdate = JFactory::getDate('now', $site_offset);  
		
		$last = $jdate->format('Y-m-d H:i:s',true);
		
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joompush_notifications');
		$query->where('sent_on <= '. $db->quote($last));
		$query->where('sent = 0');
		$db->setQuery($query);
		$query_result =$db->loadObjectList();
		
		if($query_result)
		{
			require_once JPATH_ROOT . '/components/com_joompush/helpers/jpush.php';
			
			$JoompushHelpersJpush = new JoompushHelpersJpush;
			
			$push_results = array();
			
			foreach($query_result as $data)
			{
				$template = new stdClass();
				$template->title 		= $data->title;
				$template->message 		= $data->message;
				$template->icon 		= $data->icon;
				$template->url 			= $data->url;
				
				$pushdata = new stdClass();
				$pushdata->template = $template;
				$pushdata->code = $data->code;
			
				
				if($data->group_id)
				{
					$pushdata->gid	= $data->group_id;
					
					$JoompushHelpersJpush::jtopicPush($pushdata);
				}
				else
				{
					$pushdata->key	= array($data->key);
					
					$push_results[$data->key] = $JoompushHelpersJpush::jpush($pushdata);
				}
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__joompush_notifications'))->set($db->quoteName('sent') . ' = ' . $db->quote(1))->where($db->quoteName('id') . ' = ' . $db->quote( $data->id ));
				$db->setQuery($query);
				$result = $db->execute();
			}
			
			if ($push_results)
			{
				foreach($push_results as $k=>$result)
				{
					$result = json_decode($result);
					
					$archive_keys = array();
			
					if (isset($result->results[0]->error))
					{
						
						if ($result->results[0]->error == 'NotRegistered' || $result->results[0]->error == 'InvalidRegistration')
						{
							$query = $db->getQuery(true);
							
							$query->delete($db->quoteName('#__joompush_subscribers'));
							$query->where($db->quoteName('key') . ' = ' . $db->quote( $k ));
							 
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}
		
	}
}
