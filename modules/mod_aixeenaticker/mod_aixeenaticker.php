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
$document = JFactory::getDocument();

$articleview = $params->get('articleview',1);
$categoryview = $params->get('categoryview',1);
$noitemid = $params->get('noitemid',1);

$view = JRequest::getString('view','');
$option = JRequest::getString('option','');

$app = JFactory::getApplication();
$menu = $app->getMenu()->getActive()->id;

if($articleview==0 && ($view=='article' && $option=='com_content')) return;
if($articleview==2 && ($view!='article' || $option!='com_content')) return;

if($categoryview==0 && ($view=='category' && $option=='com_content')) return;
if($categoryview==2 && ($view!='category' || $option!='com_content')) return;

if(!$menu && $noitemid )  return;
	

require_once (dirname(__FILE__).'/helper.php');
$list = modaixeenatickerHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require(JModuleHelper::getLayoutPath('mod_aixeenaticker', $params->get('layout', 'default')));

?>