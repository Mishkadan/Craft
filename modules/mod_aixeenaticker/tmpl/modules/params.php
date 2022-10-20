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

	
	// routes
	$route = 		JURI::base(true).'/modules/mod_aixeenaticker/';
	$route_assets= 	JURI::base(true).'/modules/mod_aixeenaticker/assets';
	
	$template = $params->get('template','blank'); 
	
	$layout = $params->get('layoutmode','default'); 	
	
	$CSS = $params->get('includecss',1);
	$aixeenabuttons = $params->get('aixeenabuttons',1);
	$js  = $params->get('includejs',1);	
	
	$module_height = $params->get('module_height','');
	$module_width = $params->get('module_width','');
	
	// styles
	$style = '';
	
	if($module_height) $style .= 'height:'.$module_height.'px; ';
	if($module_width) $style .= 'width:'.$module_width.'px; '; 

	if($style) $style = ' style="'.$style.'"';


	// add CSS 
	
	$addCSS = '';
	
	$backgroundcolor = 	$params->get('module_background_color','');
	$backgroundimg = 	$params->get('backgroundimg','');
	$modulepadding = 	$params->get('module_padding','');
	$modulemargin = 	$params->get('module_margin','');
	
	$font_family  		= 	$params->get('font_family','');
	
	if($params->get('led_font',0))  $font_family = $params->get('led_font',0);
	
	$font_size			= 	$params->get('font_size','');
	$font_weight		= 	$params->get('font_weight','');
	$line_height		= 	$params->get('line_height','');
	$text_color			= 	$params->get('module_text_color','');
	$link_color			= 	$params->get('module_link_color','');
	$link_hover_color	= 	$params->get('link_hover_color','');	
	$module_link_decoration			= 	$params->get('module_link_decoration','');
	$module_link_hover_decoration	= 	$params->get('module_link_hover_decoration','');
	
	$addCSSgeneral = '';
	if($backgroundcolor) $addCSSgeneral .= 'background-color:'.$backgroundcolor.';';
	if($backgroundimg) $addCSSgeneral .= 'background-image: url(././././'.$backgroundimg.'); background-repeat:repeat; background-position:left top;';
	if($modulepadding) $addCSSgeneral .= 'padding:'.$modulepadding.'px;';
	if($modulemargin) $addCSSgeneral .= 'margin:'.$modulemargin.'px;';
	if($font_family) $addCSSgeneral .= 'font-family:'.$font_family.';';
	if($font_size) $addCSSgeneral .= 'font-size:'.$font_size.';';
	if($font_weight) $addCSSgeneral .= 'font-weight:'.$font_weight.';';
	if($line_height) $addCSSgeneral .= 'line-height:'.$line_height.';';
	if($text_color) $addCSSgeneral .= 'color:'.$text_color.';';
	
	if($addCSSgeneral) $addCSS .='
#aixwebtick-'.$module->id.' {'.$addCSSgeneral.';}';

	$addCSSgeneral = '';
	if($link_color) $addCSSgeneral .= 'color:'.$link_color.';';
	if($module_link_decoration) $addCSSgeneral .= 'text-decoration:'.$module_link_decoration.';';
	
	if($addCSSgeneral) $addCSS .='
#aixeena-ul-webticker-'.$module->id.'  li a {'.$addCSSgeneral.';}';

	$addCSSgeneral = '';
	if($link_hover_color) $addCSSgeneral .= 'color:'.$link_hover_color.';';
	if($module_link_hover_decoration) $addCSSgeneral .= 'text-decoration:'.$module_link_hover_decoration.';';
	
	if($addCSSgeneral) $addCSS .='
#aixeena-ul-webticker-'.$module->id.' li a:hover {'.$addCSSgeneral.';}';


	//  --------------------------------------------------- div items styles (v2)
	
	
	
		// title CSS	
		if($params->get('show_title',1))  {
	
			$padding 			= 	$params->get('title_padding','');
			$font_family  		= 	$params->get('title_font_family','');
			$font_size			= 	$params->get('title_font_size','');
			$line_height		= 	$params->get('title_line_height','');
			$text_color			= 	$params->get('title_text_color','');
			$link_color			= 	$params->get('title_link_color','');
			$link_hover_color	= 	$params->get('title_link_hover_color','');
			$title_link_decoration			= 	$params->get('title_link_decoration','');
			$title_link_hover_decoration	= 	$params->get('title_link_hover_decoration','');
		
			$addCSSgeneral = '';
			if($padding) $addCSSgeneral .= 'padding-bottom:'.$padding.';';
			if($font_family) $addCSSgeneral .= 'font-family:'.$font_family.';';
			if($font_size) $addCSSgeneral .= 'font-size:'.$font_size.';';
			if($line_height) $addCSSgeneral .= 'line-height:'.$line_height.';';
			if($text_color) $addCSSgeneral .= 'color:'.$text_color.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li aixeenawebticker-title{'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_color) $addCSSgeneral .= 'color:'.$link_color.';';
			if($title_link_decoration) $addCSSgeneral .= 'text-decoration:'.$title_link_decoration.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li aixeenawebticker-title a {'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_hover_color) $addCSSgeneral .= 'color:'.$link_hover_color.';';
			if($title_link_hover_decoration) $addCSSgeneral .= 'text-decoration:'.$title_link_hover_decoration.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li aixeenawebticker-title a:hover {'.$addCSSgeneral.';}';

		} // end of title css
		
		// author CSS	
		if($params->get('show_aut',0))  {
	
			$padding 			= 	$params->get('author_padding','');
			$font_family  		= 	$params->get('author_font_family','');
			$font_size			= 	$params->get('author_font_size','');
			$line_height		= 	$params->get('author_line_height','');
			$text_color			= 	$params->get('author_text_color','');
			$link_color			= 	$params->get('author_link_color','');
			$link_hover_color	= 	$params->get('author_link_hover_color','');
			$author_link_decoration			= 	$params->get('author_link_decoration','');
			$author_link_hover_decoration	= 	$params->get('author_link_hover_decoration','');
		
			$addCSSgeneral = '';
			if($padding) $addCSSgeneral .= 'padding-bottom:'.$padding.';';
			if($font_family) $addCSSgeneral .= 'font-family:'.$font_family.';';
			if($font_size) $addCSSgeneral .= 'font-size:'.$font_size.';';
			if($line_height) $addCSSgeneral .= 'line-height:'.$line_height.';';
			if($text_color) $addCSSgeneral .= 'color:'.$text_color.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker_author{'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_color) $addCSSgeneral .= 'color:'.$link_color.';';
			if($author_link_decoration) $addCSSgeneral .= 'text-decoration:'.$author_link_decoration.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker_author a {'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_hover_color) $addCSSgeneral .= 'color:'.$link_hover_color.';';
			if($author_link_hover_decoration) $addCSSgeneral .= 'text-decoration:'.$author_link_hover_decoration.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker_author a:hover {'.$addCSSgeneral.';}';

		} // end of author css
		
		// category CSS	
		if($params->get('show_header',1))  {
	
			$padding 			= 	$params->get('category_padding','');
			$font_family  		= 	$params->get('category_font_family','');
			$font_size			= 	$params->get('category_font_size','');
			$line_height		= 	$params->get('category_line_height','');
			$text_color			= 	$params->get('category_text_color','');
			$link_color			= 	$params->get('category_link_color','');
			$link_hover_color	= 	$params->get('category_link_hover_color','');
			$category_link_decoration			= 	$params->get('category_link_decoration','');
			$category_link_hover_decoration	= 	$params->get('category_link_hover_decoration','');
		
			$addCSSgeneral = '';
			if($padding) $addCSSgeneral .= 'padding-bottom:'.$padding.';';
			if($font_family) $addCSSgeneral .= 'font-family:'.$font_family.';';
			if($font_size) $addCSSgeneral .= 'font-size:'.$font_size.';';
			if($line_height) $addCSSgeneral .= 'line-height:'.$line_height.';';
			if($text_color) $addCSSgeneral .= 'color:'.$text_color.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker-category {'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_color) $addCSSgeneral .= 'color:'.$link_color.';';
			if($category_link_decoration) $addCSSgeneral .= 'text-decoration:'.$category_link_decoration.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker-category a {'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_hover_color) $addCSSgeneral .= 'color:'.$link_hover_color.';';
			if($category_link_hover_decoration) $addCSSgeneral .= 'text-decoration:'.$category_link_hover_decoration.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker-category a:hover {'.$addCSSgeneral.';}';

		} // end of category css
		
		// date CSS	
		if($params->get('show_date',1))  {
	
			$padding 			= 	$params->get('date_padding','');
			$font_family  		= 	$params->get('date_font_family','');
			$font_size			= 	$params->get('date_font_size','');
			$line_height		= 	$params->get('date_line_height','');
			$text_color			= 	$params->get('date_text_color','');
		
			$addCSSgeneral = '';
			if($padding) $addCSSgeneral .= 'padding-bottom:'.$padding.';';
			if($font_family) $addCSSgeneral .= 'font-family:'.$font_family.';';
			if($font_size) $addCSSgeneral .= 'font-size:'.$font_size.';';
			if($line_height) $addCSSgeneral .= 'line-height:'.$line_height.';';
			if($text_color) $addCSSgeneral .= 'color:'.$text_color.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker-date {'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_color) $addCSSgeneral .= 'color:'.$link_color.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker-date a {'.$addCSSgeneral.';}';
		
			$addCSSgeneral = '';
			if($link_hover_color) $addCSSgeneral .= 'color:'.$link_hover_color.';';
			
			if($addCSSgeneral) $addCSS .='
		#aixeena-ul-webticker-'.$module->id.' li .aixeenawebticker-date a:hover {'.$addCSSgeneral.';}';

		} // end of date css
		
	

	$addCSSitem = '';
	
	$padding =  $params->get('item_padding','');
	if($padding) $addCSSitem .= 'padding-right:'.$padding.'px;';

	if($addCSSitem)  $addCSS .= '
	#aixeena-ul-webticker-'.$module->id.' li {'.$addCSSitem.'}';
	
	
	$maskheight 	=	$params->get('maskheight',0);
	$maskheightpx 	=	$params->get('maskheightpx','30');
	
	if($maskheight)  $addCSS .= '				
		#aixwebtick-'.$module->id.' .mask { height: '.$maskheightpx.'px; }
	';
	
	
	$fixedmode = $params->get('fixedmode','default');
	
	if($fixedmode == 'fixedtotop')  $addCSS .= '				
		#aixwebtickerall-'.$module->id.' { position: fixed; left: 0; top: 0; margin: 0; padding: 0; width: 100%; }
		#aixwebtickerall-'.$module->id.' #aixwebtick-'.$module->id.' { margin:0; } 
		
	';
	
	if($fixedmode == 'fixedtobottom')  $addCSS .= '				
		#aixwebtickerall-'.$module->id.' { position: fixed; left: 0; bottom: 0; margin: 0; padding: 0; width: 100%; }
		#aixwebtickerall-'.$module->id.' #aixwebtick-'.$module->id.' { margin:0; } 
	';
	
	
	$bodypadding 	=	$params->get('bodypadding',0);
	$bodypaddingpx 	=	$params->get('bodypaddingpx','80');
	
	
	if($bodypadding == 1)  $addCSS .= '
		body { padding-top: '.$bodypaddingpx.'px;} ';
	
	if($bodypadding == 2)  $addCSS .= '
		body { padding-bottom: '.$bodypaddingpx.'px;} ';

	
	if($params->get('led_font',0)) include 'customfonts.php';
	
	if(!$params->get('gradients',1)) $addCSS .= '				
		#aixwebtick-'.$module->id.' .maks2-left{ display:none; } 
		#aixwebtick-'.$module->id.' .maks2-right{ display:none; }
	';
	
	if(!$CSS) $addCSS = '';

?>