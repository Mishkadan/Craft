<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();


$out = $contacts = $links = $address = array();
$client = ($client == 'list') ? 1 : 2;
$links = JFormFieldCgeo::getAditionalinks();
$contacts = JFormFieldCgeo::getAditionalFields();
$address = JFormFieldCgeo::getAddressFields();
$app = JFactory::getApplication();
$sho_map = in_array($this->params->get('params.map_client'), array($client, 3))	&& in_array($this->params->get('params.map_view'), $this->user->getAuthorisedViewLevels());
$sho_sv = in_array($this->params->get('params.sv_client'), array($client, 3))	&& in_array($this->params->get('params.sv_view'), $this->user->getAuthorisedViewLevels());
$height = 100;
if($sho_map && $sho_sv && $this->params->get('params.sv_layout') == 0)
{
	$height = 50;
}

if($this->request->get('func') == 'onInfoWindow')
{
	$this->params->set('params.map_view',0);
}
$a = $c = $l = array();
if(in_array($this->params->get('params.adr_view'), $this->user->getAuthorisedViewLevels())):?>
	<?php if(! empty($this->value['address'])):
		ArrayHelper::clean_r($this->value['address']);
		foreach($this->value['address'] as $name => $link)
		{
			if($name == 'country')
			{
				$this->value['address'][$name] = $this->_getcountryname($link);
			}
			if(! in_array($this->params->get("params.address.{$name}.show", 3), array($client, 3)))
				unset($this->value['address'][$name]);
		}

		if(! empty($this->value['address']['company']))
		{
			$a[] = '<strong>' . $this->value['address']['company'] . '</strong><br>';
			$mecard['n'] = 'N:' . $this->value['address']['company'];
			unset($this->value['address']['company']);
		}
		elseif(! empty($this->value['address']['person']))
		{
			$a[] = '<strong>' . $this->value['address']['person'] . '</strong><br>';
			$mecard['n'] = 'N:' . $this->value['address']['person'];
			unset($this->value['address']['person']);
		}

		if(! empty($this->value['address']))
		{
			$ordered = array();
			foreach(array('address1', 'address2', 'city', 'state', 'zip', 'country') as $key)
			{
				if(array_key_exists($key, $this->value['address']))
				{
					$ordered[$key] = $this->value['address'][$key];
					unset($this->value['address'][$key]);
				}
			}

			$ordered = $ordered + $this->value['address'];
			$a[] = implode(", ", $ordered);
			$mecard[] = 'ADR:' . implode(", ", $ordered);
			$this->value['address'] = $ordered;
		}
	endif;

	if(! empty($this->value['contacts'])):
		ArrayHelper::clean_r($this->value['contacts']);
		foreach($this->value['contacts'] as $name => $contact)
		{
			if(empty($contacts[$name])) continue;
			if(! in_array($this->params->get("params.contacts.{$name}.show", 3), array($client, 3)))
				continue;
			$text = $contact;
			if(! empty($contacts[$name]['patern']))
			{
				$text = str_replace(array('[VALUE]', '[NAME]'), array($contact,	JText::_($contacts[$name]['label'])), $contacts[$name]['patern']);
			}
			$c[] = '<abbr title="'.JText::_($contacts[$name]['label']).'" rel="tooltip" data-original-title="'.JText::_($contacts[$name]['label']).'">
				<img src="'.str_replace(array('[VALUE]', '[NAME]'), array($contact, JText::_($contacts[$name]['label'])), $contacts[$name]['icon']).'">
				 '.JString::substr(JText::_($contacts[$name]['label']), 0, 1).':</abbr> '.$text;
		}
	endif;

	if(! empty($this->value['links'])) :
		ArrayHelper::clean_r($this->value['links']);
		foreach($this->value['links'] as $name => $link)
		{
			if(! in_array($this->params->get("params.links.{$name}.show", 3), array($client,  3)))
				continue;
			if(in_array(trim($link), array('http://', 'http:')))
				continue;
			$l[] = '<abbr title="'.JText::_($links[$name]['label']).'" rel="tooltip" data-original-title="'.JText::_($links[$name]['label']).'">
				<img src="'.$links[$name]['icon'].'">'.JString::substr(JText::_($links[$name]['label']), 0, 1).':</abbr>
				<a rel="nofollow" target="_blank" href="'.$link.'">'.($this->params->get('params.links_labels') ?  $link : JText::_($links[$name]['label'])).'</a>';
		}
		?>
	<?php endif; ?>
	<?php if($a || $c || $l):  ?>
		<address id="suggest">
			<?php if($a): ?>
				<?php echo implode("\n", $a);?>
				<br><br>
			<?php endif;?>
			<?php if($c): ?>
				<?php echo implode("<br>", $c);?>
				<br>
			<?php endif;?>
			<?php if($l): ?>
				<?php echo implode("<br>", $l);?>
			<?php endif;?>
		</address>
	<?php endif;?>
<?php endif;

if(in_array($this->params->get('params.qr_code_address'), array($client, 3))):
	$w = $this->params->get('params.qr_width_address', 250);

	if(! empty($this->value['contacts']['mob']))
	{
		$mecard[] = 'TEL:' . $this->value['contacts']['mob'];
	}
	elseif(! empty($this->value['contacts']['tel']))
	{
		$mecard[] = 'TEL:' . $this->value['contacts']['tel'];
	}
	elseif(! empty($this->value['contacts']['fax']))
	{
		$mecard[] = 'TEL:' . $this->value['contacts']['fax'];
	}

	if(! empty($this->value['links']['web']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['web'];
	}
	elseif(! empty($this->value['links']['facebook']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['facebook'];
	}
	elseif(! empty($this->value['links']['twitter']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['twitter'];
	}
	elseif(! empty($this->value['links']['google']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['google'];
	}
	elseif(! empty($this->value['links']['youtube']))
	{
		$mecard['url'] = 'URL:' . $this->value['links']['youtube'];
	}

	if(empty($mecard['url']) && ! empty($this->value['links']))
	{
		$mecard['url'] = 'URL:' . array_shift($this->value['links']);
	}
	if(!empty($mecard['url']) && $mecard['url'] == 'URL:http://')
	{
		unset($mecard['url']);
	}

	if(!empty($mecard) && $this->email)
	{
		$mecard[] = 'EMAIL:' . $this->email;
	}

	if(!empty($mecard)) :
		$url = 'http://chart.apis.google.com/chart?cht=qr&chs=' . $w . 'x' . $w . '&chl=MECARD:' . implode(';', $mecard) . ';;';
		?>
		<div class="qr-image qr-image-address"><?php echo JHtml::image($url, JText::_('G_ADDRESSQR'), array('width' => $w, 'height' => $w));?></div>
	<?php endif;?>
<?php endif;


if(
	($sho_map || $sho_sv)
	&& !empty($this->value['position']['lat'])
	&& !empty($this->value['position']['lng'])
	&& $this->request->get('info_window') == 0) :
	$params = new JRegistry($this->value['position']);

	list($icon_w, $icon_h, $icon) = $this->getMarker();
	$icon_m = round($icon_w / 2);
	?>
	<?php echo $this->_title(JText::_('G_MAP'), $client);?>
	<style>
		#map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?> label { width: auto; display:inline; }
		#map_canvas_<?php echo $record->id;?>_<?php echo $this->id;?> img { max-width: none; }
	</style>

	<div id="map1" style="width: 100% !important; height: 260px; transform: translateX(0px);"></div>
	
	
	<?php if($this->params->get('params.map_lat_lng')): ?>
		<p>(<?php echo $params->get('lat');?>,<?php echo $params->get('lng');?>)</p>
	<?php endif; ?>

	<script type="text/javascript">
	
	 // Функция ymaps.ready() будет вызвана, когда
    // загрузятся все компоненты API, а также когда будет готово DOM-дерево.
    ymaps.ready(init);
    function init(){
        // Создание карты.
        var myMap = new ymaps.Map("map1", {
            // Координаты центра карты.
            // Порядок по умолчанию: «широта, долгота».
            // Чтобы не определять координаты центра карты вручную,
            // воспользуйтесь инструментом Определение координат.
            center: [55.76, 37.64],
            // Уровень масштабирования. Допустимые значения:
            // от 0 (весь мир) до 19.
            zoom: 7
        
});
		
		/*var searchControl = new ymaps.control.SearchControl({
    options: {
    // Будет производиться поиск по топонимам и организациям.
    provider: 'yandex#search'
   }
});*/
//myMap.controls.add(searchControl);
		var dev = document.getElementById('suggest');
		var dev1 = document.getElementsByTagName('h1');
		// var suggestView1 = new ymaps.SuggestView('suggest');
   
	
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
    //... отключаем перетаскивание карты
    myMap.behaviors.disable('drag');
}
	
	//console.log(dev1[0].innerHTML);
	 ymaps.geocode(dev.innerHTML, {//document.getElementById("suggest").value)
        /**
         * Опции запроса
         * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/geocode.xml
         */
        // Сортировка результатов от центра окна карты.
        // boundedBy: myMap.getBounds(),
        // strictBounds: true,
        // Вместе с опцией boundedBy будет искать строго внутри области, указанной в boundedBy.
        // Если нужен только один результат, экономим трафик пользователей.
        results: 1
    }).then(function (res) {
            // Выбираем первый результат геокодирования.
            var firstGeoObject = res.geoObjects.get(0),
                // Координаты геообъекта.
                coords = firstGeoObject.geometry.getCoordinates(),
                // Область видимости геообъекта.
                bounds = firstGeoObject.properties.get('boundedBy');

            firstGeoObject.options.set('preset', 'islands#darkBlueDotIconWithCaption');
            // Получаем строку с адресом и выводим в иконке геообъекта.
            firstGeoObject.properties.set('iconCaption', firstGeoObject.getAddressLine());

            // Добавляем первый найденный геообъект на карту.
            myMap.geoObjects.add(firstGeoObject);
            // Масштабируем карту на область видимости геообъекта.
            myMap.setBounds(bounds, {
                // Проверяем наличие тайлов на данном масштабе.
                checkZoomRange: true
            });

            /**
             * Все данные в виде javascript-объекта.
             */
           // console.log('Все данные геообъекта: ', firstGeoObject.properties.getAll());
            /**
             * Метаданные запроса и ответа геокодера.
             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/GeocoderResponseMetaData.xml
             */
          //  console.log('Метаданные ответа геокодера: ', res.metaData);
            /**
             * Метаданные геокодера, возвращаемые для найденного объекта.
             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/GeocoderMetaData.xml
             */
          //  console.log('Метаданные геокодера: ', firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData'));
            /**
             * Точность ответа (precision) возвращается только для домов.
             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/precision.xml
             */
          //  console.log('precision', firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.precision'));
            /**
             * Тип найденного объекта (kind).
             * @see https://api.yandex.ru/maps/doc/geocoder/desc/reference/kind.xml
             
            console.log('Тип геообъекта: %s', firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.kind'));
            console.log('Название объекта: %s', firstGeoObject.properties.get('name'));
            console.log('Описание объекта: %s', firstGeoObject.properties.get('description'));
            console.log('Полное описание объекта: %s', firstGeoObject.properties.get('text'));
            /**
            * Прямые методы для работы с результатами геокодирования.
            * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/GeocodeResult-docpage/#getAddressLine
          
            console.log('\nГосударство: %s', firstGeoObject.getCountry());
            console.log('Населенный пункт: %s', firstGeoObject.getLocalities().join(', '));
            console.log('Адрес объекта: %s', firstGeoObject.getAddressLine());
            console.log('Наименование здания: %s', firstGeoObject.getPremise() || '-');
            console.log('Номер здания: %s', firstGeoObject.getPremiseNumber() || '-');
            /**
             * Если нужно добавить по найденным геокодером координатам метку со своими стилями и контентом балуна, создаем новую метку по координатам найденной и добавляем ее на карту вместо найденной.
             */
            
             var myPlacemark = new ymaps.Placemark(coords, {
             iconContent: dev1[0].innerHTML,
             balloonContent: 'Содержимое балуна <strong>моей метки</strong>'
             }, {
             preset: 'islands#violetStretchyIcon'
             });

             myMap.geoObjects.add(myPlacemark);
             
        });
	  

}
	
	
	
	
	
	
	</script>
<?php endif;

if(in_array($this->params->get('params.qr_code_geo'), array($client, 3)) && isset($this->value['position']['lat']) && isset($this->value['position']['lng']) && $this->value['position']['lat'] && $this->value['position']['lng']):
	$w = $this->params->get('params.qr_width_geo', 120);
	$url = 'http://chart.apis.google.com/chart?cht=qr&chs=' . $w . 'x' . $w . '&chl=geo:' . $this->value['position']['lat'] . ',' . $this->value['position']['lng'];
	?>
	<div class="qr-image qr-image-geo"><?php echo JHtml::image($url, JText::_('G_LOCATIONQR'), array('width' => $w, 'height' => $w));?></div>
<?php endif;
