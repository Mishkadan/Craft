<?php

defined('_JEXEC') or die;

// контейнер будет отображаться, если работает один из параметров
$container = ($moduleclass_sfx || $params->get('backgroundimage') || $module->showtitle) ? true : false;

// открывающий тег, по умолчанию div
echo $container ? '<' .
    // тег
    $params->get('module_tag', 'div') .
    // css класс
    ($moduleclass_sfx ? ' class="' . trim($moduleclass_sfx) . '"' : '') .
    // фоновая картинка
    ($params->get('backgroundimage') ? ' style="background-image:url(' . $params->get('backgroundimage') . ')"' : '') .
    '>' : '';

// заголовок модуля
echo $module->showtitle ? '<' .
    // тег
    $params->get('header_tag', 'h3') .
    // css класс
    ($params->get('header_class') ? ' class="' . $params->get('header_class') . '"' : '') .
    '>' .
    $module->title .
    '</' . $params->get('header_tag', 'h3') . '>' : '';

// содержимое модуля
echo $module->content;

// закрывающий тег
echo $container ? '</' . $params->get('module_tag', 'div') . '>' : '';
