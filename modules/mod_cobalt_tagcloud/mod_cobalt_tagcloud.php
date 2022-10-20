<?php

defined('_JEXEC') or die('Restricted access');

$cobalt = JPATH_ROOT . '/components/com_cobalt/api.php';
if(!file_exists($cobalt))
{
	return;
}
require_once(dirname(__FILE__) . '/helper.php');
include_once JPATH_ROOT . '/components/com_cobalt/library/php/html/tags.php';

$section  = new JObject();
$category = new JObject();
$app      = JFactory::getApplication();
$lang     = JFactory::getLanguage();

$Itemid = $app->input->getInt('Itemid');
$tag    = $lang->getTag();
$res    = $lang->load('com_cobalt', JPATH_ROOT . '/components/com_cobalt');

$section->id  = $params->get('depends_on_cat', 0) ? $app->input->getInt('section_id') : $params->get('section_id');
$category->id = $app->input->getInt('cat_id');

if($params->get('show_section_name'))
{
	JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_cobalt/models');
	JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_cobalt/tables');
	$section  = modCobaltTagcloudHelper::getSection($section->id);
	$category = modCobaltTagcloudHelper::getCategory($category->id);
}

$list = modCobaltTagcloudHelper::getTags($section, $params, $category->id);

if(!$list)
{
	return FALSE;
}

$html = $params->get('html_tags', 'H1, H2, H3, H4, H5, H6, strong, b, em, big, small');


require JModuleHelper::getLayoutPath('mod_cobalt_tagcloud', $params->get('layout', 'default'));
