<?php
/**
 * Emerald by MintJoomla
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
	Emerald.submitbutton = function(task)
	{
		if (task == 'emstate.cancel' || document.formvalidator.isValid('#item-form')) {
			Emerald.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<form method="post" name="adminForm" id="item-form" class="uk-form uk-form-horizontal">
<div class="page-header">
    <div class="uk-float-right">
        <?php $layout = Mint::loadLayout('buttons', $basePath = JPATH_COMPONENT .'/layouts'); echo $layout->render(null); ?>
    </div>
	<h1>
		<?php if($this->item->id):?>
            <img src="<?php echo JUri::root(TRUE); ?>/components/com_emerald/images/cpanel/states.png" />
			<?php echo JText::sprintf('EEDITSTATE', $this->item->label);?>
		<?php else:?>
            <img src="<?php echo JUri::root(TRUE); ?>/components/com_emerald/images/cpanel/states.png" />
			<?php echo JText::_('ENEWSTATE');?>
		<?php endif;?>
	</h1>
</div>
<hr />
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<fieldset class="adminform">
				<div class="uk-form-row">
					<label class="uk-form-label"><?php echo $this->form->getLabel('id'); ?></label>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label"><?php echo $this->form->getLabel('country'); ?></label>
					<div class="controls"><?php echo $this->form->getInput('country'); ?></div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label"><?php echo $this->form->getLabel('state'); ?></label>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label"><?php echo $this->form->getLabel('label'); ?></label>
					<div class="controls"><?php echo $this->form->getInput('label'); ?></div>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
	<input type="hidden" name="return" value="<?php echo $this->state->get('state.return');?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>