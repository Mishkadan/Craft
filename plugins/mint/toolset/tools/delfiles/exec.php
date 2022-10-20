<?php

function _getSubfolder($id)
{
	static $params = array();
	static $defaults = array();
	
	if(!isset($params[$id]))
	{
		$db  = JFactory::getDbo();
		$sql = "SELECT params, field_type FROM #__js_res_fields WHERE id = " . $id;
		$db->setQuery($sql);
		$result        = $db->loadObject();
		$params[$id]   = new JRegistry($result->params);
		$defaults[$id] = $result->field_type;
	}
	
	return $params[$id]->get('params.subfolder', $defaults[$id]);
}

$what = 2;

$db = JFactory::getDBO();

if($what == 0)
{
	$sql = "SELECT DISTINCT record_id FROM `#__js_res_record_values` WHERE `field_type` IN('uploads','video','audio','gallery','paytodownload','image')";
	$db->setQuery($sql);
	$records   = $db->loadColumn();
	$records[] = 0;

	$sql = "SELECT * FROM `#__js_res_files` WHERE record_id NOT IN (" . implode(',', $records) . ") OR saved = 0";
}
else
{
	$sql = "SELECT * FROM `#__js_res_files` WHERE `saved` = 2";
}

$db->setQuery($sql);
$files = $db->loadObjectList();

$size          = $lost_files = 0;
$cobalt_params = JComponentHelper::getParams('com_cobalt');

$files_ids[] = 0;
foreach($files AS $file)
{
	$subfolder = _getSubfolder($file->field_id);
	$size += $file->size;
	$part      = explode("_", $file->filename);
	$filetodel = JPATH_ROOT . DIRECTORY_SEPARATOR . $cobalt_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . date($cobalt_params->get('folder_format'), $part[0]) . DIRECTORY_SEPARATOR . $file->filename;
	if(JFile::exists($filetodel))
	{
		if(JFile::delete($filetodel))
		{
			$files_ids[] = $file->id;
		}
	}
	else
	{
		$files_ids[] = $file->id;
	}
}

$sql = "DELETE FROM #__js_res_files WHERE id IN (" . implode(',', $files_ids) . ")";
$db->setQuery($sql);
$db->query();


$files_in_folder = JFolder::files(JPATH_ROOT . DIRECTORY_SEPARATOR . $cobalt_params->get('general_upload'), '[0-9]{10}_[a-zA-Z0-9]{32}\..', TRUE, TRUE);
settype($files_in_folder, 'array');


$sql = "SELECT filename FROM #__js_res_files";
$db->setQuery($sql);
$files_in_db = $db->loadColumn();

foreach($files_in_folder as $file)
{
	if(!in_array(JFile::getName($file), $files_in_db))
	{
		$temp_size = filesize($file);
		if(JFile::delete($file))
		{
			$size += $temp_size;
			$lost_files++;
		}
	}
}

$app = JFactory::getApplication();
$app->enqueueMessage(JText::sprintf('%d file(s) have been deleted. Total size %s.', count($files) + $lost_files, HTMLFormatHelper::formatSize($size)));