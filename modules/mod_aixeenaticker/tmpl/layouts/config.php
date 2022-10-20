<?php
/*------------------------------------------------------------------------
	# mod_aixeenawebticker.php - Aixeena Web Ticker (module)
	# ------------------------------------------------------------------------
	# version		4.0.0
	# author    	Ciro Artigot for Aixeena
	# copyright 	Copyright (c) 2018 Top Position All rights reserved.
	# @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Website		http://posicionamientoenbuscadoreswebseo.es/
	
	-------------------------------------------------------------------------
	*/
	// no direct access

	defined('_JEXEC') or die;
		
		// author
		$author = '';
		if($params->get('show_aut',0))  {
		
			$icon = '';
			if($params->get('author_icon','none')!='none') $icon ='<span class="aixeena-glyphicon '.$params->get('author_icon','').'"></span> ';
			
			$author = $item->created_by_alias ? $item->created_by_alias : $item->author;
			if (!empty($item->contactid) && $params->get('link_aut',0) == true) {
				$needle = 'index.php?option=com_contact&view=contact&id=' . $item->contactid;
				$menu = JFactory::getApplication()->getMenu();
				$item_ = $menu->getItems('link', $needle, true);
				$cntlink = !empty($item_) ? $needle . '&Itemid=' . $item_->id : $needle;
				
				if($params->get('pre_aut',1)) {
					if($params->get('pre_aut_text','')=='') $author = JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author));
					else $author = $params->get('pre_aut_text','') .' '. JHtml::_('link', JRoute::_($cntlink), $author);
				} else {
					$author = JHtml::_('link', JRoute::_($cntlink), $author);
				}
			} 
			else {
				if($params->get('pre_aut',1)) {
					if($params->get('pre_aut_text','')=='') $author = JText::sprintf('COM_CONTENT_WRITTEN_BY', $author);
					else $author = $params->get('pre_aut_text','') .' '. $author;
				} 
			}
			$author = '<div class="aixeenawebticker_author">'.$params->get('author_left_sep','').$icon.$author.$params->get('author_right_sep','').'</div>';
					
		}
		
		
		
		
		//category
		$category = '';
		
		if($params->get('show_header',1)) {

			$category .= '<div class="aixeenawebticker-category">';
			if($params->get('link_header',1))  {
				$category .= $params->get('category_left_sep','').'<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid)) .'" >'.$item->category_title.'</a>'.$params->get('category_right_sep','');
			}
			else {
				$category .= $params->get('category_left_sep','').$item->category_title.$params->get('category_right_sep','');
			}
			$category .= '</div>';
		}
		
		// item title (v2)
		
		$title = '<div class="aixeenawebticker-title"> ';
		if($params->get('show_title',1)) { 
			$icon = '';
			if($params->get('title_icon','none')!='none') $icon ='<span class="aixeena-glyphicon '.$params->get('title_icon','').'"></span> ';
			if($params->get('link_title',0)) $title .= '<a href="'.$item->link.'" class="aixeena-title-a">'.$item->title.'</a>';
			else $title .= ''.$params->get('title_left_sep','').$icon.$item->title.$params->get('title_right_sep','').'';
		}	 
		$title .= '</div> ';
		
		$thedate = ''; $sdate = '';

		if($params->get('show_date',1)) {
		
			$thedate = JHtml::_('date', $item->publish_up, JText::_($params->get('thedateformat','DATE_FORMAT_LC3')));
			$thedate = '<span class="aixeenanews-date ">'.$params->get('date_left_sep','').$thedate.$params->get('date_right_sep','').'</span>';
			
			
		}
		
		

?>