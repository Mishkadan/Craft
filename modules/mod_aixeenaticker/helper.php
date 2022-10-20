<?php
/*------------------------------------------------------------------------
# Aixeena Ticker (module)
# ------------------------------------------------------------------------
# version		4.0.0
# author    	Top Position
# copyright 	Copyright (c) 2018 Top Position All rights reserved.
# @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website		http://posicionamientoenbuscadoreswebseo.es/
-------------------------------------------------------------------------
*/
// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';
JModelLegacy::addIncludePath(JPATH_SITE.'/modules/mod_aixeenaticker', 'ContentModel');

abstract class modaixeenatickerHelper
{
	public static function getList(&$params)
	{
		// Get the dbo
		$db = JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('AixeenaTicker', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);
		
		
		//echo '<pre>';
		//print_r($params->get('tags'));
		//echo '</pre>';
		
		
		$model->tags = $params->get('tags');
		$model->date_range = $params->get('date_range',0);
		
		//die();
		
		$model->setState('list.start', $params->get('tags'));

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		
		// User filter
		
		if($params->get('filterbyuser',0)&&$params->get('fuser',0))  {
		
			
		
			$model->setState('filter.author_id', (int) $params->get('fuser'));
		
			//echo $params->get('fuser');
		
		} else {
		
		
			$userId = JFactory::getUser()->get('id');
			switch ($params->get('user_id'))
			{
				case 'by_me':
					$model->setState('filter.author_id', (int) $userId);
					break;
				case 'not_me':
					$model->setState('filter.author_id', $userId);
					$model->setState('filter.author_id.include', false);
					break;
	
				case '0':
					break;
	
				default:
					$model->setState('filter.author_id', (int) $params->get('user_id'));
					break;
			}
		
		}


		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1':
				$model->setState('filter.featured', 'only');
				break;
			case '0':
				$model->setState('filter.featured', 'hide');
				break;
			default:
				$model->setState('filter.featured', 'show');
				break;
		}

		// Set ordering
		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created',
			'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
			'c_dsc' => 'a.created',
			'p_dsc' => 'a.publish_up',
			'popular' => 'a.hits',
			'a.ordering' => 'a.ordering',
			'fp.ordering' => 'fp.ordering',
		);
		
		
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
		
		$dir = $params->get('ordering_direction','DESC');

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $dir);

		$items = $model->getItems();

		if($items) { 
			
			foreach ($items as $key => $item)
			{
			
				$item->slug = $item->id.':'.$item->alias;
				$item->catslug = $item->catid.':'.$item->category_alias;
	
				if ($access || in_array($item->access, $authorised))
				{
					// We know that user has the privilege to view the article
					$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
				} else {
					$item->link = JRoute::_('index.php?option=com_users&view=login');
				}
				
				$user_item = JFactory::getUser($item->created_by);
				if($params->get('filterbyusergroup',0)&&$params->get('usergroup',0))  {
					if(in_array($params->get('usergroup',0), $user_item->groups)==false) unset($items[$key]);
				}
				
				
			}
		}

		return $items;
	}


	public static function firstXChars($string, $chars = 100, $suffix)
		{
				if(strlen($string)<=$chars) return $string;

				$text = $string." "; 
				$text = substr($text,0,$chars); 
				$text = substr($text,0,strrpos($text,' ')); 
				$text = $text.$suffix; 
				return $text; 
			
		}
	
}
?>