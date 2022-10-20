<?php

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemAutounlock extends JPlugin
{
	// текущее время
	protected $time_now;

	// время, заданное в настройках плагина (по умолчанию 24 часа)
	protected $time_max;

	// заданное время $time_max в секундах, для сравнения со временем блокировки записи
	protected $time_sec;

	// ID группы текущего пользователя
	protected $user_group_id;

	// true - если на посетителя будет реагировать плагин, иначе false
	protected $user_group_check;

	// true - если зона доступа (сайт или админка) совпадает с разрешенной, иначе false
	protected $area_check;

	public function __construct(&$subject, $config = array())
	{
		// подгрузка параметров плагина
		if ($config['params'] instanceof JRegistry)
		{
			$this->params = $config['params'];
		}
		else
		{
			$this->params = new JRegistry;
			$this->params->loadString($config['params']);
		}

		// устанавливаем текущее время
		$this->time_now = JFactory::getDate('now');
		// устанавливаем время, заданное в настройках плагина (по умолчанию 24 часа)
		$this->time_max = $this->params->get('time_max', 24);
		// устанавливаем заданное время в секундах
		$this->time_sec = $this->time_max * 3600;

		// устанавливаем ID группы текущего пользователя
		$this->user_group_id = JFactory::getUser()->get('groups');
		// ищем ID группы текущего пользователя в списке активных, на кого сработает плагин
		$this->user_group_check = array_uintersect($this->params->get('usergroups', null), $this->user_group_id, 'strcasecmp') ? true : false;

		// проверяем разрешенную зону доступа
		$app = JFactory::getApplication();
		$this->area_check = (
			// если выбраны обе зона
			($this->params->get('area', 'administrator') === 'both') ||
			// если выбрано только админка
			($app->getName() == 'administrator' && $this->params->get('area', 'administrator') === 'administrator') ||
			// если выбрано только сайт
			($app->getName() == 'site' && $this->params->get('area', 'administrator') === 'site')
		) ? true : false;

	}

	public function onAfterRender()
	{
		// проверка на доступ к зоне сайта или группы пользователя
		if (!$this->area_check || !$this->user_group_check)
		{
			// прекращаем работу и ничего дальше не делаем
			return true;
		}

		// ну а если все разрешения получены и все 7 кругов ада пройдены...
		// делаем запрос к базе
		$db = JFactory::getDBO();
		$query = 'SELECT id, checked_out, checked_out_time FROM #__js_res_record WHERE checked_out > 0';
		$db->setQuery($query);
		$result = $db->loadObjectList();
		foreach ($result as $key => $item)
		{
			// сколько времени прошло с момента блокировки в секундах
			$timer = strtotime($this->time_now) - strtotime($item->checked_out_time);
			echo $timer . ' <br>';
			// если прошедшее время больше установленного в настройках - пора убирать блокировку
			if ($timer >= $this->time_sec)
			{
				// снимаем блокировку с записи
				$query = 'UPDATE #__js_res_record SET checked_out=0, checked_out_time="0000-00-00 00:00:00" WHERE id = ' . $item->id;
				$db->setQuery($query);
				$final = $db->query();
			}
		}

		// прекращаем работу и ничего дальше не делаем
		return true;
	}

}
