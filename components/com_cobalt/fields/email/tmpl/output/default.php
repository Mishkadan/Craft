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
$document = JFactory::getDocument();
$document->addScript(JURI::root(TRUE) . '/components/com_cobalt/fields/email/email_iframe.js');
$params = $this->params;

if ($this->value && in_array($params->get('params.view_mail', 1), $this->user->getAuthorisedViewLevels()))
{
	$fvalue = JHtml::_('content.prepare', $this->value);
	if($params->get('params.qr_code', 0))
	{
		$width = $this->params->get('params.qr_width', 60);
		$src = 'http://chart.apis.google.com/chart?chs='.$width.'x'.$width.'&cht=qr&chld=L|0&chl='.$this->value;

		echo JHtml::image($src, JText::_('E_QRCODE'), array( 'class' => 'qr-image', 'width' => $width, 'height' => $width, 'align' => 'absmiddle'));
	}

	echo '<div class="emailadres">
	<svg class="svg-inline--fa fa-envelope fa-w-16 email" aria-hidden="true" focusable="false" data-prefix="far" data-icon="envelope" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path></svg>			
	 &nbsp; &nbsp;'. $fvalue .'</div>';
}

if (in_array($params->get('params.send_mail', 3), $this->user->getAuthorisedViewLevels()))
{
	if ($params->get('params.to') == 1 && !$this->value)
		return;
	if ($params->get('params.to') == 5 && !$params->get('params.custom'))
		return;

	$url_form = JRoute::_('index.php?option=com_cobalt&view=elements&layout=field&id=' . $this->id . '&section_id=' . $section->id . '&func=_getForm&record=' . $record->id . '&tmpl=component&Itemid=' . $this->request->getInt('Itemid').'&width=640', FALSE);
	$key = $record->id.$this->id;
	switch ($params->get('params.form_style', 1))
	{

	case 1 :?>
		<a href="javascript: void(0);" onclick="getEmailIframe('<?php echo $key;?>', '<?php echo $url_form;?>');" data-role="button" class="btn btn-primary btn-small" data-toggle="collapse" data-target="#email_form<?php echo $record->id;?>">
			<?php echo JText::_($this->params->get('params.popup_label', $this->label));?>
		</a>

		<div id="email_form<?php echo $key;?>" class="hide"></div>

	<?php break; ?>

	<?php case 2:?>
		<a class="btn btn-primary btn-small" onclick="getEmailIframe('<?php echo $key;?>', '<?php echo $url_form;?>');" href="#emailmodal<?php echo $this->id;?>" data-toggle="modal" role="button">
			<?php echo JText::_($this->params->get('params.popup_label', $this->label));?>
		</a>

		<div style="width:700px;" class="modal hide fade" id="emailmodal<?php echo $this->id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('E_SENDMSG');?></h3>
			</div>

			<div id="email_form<?php echo $key;?>" class="modal-body" style="overflow-x: hidden; max-height:500px; padding:0;">
			</div>
		</div>
	<?php break; ?>

	<?php case 3 : ?>
		<div id="email_form<?php echo $this->id;?>">
			<h3><?php echo JText::_($this->params->get('params.popup_label', $this->label));?></h3>
			<iframe frameborder="0" src="<?php echo $url_form;?>" width="100%" height="<?php echo $params->get('params.height', 600);?>" />
		</div>
	<?php break; ?>
<?php
	}
}
?>