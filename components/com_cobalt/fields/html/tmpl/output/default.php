<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<?php 

$str = '<div class="about-us-vine">' . $this->value . '</div>';

// убираем HTML теги со строки
$str = strip_tags($str);

// проверяем длину строки
if (strlen($str) > 100) {

	// записываем в переменную первые xxx символов
	$textPrev = substr($str, 0, 100);
	// смотрим на последние символы строки - текст не должен заканчиваться на символы эти 4 символа
	$textPrev = rtrim($textPrev, "!,.-");
	// находим последний пробел, чтобы строка заканчивалась целым словом
	$textPrev = substr($textPrev, 0, strrpos($textPrev, ' '));
	// записываем в новую переменную строку, которая начинается с последнего символа "текста анонса"
	$textNext = substr($str, strlen($textPrev));
	?>
	
	<div id="abtext" class="review-text">
		<span class="text-prev"><?=$textPrev?></span><span class="text-ellipsis-mobile">...</span><span class="text-next"><?=$textNext?></span><span class="text-ellipsis-desktop">...</span>
		<a class="text-more">Подробнее</a>
	</div>

	<?
} else {
	// выводим целую строку, если ее длина меньше 180 символов
	echo $str;
}
?>
<script type="text/javascript">
if(!document.querySelector('.text-more').isDisplayed()){
	document.querySelector('.text-ellipsis-mobile').style.display = 'none';
}

document.querySelector('.text-more').onclick = function(){
	if(document.querySelector('.text-more').innerText == 'Подробнее'){
		document.querySelector('.text-ellipsis-mobile').style.display = 'none';
		document.querySelector('.text-next').style.display = 'contents';
		document.querySelector('.text-more').innerText = 'Свернуть';	
	}else{
		document.querySelector('.text-ellipsis-mobile').style.display = '';
		document.querySelector('.text-next').style.display = 'none';
		document.querySelector('.text-more').innerText = 'Подробнее';
	}
}
</script>
