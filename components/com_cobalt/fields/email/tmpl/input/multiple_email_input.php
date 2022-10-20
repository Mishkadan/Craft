<?php

defined('_JEXEC') or die();

// * js библиотека для сортировки из шаблона
JHtml::_('script', 'sortable.min.js', array('version' => 'auto', 'relative' => true));

// * атрибуты поля
$attributes = [];

// * css id
$attributes[] = 'id="field_' . $this->id . '"';

// * css класс
$css_class = [];
$css_class[] = 'form__input form__input--email';
if ($this->params->get('core.field_class'))
{
	$css_class[] = $this->params->get('core.field_class');
}
$attributes[] = 'class="' . implode(' ', $css_class) . '"';

// * тип
$attributes[] = 'type="email"';

// * имя
$attributes[] = 'name="jform[fields][' . $this->id . ']"';

// * требуемое для заполнения поле
if ($this->required)
{
	$attributes[] = 'required';
}

// * несколько
if ($this->params->get('params.multiple', 0))
{
	$attributes[] = 'multiple';
}

// * сортировка
if ($this->params->get('params.sortable', 0))
{
	$attributes[] = 'sortable';
}

// * значение
$attributes[] = 'value="' . $this->value . '"';

$attributes = implode(' ', $attributes);

?>
<input <?php echo $attributes; ?>>