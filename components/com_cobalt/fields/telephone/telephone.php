<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_cobalt/library/php/fields/cobaltfield.php';


class JFormFieldCTelephone extends CFormField
{
	public function getInput()
	{
		$document = JFactory::getDocument();
// 		$document->addScript(JURI::root(TRUE) . '/media/mint/js/autocomplete/mootools-autocompleter-1.2.js');
// 		$document->addStyleSheet(JURI::root(TRUE) . '/media/mint/js/autocomplete/autocompleter.css');
		$document->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/fields/telephone/telephone.css');
		$document->addScript(JURI::root(TRUE) . '/components/com_cobalt/fields/telephone/telephone.js');


		$value = $this->value ? $this->value : null;
		$this->flag = $this->_getFlag($value);

		return $this->_display_input();
	}



	public function validate($value, $record, $type, $section)
	{
		if($this->required && ( $value['tel'] == ''))
		{
				$this->setError(JText::sprintf('CFIELDREQUIRED', $this->label));
				return FALSE;
		}
		
		return parent::validate($value, $record, $type, $section);
	}

	

	public function onStoreValues($validData, $record)
	{
		if(!$this->value) return;
		$v = $this->value;
		//if($v['country'] == '' ) return ;
// 		if ($v['ext'] != '')
// 		{
// 			$v['ext'] = '#' . $v['ext'];
// 		}
		return implode('.', $this->value);
	}

	public function onFilterWornLabel($section)
	{
		$val = explode('.', $this->value);
		$value['country'] = $val[0];
		$value['region'] = @$val[1];
		$value['tel'] = @$val[2];
		$value['ext'] = @$val[3];

		$val = $this->_getFormated($value);
		return $val;
	}

	public function onFilterWhere($section, &$query)
	{
		$ids = $this->getIds("SELECT record_id FROM #__js_res_record_values WHERE field_value = '{$this->value}' AND section_id = {$section->id} AND field_key = '{$this->key}'");
		return $ids;
	}

	public function onRenderFilter($section, $module = false)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/fields/telephone/telephone.css');
		$document->addScript(JURI::root(TRUE) . '/components/com_cobalt/fields/telephone/telephone.js');

		$val = '';
		if ($this->value)
		{
			$val = explode('.', $this->value);
			$value['country'] = $val[0];
			$value['region'] = $val[1];
			$value['tel'] = $val[2];
			$value['ext'] = $val[3];

			$val = $this->_getFormated($value);
		}
		$this->formated_value = $val;

		return $this->_display_filter($section, $module);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		$v = $value;
		if($v['country'] == '' ) return ;

		return implode('.', $v);
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($client, $record, $type, $section )
	{
		if(!$this->value) return ;
		JFactory::getDocument()->addStyleSheet(JURI::root(TRUE) . '/components/com_cobalt/fields/telephone/telephone.css');

		$value = $this->value;
		$f_value = '';
		if (is_array($value))
		{
			$f_value = implode('.', $value);

			$this->qrvalue = urlencode(JText::sprintf('+%d%d%d%s', $value['country'], $value['region'], $value['tel'], isset($value['ext']) ? $value['ext'] : ''));

			$value = $this->_getFormated($value);
		}
		if ($this->params->get('params.filter_enable'))
		{
			$tip = ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $value . '</b>') : NULL);

			switch ($this->params->get('params.filter_linkage'))
			{
				case 1 :
					$value = FilterHelper::filterLink('filter_' . $this->id, $f_value, $value, $this->type_id, $tip, $section);
					break;

				case 2 :
					$value = $value . ' ' . FilterHelper::filterButton('filter_' . $this->id, $f_value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
					break;
			}
		}

		$this->phone = $value;

		return $this->_display_output($client, $record, $type, $section);
	}

	private function _getFlag($value)
	{
		if (!$value)
			return;
		$db = JFactory::getDbo();
		$code1 = $value['country'] . '-' . $value['region'];
		$code2 = $value['country'];
		$sql = "SELECT * FROM `#__js_res_field_telephone` where phone_code = '$code1'";
		$db->setQuery($sql);
		if (!$res = $db->loadObject())
		{
			$sql = "SELECT * FROM `#__js_res_field_telephone` where phone_code = '$code2'";
			$db->setQuery($sql);
			$res = $db->loadObject();
		}
		return $res;
	}

	public function onGetCountriesCode($post)
	{
		$db = JFactory::getDbo();
		$word = $this->request->getWord('q');
		$code = $this->request->getInt('q');
		if ($code)
			$where[] = "phone_code like '$code%'";
		if ($word)
			$where[] = "name like '%$word%'";
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from('#__js_res_field_telephone');
		$query->where(implode(' OR ', $where));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$data = array();
		foreach($result as $k => $val)
		{
			$data[$k] = new stdClass();
			$data[$k]->label = JText::_($val->name).' (+' . JText::_($val->phone_code) . ')';//$val->phone_code;
			$data[$k]->value = $val->phone_code;

			$data[$k]->flag = '';
			if(JFile::exists(JPATH_ROOT.'/media/mint/icons/flag/16/' . JString::strtolower($val->code2) . '.png'))
			{
				$data[$k]->flag = '<img src="' . JURI::root(TRUE) . '/media/mint/icons/flag/16/' . JString::strtolower($val->code2) . '.png" border="0" align="absmiddle" alt="' . JText::_($val->name) . '">';
			}
		}
		return $data;
	}

	public function onFilterData($post, $record)
	{
		$db = JFactory::getDbo();
		$q = $this->request->get('q');
		$q = $db->escape($q);

		$field = $this->request->get('field');
		$section = $this->request->get('section');
		$where = "REPLACE(field_value, '.', '' ) like '%$q%' AND type_id = '{$this->type_id}' AND section_id = '$section' AND field_type = '$field'";
		$query = $db->getQuery(true);
		$query->select("*, COUNT(record_id) as num");
		$query->from('#__js_res_record_values');
		$query->where($where);
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if(!$result[0]->id)
		{
			$result = array();
		}
		$data = array();
		foreach($result as $k => $val)
		{
			//$data[$k]->label = $val->field_value;
			$data[$k]->value = $val->field_value;
			$vals = explode('.', $val->field_value);
			ArrayHelper::clean_r($vals);
			$value['country'] = @$vals[0];
			$value['region'] = @$vals[1];
			$value['tel'] = @$vals[2];
			$value['ext'] = @$vals[3];

			$data[$k]->value = $val->field_value;
 			$data[$k]->label .= $this->_getFormated($value).($this->params->get('params.filter_show_number') ? " <span class=\"badge\">{$val->num}</span>" : '');
		}
		return $data;
	}

	private function _getFormated($value)
	{
		$phone = '';


		if(!empty($value['country']))
		{
			$phone .= str_replace('[country]', $value['country'], $this->params->get('params.pattern_country', '+[country]'));
		}
		if(!empty($value['region']))
		{
			$phone .= str_replace('[region]', $value['region'], $this->params->get('params.pattern_area', ' ([region])'));
		}
		if(!empty($value['tel']))
		{
			$phone .= str_replace('[tel]', $value['tel'], $this->params->get('params.pattern_tel', ' [tel]'));
		}
		if(!empty($value['ext']))
		{
			$phone .= str_replace('[ext]', $value['ext'], $this->params->get('params.pattern_ext', '+[ext]'));
		}
if(!empty($value['ext'])){
		return '<div>
		<svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
		<a class="phoneout" href="tel:' . substr_replace( preg_replace( "/[^0-9]/" , '' , $value['tel'] ) , '+7' , 0 , 1 ) .'">'. $value['tel'] .'</a>
		'.((!empty($value['tinfo_1'])) ? '<i style="color: #666; margin-left: 5px;">('.htmlspecialchars($value['tinfo_1']).')</i>' : '').'</div>
		
		<div>
		<svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg><a class="phoneout" href="tel:' . substr_replace( preg_replace( "/[^0-9]/" , '' , $value['ext'] ) , '+7' , 0 , 1 ) .'">'. $value['ext'] .'</a>'.((!empty($value['tinfo_2'])) ? '<i style="color: #666; margin-left: 5px;">('.htmlspecialchars($value['tinfo_2']).')</i>' : '').'
		</div>';

	}
	  else if(empty($value['ext']))
		{
			return '<div>
		<svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
		<a class="phoneout" href="tel:' . substr_replace( preg_replace( "/[^0-9]/" , '' , $value['tel'] ) , '+7' , 0 , 1 ) .'">'. $value['tel'] .'</a>'.((!empty($value['tinfo_1'])) ? '<i style="color: #666; margin-left: 5px;">('.htmlspecialchars($value['tinfo_1']).')</i>' : '').'
		</div>';
		}
	
	}//$phone2 = $value['ext'];
}
