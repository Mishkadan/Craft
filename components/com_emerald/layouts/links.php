<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

if(!EmeraldHelper::isModer()) return;
$view = JFactory::getApplication()->input->getCmd('view');
$img_url =  JUri::root(TRUE).'/components/com_emerald/images/cpanel/';
?>
<style>
.emerald a.brand,
.emerald li {line-height: 27px;}
.emerald a.brand img{width: 30px;}
</style>
<div class="navbar emerald">
	<div class="navbar-inner">
		<a class="brand" href="<?php echo JRoute::_('index.php?option=com_emerald&view=emcpanel')?>"><img src="<?php echo $img_url; ?>dashboard-small.png" align="absmiddle"/> <strong>Emerald <sup><small>10</small></sup></strong></a>
		<ul class="nav">
		<li class="<?php echo($view == 'emsales' ? 'active' : NULL); ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emsales') ?>">
				<img src="<?php echo $img_url; ?>sales-small.png" align="absmiddle"/> <?php echo JText::_('ESUBSCRIPTIONS') ?></a></li>
		<li class="<?php echo($view == 'emplans' ? 'active' : NULL); ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emplans') ?>">
				<img src="<?php echo $img_url; ?>plans-small.png" align="absmiddle"/> <?php echo JText::_('EPLANS') ?></a></li>
		<li class="<?php echo($view == 'emgroups' ? 'active' : NULL); ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emgroups') ?>">
				<img src="<?php echo $img_url; ?>groups-small.png" align="absmiddle"/> <?php echo JText::_('EGROUPS') ?></a></li>
		<li class="<?php echo($view == 'emfields' ? 'active' : NULL); ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emfields') ?>">
				<img src="<?php echo $img_url; ?>fields-small.png" align="absmiddle"/> <?php echo JText::_('EFIELDS') ?></a></li>

			<li class="dropdown <?php echo(in_array($view, array('emcoupons', 'emanalytics', 'emstates', 'emtaxes', 'emimports', 'emlist', 'emhistory')) ? 'active' : NULL); ?>">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?php echo $img_url; ?>more-small.png" align="absmiddle"/> <?php echo JText::_('EOTHER')?> <i class="caret"></i>
				</a>
				<ul class="dropdown-menu">
					<li class="<?php echo($view == 'emcoupons' ? 'uk-active' : NULL); ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emcoupons') ?>">
							<img src="<?php echo $img_url; ?>coupons-small.png" align="absmiddle"/> <?php echo JText::_('ECOUPONS') ?></a></li>
					<li class="<?php echo($view == 'emanalytics' ? 'uk-active' : NULL); ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emanalytics') ?>">
							<img src="<?php echo $img_url; ?>analytics-small.png" align="absmiddle"/> <?php echo JText::_('EANALYTICS') ?></a></li>
					<li class="<?php echo($view == 'emstates' ? 'uk-active' : NULL); ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emstates') ?>">
							<img src="<?php echo $img_url; ?>states-small.png" align="absmiddle"/> <?php echo JText::_('ESTATES') ?></a></li>
					<li class="<?php echo($view == 'emtaxes' ? 'uk-active' : NULL); ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emtaxes') ?>">
							<img src="<?php echo $img_url; ?>taxes-small.png" align="absmiddle"/> <?php echo JText::_('ETAXES') ?></a></li>
					<li class="<?php echo($view == 'emimports' ? 'uk-active' : NULL); ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emimports') ?>">
							<img src="<?php echo $img_url; ?>import-small.png" align="absmiddle"/> <?php echo JText::_('EIMPORT') ?></a></li>
					<li class="divider"></li>
					<li class="<?php echo($view == 'emlist' ? 'uk-active' : NULL); ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emlist') ?>">
							<img src="<?php echo $img_url; ?>purchase-small.png" align="absmiddle"/> <?php echo JText::_('EPLUNLIST') ?></a></li>
					<li class="<?php echo($view == 'emhistory' ? 'uk-active' : NULL); ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_emerald&view=emhistory') ?>">
							<img src="<?php echo $img_url; ?>history-small.png" align="absmiddle"/> <?php echo JText::_('EPLUNHISTORY') ?></a></li>
				</ul>

			</li>
		</ul>
	</div>
</div>
