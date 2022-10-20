<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
//require_once JPATH_ROOT . '/components/com_cobalt/fields/geo/tpl/output/default.php';
$default = new JRegistry($this->value);
$contacts = $links = $link_list = $contact_list = $adr_list = array();
$defaultmarker = $default->get('position.marker', $this->params->get('params.map_icon_src.icon'));
$lang = explode('-', JFactory::getLanguage()->getTag());
$lang = $lang[0];

?>

<style>
.img-marker {
	margin-right: 8px;
	margin-bottom: 8px;
	cursor: pointer;
}
#markertabs li a {
	padding: 2px 12px;
}
#map_canvas_<?php echo $this->id;?> label { width: auto; display:inline; }
#map_canvas_<?php echo $this->id;?> img { max-width: none; }
</style>
<?php if(in_array($this->params->get('params.adr_enter'), $this->user->getAuthorisedViewLevels())):?>

	<?php $address = JFormFieldCgeo::getAddressFields();?>
	<?php

	$sort = array('country' => $address['country'], 'state' => $address['state'],
	'city' => $address['city'], 'zip' => $address['zip']);
	?>
	<?php if(count($address)): $key = 0;?>
		<h5><?php echo JText::_("G_ADDRESS");?></h5>
		<?php foreach($sort as $name => $adr):?>
			<?php
			if(! $this->params->get('params.address.' . $name . '.show', 1))
			{
				continue;
			}
			$input = $this->_input('address', $name, 'text');
			$label = $this->_label('address', $name, $adr['label']);
			$adr_list[$name] = $input;
			?>
			<?php if($key % 2 == 0):?>
			<div class="row-fluid">
			<?php endif;?>

			<div class="span6">
				<small><?php echo $label;?></small><br>

				<?php if($name == 'country'):?>
					<?php echo $this->countries();?>
				<?php else:?>
					<?php echo $input; ?>
				<?php endif;?>
			</div>

			<?php if($key % 2 != 0):?>
			</div>
			<?php endif; $key++;?>

		<?php endforeach; ?>

		<?php if($key % 2 != 0):?>
			</div>
		<?php endif;?>

		<?php if($this->params->get('params.address.address1.show', 1)):?>
			<div class="row-fluid">
				<?php if($this->params->get('params.address.address1.show', 1)):?>
					<div class="span12">
						<small><?php echo $this->_label('address', 'address1', $address['address1']['label']);?></small><br>
						<?php echo  $this->_input('address', 'address1'); ?>
					</div>
				<?php endif;?>
			</div>
		<?php endif;?>
		<?php if($this->params->get('params.address.company.show', 1) || $this->params->get('params.address.person.show', 1)):?>
			<div class="row-fluid">
				<?php if($this->params->get('params.address.company.show', 1)):?>
					<div class="span<?php echo ($this->params->get('params.address.person.show', 1) ? 6 : 12) ?>">
						<small><?php echo $this->_label('address', 'company', $address['company']['label']);?></small><br>
						<?php echo  $this->_input('address', 'company'); ?>
					</div>
				<?php endif;?>
				<?php if($this->params->get('params.address.person.show', 1)):?>
					<div class="span<?php echo ($this->params->get('params.address.company.show', 1) ? 6 : 12) ?>">
						<small><?php echo $this->_label('address', 'person', $address['person']['label']);?></small><br>
						<?php echo  $this->_input('address', 'person'); ?>
					</div>
				<?php endif;?>
			</div>
		<?php endif;?>
			<?php if(! empty($this->value['address'])):
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
	endif;?>	
<?php endif;?>	
		
<!-- Форма и Я Карта -->
<?php if($this->params->get('params.address.address2.show', 1)):?>
	<div class="searchmap">
		<div id="locationField1">
		   <small style="margin-bottom:10px"></small>
		 <!--  <input type="text" id="suggest" class="ymaps-searchbox-input__input"<?php //if(!empty(implode("\n", $a))): ?>placeholder="<?php //echo implode("\n", $a);?>" <?php //else: ?>placeholder="Адрес или название организации" <?php //endif;?>style="text-align:center" name="jform[fields][40][address][address2] value="<?php //if($a): ?><?php //echo implode("\n", $a);?><?php //endif;?>" />
		  	  -->  <?php //if($this->params->get('params.address.address2.show', 1)):?>
			    <?php //echo  $this->_input('address', 'address2'); ?>
				<?php //endif;?>
		   </div>
		<div class="adress_result">
		<textarea id="suggest" class="ymaps-searchbox-input__input" placeholder="Адрес или название организации" name="jform[fields][40][address][address2]"  value="<?php echo implode("\n", $a);?>">
		<?php echo implode("\n", $a);?>
		</textarea>
		</div>
	</div>
	
		
	

		<div id="map1" style="width: 100% !important; height: 260px; transform: translateX(0px);"></div>
<!--<address id="ininput">
			<?php //if($a): ?><?php //echo implode("\n", $a);?><?php //endif;?>
		</address>
<!-- Konets:) Форма и Я Карта -->

		<?php if(in_array($this->params->get('params.map_marker'), $this->user->getAuthorisedViewLevels())):?>
			<br/>
	<?php endif;?>
		<div class="clearfix"></div>
	<?php endif;?>
<?php endif;?>

<?php if(in_array($this->params->get('params.map_marker'), $this->user->getAuthorisedViewLevels())):?>
	<h5>
		<?php if(!$this->params->get('params.map_require')):?>
		<button class="btn btn-mini btn-danger pull-right hide" id="rmp<?php echo $this->id?>" type="button"><?php echo JText::_('G_REMOVEPOSITION');?></button>
		<?php endif; ?>

		<?php if($this->params->get('params.map_require')):?>
			<?php echo JHtml::image(JURI::root() . 'media/mint/icons/16/asterisk-small.png', 'Required', array('align'=>'absmiddle', 'rel' => 'tooltip', 'data-original-title' => JText::_('CREQUIRED')));?>
		<?php endif; ?>

		<?php echo JText::_('G_MAP');?>
	</h5>
	<style type="text/css">
		#locationField {
			display: inline;
			position: absolute;
			z-index: 5;
		}
		#locationField input {
			width: 280px;
		}
	</style>

	<!--<div id="map_canvas_<?php //echo $this->id;?>" style="width:<?php //echo $this->params->get('params.map_width', '100%');?>; height:<?php //echo $this->params->get('params.map_height', '200px');?>"></div>
	<!--<small><?php //echo JText::_('G_DRAGMARKER');?></small>-->
	<?php if(in_array($this->params->get('params.map_manual_position'), $this->user->getAuthorisedViewLevels())):?>
	<div class="row-fluid">
		<div class="span6">
			<small><?php echo JText::_('G_LAT')?></small>
			<?php echo $this->_input('position', 'lat');?>
		</div>
		<div class="span6">
			<small><?php echo JText::_('G_LNG')?></small>
			<?php echo $this->_input('position', 'lng');?>
		</div>
	</div>
	<?php else:?>
		<?php echo $this->_input('position', 'lat', 'hidden');?>
		<?php echo $this->_input('position', 'lng', 'hidden');?>
	<?php endif;?>
	<?php echo $this->_input('position', 'zoom', 'hidden');?>

	<?php
		$dir = JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_cobalt'. DIRECTORY_SEPARATOR .'fields'. DIRECTORY_SEPARATOR .'geo'. DIRECTORY_SEPARATOR .'markers'. DIRECTORY_SEPARATOR .$this->params->get('params.map_icon_src.dir', 'custom');
		$path = '/components/com_cobalt/fields/geo/markers/'.$this->params->get('params.map_icon_src.dir', 'custom').'/';
	?>

	<?php if(in_array($this->params->get('params.map_whoicon'), $this->user->getAuthorisedViewLevels())):?>
		<h5><?php echo JText::_('G_MARKER');?></h5>
		<?php echo $this->_input('position', 'marker', 'hidden');?>
		
		
		<div class="markers1" style= "display:none; max-height:220px;width:100%;overflow-x:hidden;overflow-y:scroll">
			<?php $folders = JFolder::folders($dir);?>
			<?php if($folders):?>
				<div class="tabbable tabs-left">
					<ul class="nav nav-tabs" id="markertabs">
						<?php foreach ($folders AS $folder):?>
							<li><a href="#tab-<?php echo $folder?>" data-toggle="tab"><?php echo JText::_($folder);?></a></li>
						<?php endforeach;?>
					</ul>
					<div class="tab-content">
						<?php foreach ($folders AS $folder):?>
							<div class="tab-pane active" id="tab-<?php echo $folder?>">
								<?php echo $this->_listmarkers($dir.DIRECTORY_SEPARATOR.$folder, $defaultmarker, $folder.'/'); ?>
							</div>
						<?php endforeach;?>
					</div>
				</div>
				<script>
					jQuery('#markertabs a:first').tab('show');
				</script>
			<?php else:?>
				<?php echo $this->_listmarkers($dir, $defaultmarker); ?>
			<?php endif;?>
		</div>
		
		
		
	<?php endif;?>
	<script type="text/javascript">
		<?php
		$style = 'null';
		if(substr($this->params->get('params.map_style'), -5) == '.json')
		{
			$style = file_get_contents(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/js/mapstyles/' . $this->params->get('params.map_style'));
		}
		$w = 32;
		$h = 37;
		if(JFile::exists(JPATH_ROOT.'/'.$path.$defaultmarker))
		{
			$msize = getimagesize(JPATH_ROOT.'/'.$path.$defaultmarker);
			$w = $msize[0];
			$h = $msize[1];
		}

		$default_country = @$this->values['address']['country'];
		if(!$default_country && $this->params->get('params.country_limit') && (count($this->params->get('params.country_limit')) == 1))
		{
		    $country_array = $this->params->get('params.country_limit');
		    $default_country = array_shift($country_array);
		}
		?>
		/*jQuery(function(){jQuery('#map_canvas_<?php echo $this->id;?>').loadmap({
			id: '<?php echo $this->id; ?>',
			style: <?php echo $style; ?>,
			lat: '<?php echo $default->get('position.lat'); ?>',
			lng: '<?php echo $default->get('position.lng'); ?>',
			plat: '<?php echo $this->params->get('params.map_lat'); ?>',
			plng: '<?php echo $this->params->get('params.map_lng'); ?>',
			zoom: '<?php echo $default->get('position.zoom'); ?>',
			pzoom: '<?php echo $this->params->get('params.map_zoom'); ?>',
			marker: '<?php echo $defaultmarker;?>',
			marker_path: '<?php echo JURI::root(TRUE).$path;?>',
			marker_w: '<?php echo $w;?>',
			marker_h: '<?php echo $h;?>',
			root: '<?php echo JURI::root(TRUE);?>',
			lang: '<?php echo substr(JFactory::getLanguage()->getTag() ,0 ,2); ?>',
			initposition: <?php echo (int)$this->params->get('params.map_find_position');?>,
            defaultcountry: '<?php echo $default_country; ?>',
			strings: {
				addrnotfound: '<?php echo JText::_('G_ADDRESSNOTFOUND');  ?>',
				addrnotentered: '<?php echo JText::_('G_ENTERADDRESS'); ?>',
				geocodefail: '<?php echo JText::_('G_GEONOTSUCCESSFUL'); ?>'
			}
		})});*/
		<?php if(count($adr_list) <= 0): ?>
		    jQuery('#adr_loc<?php echo $this->id; ?>').css('display', 'none');
		<?php endif;?>
	</script>
<?php endif;?>

<?php if(in_array($this->params->get('params.adr_enter'), $this->user->getAuthorisedViewLevels())):?>
	<?php
	$format = '<tr><td nowrap="nowrap" width="1%%">%s %s</td><td><div class="row-fluid">%s</div></td></tr>';
	$contacts = JFormFieldCgeo::getAditionalFields();

	foreach($contacts as $name => $contact)
	{
		if(! $this->params->get('params.contacts.' . $name . '.show', 1))
		{
			continue;
		}
		$input = $this->_input('contacts', $name);
		$group = 'contacts';
		if($contact['label'] == JText::_('G_SKYPE'))
		{
			$contact['icon'] = JURI::root() . 'components/com_cobalt/fields/geo/icons/skype.png';
		}
		$contact_list[] = sprintf($format, JHtml::image($contact['icon'], $contact['label']), $this->_label('contacts', $name, $contact['label']), $input);
	}
	?>
	<?php if($contact_list):?>
		<h5><?php echo JText::_("G_INSTANTCONTACTS");?></h5>
		<table class="table table-hover"><?php echo implode(' ', $contact_list);?></table>
	<?php endif;?>

	<?php
	$links = JFormFieldCgeo::getAditionalinks();
	foreach($links as $name => $link)
	{
		if(! $this->params->get('params.links.' . $name . '.show', 1))
		{
			continue;
		}
		$input = $this->_input('links', $name);
		$link_list[] = sprintf($format, JHtml::image($link['icon'], $link['label']), $this->_label('links', $name, $link['label']), $input);
	}
	?>
	<?php if($link_list):?>
		<h5><?php echo JText::_("G_LINKS");?></h5>
		<table class="table table-hover"><?php echo implode(' ', $link_list);?></table>
	<?php endif; ?>

<?php endif;?>

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
		
		var searchControl = new ymaps.control.SearchControl({
    options: {
    // Будет производиться поиск по топонимам и организациям.
    provider: 'yandex#search'
   }
});
myMap.controls.add(searchControl);
		var dev = document.getElementById('suggest');
		var dev1 = document.querySelector('.inputbox.required');
		 var suggestView1 = new ymaps.SuggestView('suggest');
   
	
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
    //... отключаем перетаскивание карты
    myMap.behaviors.disable('drag');
}
	
	//console.log(dev1.value);
	 ymaps.geocode(dev.value, {//document.getElementById("suggest").value)
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
             iconContent: dev1.value,
             balloonContent: 'Содержимое балуна <strong>моей метки</strong>'
             }, {
             preset: 'islands#violetStretchyIcon'
             });

             myMap.geoObjects.add(myPlacemark);
             
        });
	  

var div = document.querySelector("#suggest");
div.addEventListener('change', function (e) { 

	/*e = e.target || e.srcElement;
      //if(e.getAttribute('data-copy') !== 'true') return; если нужно копировать только с inputa у которого есть атрибут data-copy
	var value = e.value;
	var child = document.querySelectorAll(".ymaps-2-1-79-searchbox-input__input");//e.parentNode.children
	for(var i = 0; i < child.length; i++) {
		child[i].value = value;
	};*/
	 searchControl.search(div.value);
	 //console.log(suggestView1.value);
  });
  div.addEventListener('keyup', function () { 
	 searchControl.search(div.value)
  });
  //var er = document.getElementsById('id_165327975585931882202'); 
	div.addEventListener('click', function () { 
	 searchControl.search(div.value)
  });
}
</script>