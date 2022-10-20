<?php
const _JEXEC = 1;

// error_reporting(E_ALL | E_NOTICE);
// ini_set('display_errors', 'On');

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

require_once JPATH_CONFIGURATION . '/configuration.php';

class sendNotifications extends JApplicationCli
{
	
	public function doExecute()
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
    
JApplicationCli::getInstance('sendNotifications')->execute();
