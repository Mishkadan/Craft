<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.JoomPush
 *
 * @copyright   Copyright (C) 2018 Weppsol Technologies, Inc. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */
 
defined ('_JEXEC') or die;

if (JComponentHelper::getComponent('com_joompush', true)->enabled)
{
	require_once JPATH_ROOT . '/components/com_joompush/helpers/jpush.php';
	require_once JPATH_ROOT . '/components/com_joompush/helpers/joompush.php';
}

class plgContentJpcontent extends JPlugin
{
	public function __construct(& $subject, $config)
	{
	  parent::__construct($subject, $config);
	  $this->loadLanguage();
	}
	
	public function onContentPrepareForm($form, $data)
	{ 
		$jinput = JFactory::getApplication()->input;
		
		if ($jinput->get('option') == 'com_content')
		{ 
			if (!($form instanceof JForm))
			{
				$this->_subject->setError('JERROR_NOT_A_FORM');
				return false;
			}

			// Add the extra fields to the form.
			JForm::addFormPath(dirname(__FILE__) . '/jpcontent');
			$form->loadFile('jpcontent', false);
		}
		
		return true;
	}
	
	public function onContentAfterSave($context, $article, $isNew)
	{ 
		if (JComponentHelper::getComponent('com_joompush', true)->enabled)
		{		
			$articleId = $article->id;
			
			$aricle_attribs = json_decode($article->attribs); 
			
			if (isset($aricle_attribs->jp_send_notification) && $aricle_attribs->jp_send_notification == 1)
			{
				$article_title= $article->get('title');
				$article_images= json_decode($article->get('images'))->image_intro;
				
				$article_image = '';
				
				if ($article_images)
				{
					$article_image = $article_images;
				}
				
				$uri = JUri::getInstance(); 
				$article_url =  $uri->toString();	
				
				$notification_msg = strip_tags(substr($article->introtext, 0, 50));
				$notification_msg = trim(preg_replace('/\s+/', ' ', $notification_msg));
				
				$track_code 		= md5(uniqid(rand(), true)); 
				
				$pushMsg 					= new stdClass;
				$pushMsg->template          = new stdClass;
				
				$pushMsg->template->icon 	= $article_image;
				$pushMsg->template->url 	= $article_url;
				$pushMsg->code				= $track_code;
				$pushMsg->template->title	= $article_title;
				$pushMsg->template->message	= $notification_msg;
				
				if ($aricle_attribs->jp_subscribers_ids)
				{
					foreach ($aricle_attribs->jp_subscribers_ids as $gid)
					{
						$pushMsg->gid 				= $gid;

						$JoompushHelpersJpush = new JoompushHelpersJpush;
						$result = $JoompushHelpersJpush::jtopicPush($pushMsg);
						
						$result = json_decode($result);
						
						if ($result->message_id)
						{
							$JoompushHelpersJoompush = new JoompushHelpersJoompushsite;
							$JoompushHelpersJoompush->saveNotification('', $pushMsg->gid, 'group', $pushMsg->template, 1, 'com_content.admin', $articleId, $track_code,1,'');
						}
					}
				}
			}
		}
		
      return true;
	}
	
	public function onContentPrepareData($context, $data)
	{
		// load the form
		JForm::addFormPath(dirname(__FILE__) . '/jpcontent');
		$form = new JForm('com_content.article');
		$form->loadFile('jpcontent', false);

		$data->attribs['jp_send_notification'] = 0;
		
		return true;
	}
}
?>
