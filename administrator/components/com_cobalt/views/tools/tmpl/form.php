<?php 
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
/*$doc = JFactory::getDocument();
$doc->addStyleSheet('components/com_cobalt/views/tools/tmpl/ladda-bootstrap/ladda-themeless.min.css');
$doc->addScript('components/com_cobalt/views/tools/tmpl/ladda-bootstrap/spin.min.js');
$doc->addScript('components/com_cobalt/views/tools/tmpl/ladda-bootstrap/ladda.min.js');*/
?>
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<button align="right" data-style="expand-left" class="btn btn-primary ladda-button" onclick="javascript:submitbutton('tools.apply')" style="float: right;">
		<span class="ladda-label"><?php echo JText::_('CRUNTOOL')?></span>
	</button>

	<br style="clear: both;" />	
	<br />	
	<?php echo $this->form; ?>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form> 