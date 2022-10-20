<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

if(empty($this->value))
{
	return null;
}

JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/lightgallery/dist/lightgallery.umd.js');
JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/lightgallery/dist/lg-thumbnail.umd.js');
//JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/lightgallery/dist/lg-zoom.umd.js');
JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/lightgallery/dist/lg-fullscreen.umd.js');

JFactory::getDocument()->addStyleSheet(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/lightgallery/dist/css/lightgallery-bundle.css');
JFactory::getDocument()->addStyleSheet(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/lightgallery/dist/css/lg-thumbnail.css');
JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/galleria/galleria.js');
JFactory::getDocument()->addStyleSheet(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/galleria/themes/classic/galleria.classic.css');
JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_cobalt/fields/gallery/galleria/themes/classic/galleria.classic.js');

$key = $this->id . '-' . $record->id;
$dir = JComponentHelper::getParams('com_cobalt')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;
?>
	<style>
		#galleria<?php echo $key?> {
			width: 100%;
			height: <?php echo $this->params->get('params.full_height', 100) + 60;  ?>px;

		}
	</style>
	<div id="animated-thumbnails-gallery<?php echo $key?>" class="inline-gallery-container">
		<?php
		foreach ($this->value as $picture_index => $file)
		{
			$picture = $dir . $file['fullpath'];
			$url     = CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
				array(
					 'mode'       => $this->params->get('params.full_mode', 6),
					 'strache'    => $this->params->get('params.full_stretch', 1),
					 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
					 'quality'    => $this->params->get('params.full_quality', 100)
				));
			echo '<a href="' . $url . '"><img src="' . $url . '"></a>';
		}
		?>
	</div>







	<script type="text/javascript">


	//Galleria.run('#galleria<?php echo $key?>');



    //lightGallery(document.getElementById('galleria<?php echo $key?>'), {
     //   plugins: [lgZoom, lgThumbnail, lgFullscreen],
      //  speed: 500,
       // licenseKey: 'your_license_key'

    //});
const lgContainer = document.getElementById('animated-thumbnails-gallery<?php echo $key?>');
const inlineGallery = lightGallery(lgContainer, {
    container: lgContainer,
    dynamic: false,
    // Turn off hash plugin in case if you are using it
    // as we don't want to change the url on slide change
    hash: false,
    // Do not allow users to close the gallery
    closable: false,
    // Add maximize icon to enlarge the gallery
    showMaximizeIcon: true,
    // Append caption inside the slide item
    // to apply some animation for the captions (Optional)
    appendSubHtmlTo: '.lg-item',
    // Delay slide transition to complete captions animations
    // before navigating to different slides (Optional)
    // You can find caption animation demo on the captions demo page
    plugins: [lgThumbnail],

	//thumbnail: {

		//thumb: '<?php echo  $url ?>',

	//},
});

// Since we are using dynamic mode, we need to programmatically open lightGallery
inlineGallery.openGallery();

	// * Fix body scroll
	const buttonMaxSize = lgContainer.querySelector('button.lg-maximize');
	buttonMaxSize.addEventListener('click', e => {
		const body = document.body;
		const container = lgContainer.querySelector('.lg-container');
		if (container.classList.contains('lg-inline'))
		{
			body.classList.remove('scroll-disable');
		}
		else
		{
			body.classList.add('scroll-disable');
		}
	});

	</script>


<?php if ($this->params->get('params.download_all', 0) == 1): ?>
	<div class="clearfix"></div>
	<a class="btn btn-success" href="<?php echo Url::task('files.download&fid=' . $this->id . '&rid=' . $record->id, 0); ?>">
		<?php echo JText::_('CDOWNLOADALL') ?>
	</a>
<?php endif; ?>