<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltfield.php';

class CFormFieldUpload extends CFormField
{

	public function __construct($field, $default)
	{
		$root      = JPath::clean(JComponentHelper::getParams('com_cobalt')->get('general_upload'));
		$url       = str_replace(JPATH_ROOT, '', $root);
		$url       = str_replace("\\", '/', $url);
		$url       = preg_replace('#^\/#iU', '', $url);
		$this->url = JURI::root(TRUE) . '/' . str_replace("//", "/", $url);

		$this->fieldname = NULL;

		parent::__construct($field, $default);

		$this->subscriptions = array();
		settype($this->value, 'array');
		if(isset($this->value['subscriptions']) && !empty($this->value['subscriptions']))
		{
			$this->subscriptions = $this->value['subscriptions'];
			unset($this->value['subscriptions']);
		}
	}

	public function onJSValidate()
	{
		$js = "jQuery('input[id^=\"" . $this->tmpname . "_tbxFile\"]').remove();";

		return $js;
	}

	protected function getFileUrl($file)
	{
		$out = $this->url . "/{$file->subfolder}/" . $file->fullpath;

		return $out;
	}

	public function getInput()
	{
		$user = JFactory::getUser();
		settype($this->value, 'array');
		$default = array();
		if(isset($this->value[0]))
		{
			if(is_array($this->value[0]))
			{
				$default = $this->value;
			}
			else
			{
				$default = $this->getFiles($this->record);
			}
		}
		$this->options['autostart']               = $this->params->get('params.autostart');
		$this->options['can_delete']               = $this->_getDeleteAccess();
		$this->tmpname = $this->options['tmpname'] = substr(md5(time() . rand(1, 1000000)), 0, 5);

		$html = JHtml::_('mrelements.mooupload', "jform[fields][{$this->id}]" . $this->fieldname, $default, $this->options, $this->id);

		if($this->params->get('params.subscription', 0) && in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()))
		{
			$html .= JHtml::_('emerald.plans', "jform[fields][{$this->id}][subscriptions][]", $this->params->get('params.subscription', array()), $this->subscriptions, 'CRESTRICTIONPLANSDESCR');
		}

		return $html;
	}

	private function _getDeleteAccess()
	{
		$user              = JFactory::getUser();
		$author_can_delete = $this->params->get('params.delete_access', 1);
		$params            = JComponentHelper::getParams('com_cobalt');
		$type              = ItemsStore::getType($this->type_id);
		$app               = JFactory::getApplication();

		$record_id = $app->input->getInt('id', 0);

		if($author_can_delete && (!$record_id || $user->get('id') == ItemsStore::getRecord($record_id)->user_id))
		{
			return 1;
		}
		else
		{
			if($params->get('moderator') == $user->get('id'))
			{
				return 1;
			}

			if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels()))
			{
				return 1;
			}

			if(MECAccess::allowUserModerate($user, ItemsStore::getSection($app->input->getInt('section_id')), 'allow_delete'))
			{
				return 1;
			}
		}

		return 0;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$subscr = FALSE;

		if(isset($value['subscriptions']))
		{
			$subscr = $value['subscriptions'];
			unset($value['subscriptions']);
		}

		$result = $this->_getPrepared($value);

		if($subscr)
		{
			$result['subscriptions'] = $subscr;
		}

		return $result;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		$files = $this->_getPrepared($value);

		$out = array();
		settype($files, 'array');
		foreach($files as $file)
		{
			$out[] = $file['realname'];
		}

		return implode(', ', $out);
	}

	public function onStoreValues($validData, $record)
	{
		settype($this->value, 'array');
		$out = $saved = array();
		foreach($this->value as $key => $file)
		{
			if(!JString::strcmp($key, 'subscriptions'))
			{
				continue;
			}
			$out[]   = $file['realname'];
			$saved[] = $file['id'];
		}

		$files = JTable::getInstance('Files', 'CobaltTable');
		$files->markSaved($saved, $validData, $this->id);

		return $out;
	}

	protected function _getPrepared($array)
	{
		static $data = array();

		if(empty($array))
		{
			return NULL;
		}

		settype($array, 'array');

		$key = md5(implode(',', $array));

		if(isset($data[$key]))
		{
			return $data[$key];
		}

		$files      = JTable::getInstance('Files', 'CobaltTable');
		$array      = $files->prepareSave($array);

		$data[$key] = json_decode($array, TRUE);
		foreach($data[$key] AS &$file)
		{
			unset($file['params']);
		}

		return $data[$key];

	}

	public function onBeforeDownload($record, $file_index, $file_id, $return = TRUE)
	{
		$user = JFactory::getUser();
		if(!in_array($this->params->get('params.allow_download', 1), $user->getAuthorisedViewLevels()))
		{
			$this->setError(JText::_("CNORIGHTSDOWNLOAD"));

			return FALSE;
		}

		if($this->_ajast_subscr($record))
		{
			$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
			if(!JFile::exists($em_api))
			{
				return TRUE;
			}

			if(in_array($this->params->get('params.subscr_skip', 3), $user->getAuthorisedViewLevels()))
			{
				return TRUE;
			}

			if($this->params->get('params.subscr_skip_author', 1) && $record->user_id && ($record->user_id == $user->id))
			{
				return TRUE;
			}
			$section = ItemsStore::getSection($record->section_id);
			if($this->params->get('params.subscr_skip_moderator', 1) && MECAccess::allowRestricted($user, $section))
			{
				return TRUE;
			}

			include_once($em_api);

			if($this->_is_subscribed($this->_ajast_subscr($record), false))
			{
				return TRUE;
			}

			$result = JText::_($this->params->get('params.subscription_msg'));
			$result .= sprintf('<br><small><a href="%s">%s</a></small>',
				EmeraldApi::getLink('list', true, $this->_ajast_subscr($record)),
				JText::_('CSUBSCRIBENOW')
			);

			$this->setError($result);

			return FALSE;
		}

		return $return;
	}

	public function _is_subscribed($plans, $redirect)
	{
		require_once JPATH_ROOT . '/components/com_emerald/api.php';

		return EmeraldApi::hasSubscription(
			$plans,
			$this->params->get('params.subscription_msg'),
			null,
			$this->params->get('params.subscription_count'),
			$redirect);
	}

	public function _ajast_subscr($record)
	{
		if(!$record->user_id)
		{
			return;
		}

		$user = JFactory::getUser($record->user_id);

		if(in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()) &&
			$this->params->get('params.subscription')
		)
		{
			$subscr = $this->subscriptions;
		}
		else
		{
			$subscr = $this->params->get('params.subscription');
		}

		ArrayHelper::clean_r($subscr);

		return $subscr;
	}

	public function onCopy($value, $record, $type, $section, $field)
	{
		if(!empty($value))
		{
			foreach($value AS $key => $file)
			{
				$value[$key] = $this->copyFile($file, $field);
			}
		}

		return $value;
	}

	protected function getFiles($record, $show_hits = FALSE)
	{
		$list = $this->value;

		$subfolder = $this->params->get('params.subfolder', FALSE);

		if(!$list)
		{
			return array();
		}

		if(is_string($list))
		{
			$list = json_decode($list);
		}

		$files = JTable::getInstance('Files', 'CobaltTable');

		if(!is_array(@$list[0]))
		{
			$list      = $files->getFiles($list, 'filename');
			$show_hits = FALSE;
		}

		if($show_hits)
		{
			$in = array();
			foreach($list as $attach)
			{
				settype($attach, 'array');
				$in[] = $attach['id'];
			}

			if($in)
			{
				$list = $files->getFiles($in);
			}
		}
		foreach($list as $idx => &$file)
		{
			if(is_array($file))
			{
				$file = JArrayHelper::toObject($file);
			}
			if($this->params->get('params.show_in_browser', 0) == 0)
			{
				$file->url = $this->getDownloadUrl($record, $file, $idx);
			}
			else
			{
				$file->url = JURI::root(TRUE) . '/' . JComponentHelper::getParams('com_cobalt')->get('general_upload') . '/' . $subfolder . '/' . str_replace('\\', '/', $file->fullpath);
			}
			$file->subfolder = $subfolder ? $subfolder : $file->ext;
		}

		$sort = $this->params->get('params.sort', 0);

		$parts = explode(' ', $sort);
		if(!isset($parts[0]))
		{
			$parts[0] = 0;
		}

		if(!isset($parts[1]))
		{
			$parts[1] = 'ASC';
		}
		$sortArray = array();
		switch($parts[0])
		{
			case 0:
				$title = $this->params->get('params.allow_edit_title', 0);
				foreach($list as $val)
				{
					$sortArray[] = strtolower($title && $val->title ? $val->title : $val->realname);
				}
				natcasesort($sortArray);
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;

			case 1:
				foreach($list as $val)
				{
					$sortArray[] = $val->size;
				}
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;

			case 2:
				foreach($list as $val)
				{
					$sortArray[] = $val->hits;
				}
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;
			case 3:
				foreach($list as $val)
				{
					$sortArray[] = $val->id;
				}
				array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
				break;
		}


		return $list;
	}

	protected function getDownloadUrl($record, $file, $idx)
	{
		if(empty($record))
		{
			return;
		}
		$url = JURI::root(TRUE) . '/index.php?option=com_cobalt&task=files.download&tmpl=component';
		$url .= '&id=' . $file->id;
		$url .= '&fid=' . $this->id;
		$url .= '&fidx=' . $idx;
		$url .= '&rid=' . $record->id;
		$url .= '&return=' . Url::back();

		return $url;
	}

	/**
	 *
	 * @param string $filename Value from column 'filename' in table #__js_res_files
	 *
	 * @return string Filename of copied file
	 */

	protected function copyFile($filename, $field)
	{
		$params      = JComponentHelper::getParams('com_cobalt');
		$files_table = JTable::getInstance('Files', 'CobaltTable');
		if($files_table->load(array('filename' => $filename)))
		{
			$time = time();
			//$date = date('Y-m', $time);
			$date      = date($params->get('folder_format', 'Y-m'), $time);
			$ext       = strtolower(JFile::getExt($filename));
			$subfolder = $field->params->get('params.subfolder', $ext);

			$dest  = JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR;
			$index = '<html><body></body></html>';
			if(!JFolder::exists($dest))
			{
				JFolder::create($dest, 0755);
				JFile::write($dest . DIRECTORY_SEPARATOR . 'index.html', $index);
			}
			$dest .= $date . DIRECTORY_SEPARATOR;
			if(!JFolder::exists($dest))
			{

				JFolder::create($dest, 0755);
				JFile::write($dest . DIRECTORY_SEPARATOR . 'index.html', $index);
			}

			$files_table->id       = NULL;
			$parts                 = explode('_', $filename);
			$files_table->filename = $time . '_' . $parts[1];

			$copied = JFile::copy(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $files_table->fullpath, $dest . $files_table->filename);

			$files_table->fullpath = JPath::clean($date . DIRECTORY_SEPARATOR . $files_table->filename, '/');
			$files_table->saved    = 0;

			if(!$copied)
			{
				return FALSE;
			}
			if(!$files_table->store())
			{
				return FALSE;
			}

			return $files_table->filename;
		}

		return FALSE;
	}

	// * функция сохраняет позицию файлов в базе данных
	public function onSaveOrder()
	{
		$app = JFactory::getApplication();
		$file_list_order = json_decode($app->input->getString('file_list_order', 0));

		if ($file_list_order)
		{
			// * подключение к базе
			$db = JFactory::getDbo();

			foreach ($file_list_order as $file) {
				// * ловим ошибки
				try {
					// * начало SQL транзакции
					$db->transactionStart();

					// * новый запрос
					$query = $db->getQuery(true);

					// * меняем поле order
					$fields = array(
						$db->quoteName('order') . '=' . $file->order
					);
					// * выделяем запись по id
					$conditions = array(
						$db->quoteName('id') . ' = ' . $file->id
					);
					// * формируем запрос
					$query->update($db->quoteName('#__js_res_files'))
						->set($fields)
						->where($conditions);

					// * отправляем запрос
					$db->setQuery($query);
					$db->execute();

					// * конец SQL транзакции
					$db->transactionCommit();
				}
				// * в случае ошибки
				catch (Exception $error)
				{
					// * откат вносимых изменений в базу
					$db->transactionRollback();
					AjaxHelper::error($error);
				}
			}

		}

	}

	public function onSaveTitle()
	{
		$app = JFactory::getApplication();

		$id        = $app->input->getInt('id', 0);
		$text      = CensorHelper::cleanText($app->input->getString('text'));
		$record_id = $app->input->getInt('record_id', 0);
		$field_id  = $app->input->getInt('field_id', 0);
		if($record_id && $field_id)
		{
			$record_table = JTable::getInstance('Record', 'CobaltTable');
			$record_table->load($record_id);
			$fields = json_decode($record_table->fields, TRUE);

			if(isset($fields[$field_id]))
			{
				$files = & $fields[$field_id];
				if(isset($fields[$field_id]['files']))
				{
					$files = & $fields[$field_id]['files'];
				}

				foreach($files as &$file)
				{
					if($file['id'] == $id)
					{
						$file['title'] = $text;
						break;
					}
				}
				$record_table->fields = json_encode($fields);
				$record_table->store();
			}

		}
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__js_res_files SET title = '".$db->escape($text)."' WHERE id = {$id}");

		if(!$db->execute())
		{
			AjaxHelper::error('DB save error');
		}

		return $text;
	}

	// * функция замены изображения
	public function replaceFile()
	{
		jimport('joomla.filesystem.file');

		include_once JPATH_ROOT . '/components/com_cobalt/controllers/files.php';
		$controller = new CobaltControllerFiles();

		$params = JComponentHelper::getParams('com_cobalt');
		$general_upload = $params->get('general_upload');
		$subfolder = $this->params->get('params.subfolder', FALSE);

		$app = JFactory::getApplication();

		$field_id = $app->input->getInt('field_id', 0);
		$old_file = json_decode($app->input->getJson('old_file'));
		$old_file_id = $old_file->id;
		$old_file_fullpath = $old_file->fullpath;

		$file  = $app->input->files->get('file', '', 'files', 'array');
		$file['name'] = JFile::makeSafe($file['name']);

		$ext = JString::strtolower(JFile::getExt($file['name']));
		$new_name = JFactory::getDate($record->ctime)->toUnix() . '_' . md5($file['name']) . '.' . $ext;

		$date = date($params->get('folder_format', 'Y-m'));

		$new_path = $general_upload . '/' . $subfolder . '/' . $date . '/' . $new_name;

		$filepath = JPath::clean( $new_path );

		$result = JFile::upload( $file['tmp_name'], $filepath );

		if ($result)
		{

			// * размер картинки
			$image_properties = JImage::getImageFileProperties($new_path);

			// * EXIF параметры
			if(function_exists('exif_read_data') && in_array(strtolower($ext),
					array(
						'jpg',
						'jpeg',
						'ttf'
					))
			)
			{
				$image_params_json = json_encode(@exif_read_data(JPath::clean($src)));
			}

			$db = JFactory::getDbo();
			// * ловим ошибки
			try {
				// * начало SQL транзакции
				$db->transactionStart();

				// * новый запрос - #__js_res_files
				$query = $db->getQuery(true);

				// * меняем поля
				$fields = array(
					$db->quoteName('filename') . '=' . $db->quote($new_name),
					$db->quoteName('realname') . '=' . $db->quote($file['name']),
					$db->quoteName('ctime') . '=' . $db->quote(date('Y-m-d H:i:s')),
					$db->quoteName('ext') . '=' . $db->quote($ext),
					$db->quoteName('fullpath') . '=' . $db->quote($date . '/' . $new_name),
					$db->quoteName('size') . '=' . $db->quote($file['size']),
					$db->quoteName('width') . '=' . $db->quote($image_properties->width),
					$db->quoteName('height') . '=' . $db->quote($image_properties->height),
					$db->quoteName('params') . '=' . $db->quote($image_params_json),
				);
				// * выделяем запись по id
				$conditions = array(
					$db->quoteName('id') . ' = ' . $old_file_id
				);
				// * формируем запрос
				$query->update($db->quoteName('#__js_res_files'))
					->set($fields)
					->where($conditions);

				// * отправляем запрос
				$db->setQuery($query);
				$db->execute();

				// * новый запрос на получение id страницы - #__js_res_record
				$query = $db->getQuery(true);

				$query
					->select(array('id, fields'))
					->from($db->quoteName('#__js_res_record'))
					->where($db->quoteName('fields') . ' LIKE ' . $db->quote('%'.$old_file->filename.'%'));

				// * отправляем запрос
				$db->setQuery($query);
				$db->execute();

				$js_res_record = $db->loadRowList()[0];
				if ($js_res_record)
				{
					$js_res_record_id = $js_res_record[0];
					// * заменяем старое понлый путь к файлу на новый
					$js_res_record_fields = str_replace(str_replace('/', '\/', $old_file_fullpath), $date . '\/' . $new_name, $js_res_record[1]);
					// * заменяем старое имя файла на новое
					$js_res_record_fields = str_replace($old_file->filename, $new_name, $js_res_record_fields);

					// * новый запрос на запись страницы - #__js_res_record
					$query = $db->getQuery(true);

					// * меняем поля
					$fields = array(
						$db->quoteName('fields') . '=' . $db->quote($js_res_record_fields),
					);
					// * выделяем запись по id
					$conditions = array(
						$db->quoteName('id') . ' = ' . $js_res_record_id
					);
					// * формируем запрос
					$query->update($db->quoteName('#__js_res_record'))
						->set($fields)
						->where($conditions);

					// * отправляем запрос
					$db->setQuery($query);
					$db->execute();
				}

				// * новый запрос на получение id страницы - #__js_res_notifications
				$query = $db->getQuery(true);

				$query
					->select(array('id, params'))
					->from($db->quoteName('#__js_res_notifications'))
					->where($db->quoteName('params') . ' LIKE ' . $db->quote('%'.$old_file->filename.'%'));

				// * отправляем запрос
				$db->setQuery($query);
				$db->execute();

				$js_res_notifications = $db->loadRowList();

				if ($js_res_notifications)
				{
					foreach ($js_res_notifications as $params) {

						$id = $params[0];
						$params = $params[1];

						// * заменяем старое полный путь к файлу на новый
						$params = str_replace(str_replace('/', '\\\\\\\\\\\\\/', $old_file_fullpath), $date . '\\\\\\\\\\\\\/' . $new_name, $params);
						// * заменяем старое имя файла на новое
						$params = str_replace($old_file->filename, $new_name, $params);

						// * новый запрос на запись страницы - #__js_res_record
						$query = $db->getQuery(true);

						// * меняем поля
						$fields = array(
							$db->quoteName('params') . '=' . $db->quote($params),
						);
						// * выделяем запись по id
						$conditions = array(
							$db->quoteName('id') . ' = ' . $id
						);
						// * формируем запрос
						$query->update($db->quoteName('#__js_res_notifications'))
							->set($fields)
							->where($conditions);

						// * отправляем запрос
						$db->setQuery($query);
						$db->execute();

					}
				}

				// * конец SQL транзакции
				$db->transactionCommit();

				// * удаляем устарый файл
				$remove_file = $general_upload . '/' . $subfolder . '/' . $old_file_fullpath;
				if (JFile::exists($remove_file))
				{
					JFile::delete($remove_file);
				}
			}
			// * в случае ошибки
			catch (Exception $error)
			{
				// * откат вносимых изменений в базу
				$db->transactionRollback();
				AjaxHelper::error('Ошибка загрузки файла: ' . $error);
			}

			// * отправляем обратно путь к новой картинке
            $result = json_encode((object) [
                'old_fullpath' => $general_upload . '/' . $subfolder . '/' . $old_file_fullpath,
                'new_fullpath' => $new_path,
                'new_filename' => $new_name,
				'new_realname' => $file['name'],
				'new_size' => $file['size'],
				'new_width' => $image_properties->width,
				'new_height' => $image_properties->height,
            ]);

			AjaxHelper::send($result);
		}
		else
		{
			AjaxHelper::error('Ошибка загрузки файла.');
		}
	}

	public function onSaveDescr()
	{
		$app        = JFactory::getApplication();
		$id         = $app->input->getInt('id', 0);
		$text       = CensorHelper::cleanText($app->input->getString('text',   $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text1      = CensorHelper::cleanText($app->input->getString('text1',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text2      = CensorHelper::cleanText($app->input->getString('text2',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text3      = CensorHelper::cleanText($app->input->getString('text3',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text4      = CensorHelper::cleanText($app->input->getString('text4',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text5      = CensorHelper::cleanText($app->input->getString('text5',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text6      = CensorHelper::cleanText($app->input->getString('text6',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text7      = CensorHelper::cleanText($app->input->getString('text7',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text8      = CensorHelper::cleanText($app->input->getString('text8',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$text9      = CensorHelper::cleanText($app->input->getString('text9',  $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$gradus     = CensorHelper::cleanText($app->input->getString('text10', $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$gurojaya     = CensorHelper::cleanText($app->input->getString('text11', $default = NULL, $hash = 'default', $type = 'none', $mask = 4));
		$record_id  = $app->input->getInt('record_id', 0);
		$field_id   = $app->input->getInt('field_id', 0);
		if($record_id && $field_id)
		{
			$record_table = JTable::getInstance('Record', 'CobaltTable');
			$record_table->load($record_id);
			$fields = json_decode($record_table->fields, TRUE);

			if(isset($fields[$field_id]))
			{
				$files = & $fields[$field_id];
				if(isset($fields[$field_id]['files']))
				{
					$files = & $fields[$field_id]['files'];
				}

				foreach($files as &$file)
				{
					if($file['id'] == $id)
					{
						$file['description'] = $text;
						$file['vcolor'] = $text1;
						$file['vsugar'] = $text2;
						$file['vsort'] = $text3;
						$file['vvinogrd'] = $text4;
						$file['vproizvod'] = $text5;
						$file['vorglept'] = $text6;
						$file['vgastronom'] = $text7;
						$file['vtemp'] = $text8;
						$file['vkisl'] = $text9;
						$file['gradus'] = $gradus;
						$file['gradus'] = $gradus;
						$file['gurojaya'] = $gurojaya;
						break;
					}
				}
				$record_table->fields = json_encode($fields);
				$record_table->store();
			}

		}
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__js_res_files SET gurojaya ='{$gurojaya}', vcolor = '{$text1}', vsugar = '{$text2}', vsort = '{$text3}', vvinogrd   = '{$text4}', vproizvod  = '{$text5}', vorglept = '{$text6}', vgastronom = '{$text7}', vtemp = '{$text8}', vkisl = '{$text9}', gradus ='{$gradus}', description = '{$text}' WHERE id = {$id}");

		if(!$db->execute())
		{
			AjaxHelper::error('DB save error');
		}
		return 'gurojaya:'.$gurojaya.', vcolor:'.$text1.', vsugar:'.$text2.', vsort:'.$text3.', vvinogrd:'.$text4.', vproizvod:'.$text5.', vorglept:'.$text6.', vgastronom:'.$text7.', vtemp:'.$text8.', vkisl:'.$text9.', gradus:'.$gradus.', description:'.$text;
	}


  public function loadSaveDescr() { // Запрос к файлам продуктов галлереи

		$app       = JFactory::getApplication();
		$id        = $app->input->getInt('id', 0);
		$record_id = $app->input->getInt('record_id', 0);
		$field_id  = $app->input->getInt('field_id', 0);
		$db  = JFactory::getDBO();
		$sql = "SELECT id, field_id, title, gurojaya, vcolor, vsugar, vsort, vvinogrd, vproizvod, vorglept, vgastronom, vtemp, vkisl, gradus, description  FROM #__js_res_files WHERE id = '{$id}' AND field_id = '{$field_id}'";
		$db->setQuery($sql);
		$list = $db->loadObjectList();
		return $list;

	}









	public function onImportData($row, $params)
	{
		return $row->get($params->get('field.' . $this->id . '.fname'));
	}

	public function onImport($value, $params, $record = null)
	{
		$values = explode($params->get('field.' . $this->id . '.separator', ','), $value);
		ArrayHelper::clean_r($values);

		$files = array();
		include_once JPATH_ROOT . '/components/com_cobalt/controllers/files.php';
		$controller = new CobaltControllerFiles();

		$default = $this->value;
		if(array_key_exists('files', $default))
		{
			$default = $default['files'];
		}
		settype($default, 'array');

		foreach($values AS $file)
		{

			$exists = FALSE;
			foreach($default AS $f)
			{
				if(basename($file) == $f['realname'])
				{
					$files[] = $f['filename'];
					$exists = TRUE;
				}
			}

			if($exists)
			{
				continue;
			}

			$ext        = JString::strtolower(JFile::getExt($file));
			$new_name   = JFactory::getDate($record->ctime)->toUnix() . '_' . md5($file) . '.' . $ext;

			$file = $this->_find_import_file($params->get('field.' . $this->id . '.path'), $file);
			if(!$file)
			{
				continue;
			}

			$sub_folder = $this->params->get('params.subfolder', $this->field_type);

			if(!$controller->savefile(basename($file), $new_name, $sub_folder, $file, $record->id, $record->section_id, $record->type_id, $this->id))
			{
				continue;
			}

			$files[] = $new_name;
		}


		if(empty($files))
		{
			return NULL;
		}

		$return = $this->_getPrepared($files);

		if($this->type == 'paytodownaload' || $this->type == 'video')
		{
			$out['files'] = $return;
		}
		else
		{
			$out = $return;
		}

		return $out;
	}

	public function onImportForm($heads, $defaults)
	{
		$out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][separator]" value="%s" class="span2" >',
			JText::_('CMULTIVALFIELDSEPARATOR'), $this->id, $defaults->get('field.' . $this->id . '.separator', ','));
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][path]" value="%s" class="span12" >',
			JText::_('CFILESPATH'), $this->id, $defaults->get('field.' . $this->id . '.path', 'files'));

		return $out;
	}

	public function validate($value, $record, $type, $section)
	{
		$jform = $this->request->get('jform', array(), 'array');
		if($this->required && !isset($jform['fields'][$this->id]))
		{
			$jform['fields'][$this->id] = '';
			$this->request->set('jform', $jform);
		}
		$jform = $this->request->get('jform', array(), 'array');

		parent::validate($value, $record, $type, $section);
	}
}
