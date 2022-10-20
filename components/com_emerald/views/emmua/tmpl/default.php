<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 1.7 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<div class="page-header">
	<h1><?php echo JText::_('EMR_MUA_TITLE'); ?></h1>
</div>

<p>
	<a class="btn btn-link" href="<?php echo EmeraldApi::getLink('emhistory'); ?>">
		<img src="<?php echo JUri::root(true); ?>/components/com_emerald/images/back.png">
		<?php echo JText::_('EMR_MUA_BAKTOHISTORY'); ?>
	</a>
</p>

<?php if($this->instruction): ?>
	<?php echo $this->instruction; ?>
<?php endif; ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th width="1%"><?php echo JText::_('ID')?></th>
			<th><?php echo JText::_('EUSER')?></th>
			<th width="1%"><?php echo JText::_('EACTIVE')?></th>
			<th nowrap width="1%"><?php echo JText::_('ESTARTON')?></th>
			<th nowrap width="1%"><?php echo JText::_('EENDON')?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->mua as $row): ?>
			<?php
			$img = 'active.png';
			if($row->published == 0) $img = 'block.png';
			if($row->expired)
			{
				$class = 'red';
				$img = 'block.png';
			}
			else
			{
				$class = 'green';
			}
			?>
			<tr>
				<td><?php echo $row->id; ?></td>
				<td><?php echo $row->username; ?></td>
				<td align="center">
					<img align="absmiddle" border="0"
						src="<?php echo JURI::root(true)?>/components/com_emerald/images/<?php echo $img;?>"></td>
				<td nowrap><?php echo JHtml::_('date', $row->ctime, $this->params->get('date_format')); ?></td>
				<td nowrap><span class="<?php echo $class; ?>"><?php echo JHtml::_('date', $row->extime, JText::_('DATE_FORMAT_LC3'));?></span></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>