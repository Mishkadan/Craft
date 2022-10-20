<?php
/**
 * Template For Galery Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 3.10.xx CMS (http://www.joomla.org)
 * Author Website: http://craft.ru.net/
 * @copyright Copyright (C) 2022 Mishkadan Webbear Studio (http://webbs.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/library/php/fields/cobaltupload.php';
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_cobalt/fields/gallery/gallery.php';
if(empty($this->value))
{
	return null;
}

$this->record = $record;
$key = $this->id . '-' . $record->id;
$this->_init();

$dir = JComponentHelper::getParams('com_cobalt')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;

$ids = array_keys($this->value);
if ($this->params->get('params.thumbs_list_random', 1))
{
	shuffle($ids);
}
$index = array_shift($ids);

$picture = $dir . $this->value[$index]['fullpath'];

$url     = CImgHelper::getThumb($picture, $this->params->get('params.thumbs_list_width', 100), $this->params->get('params.thumbs_list_height', 100), 'gallery' . $key, $record->user_id,
	array(
		 'mode'       => $this->params->get('params.thumbs_list_mode', 6),
		 'strache'    => $this->params->get('params.thumbs_list_stretch', 1),
		 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
		 'quality'    => $this->params->get('params.thumbs_list_quality', 80)
	));

$rel = '';
if ($this->params->get('params.lightbox_click_list', 0) == 0)
{
	$rel = ' data-lightbox="' . $this->id . '_' . $this->record->id.'"';
	if ($this->params->get('params.show_mode', 'gallerybox') == 'gallerybox')
	{
		$rel = ' rel="gallerybox' . $this->id . '_' . $this->record->id.'"';
	}
	if ($this->params->get('params.show_mode', 'gallerybox') == 'rokbox')
	{
        $rel = ' data-rokbox data-rokbox-album="'.htmlentities($this->record->title, ENT_COMPAT, 'UTF-8').'"';
	}
}

if($rel)
{
	$url_orig =  CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
		array(
			 'mode'       => $this->params->get('params.full_mode', 6),
			 'strache'    => $this->params->get('params.full_stretch', 1),
			 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
			 'quality'    => $this->params->get('params.full_quality', 80)
		));
}
?>
<!-- Swiper css
<link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css"/>
-->
<?php if($rel):  ?>
	<div class="product-section">
	  <div class="product-wrapper">
		<?php foreach($this->value as $picture_index => $file):  ?>
		 <div class="product-item">
		 <span class="product-realname"><?php echo $file['title']?></span>
			<?php $url =  CImgHelper::getThumb($dir . $file['fullpath'], $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
				array(
					 'mode'       => $this->params->get('params.full_mode', 6),
					 'strache'    => $this->params->get('params.full_stretch', 1),
					 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
					 'quality'    => $this->params->get('params.full_quality', 80)
				));  ?>

			<a href="<?php echo $url; ?>" id="<?php echo $picture_index; ?>" value="<?php echo $picture_index; ?>" onclick="addhash(this)"<?php echo $rel  ?>>
				<img src="<?php echo $url; ?>" alt="<?php echo htmlentities($file['title'], ENT_COMPAT, 'UTF-8'); ?>">
			</a>

		  </div>

		<?php endforeach;  ?>
	  </div>

	</div>



<?php else:  ?>
	<a href="<?php echo $record->url  ?>">
		<img src="<?php echo $url;  ?>" class="img-polaroid">
	</a>
<?php endif;  ?>


<script>
    function addhash(element) {
        window.location.hash = element.id;
		// * предотвращение скролла к элементу по его хэшу
		// element.preventDefault();
    }
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const anc = window.location.hash.replace("#","");
            if (anc == "") {
            return false}
            const zzz = document.getElementById(anc);
            const vvv = document.getElementById('1');
           // if (window.location.hash ="01"){ /todo мож допилить еще
               // vvv.click();
            //} else {
                zzz.click();
           // }
        }, 500);
    });

    </script>
