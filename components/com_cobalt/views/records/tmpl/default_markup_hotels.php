<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$user_id = $this->input->getInt('user_id', 0);
$app = JFactory::getApplication();

$markup = $this->tmpl_params['markup'];
$listparams = $this->tmpl_params['list'];

$listOrder	= @$this->ordering;
$listDirn	= @$this->ordering_dir;

$back = NULL;
if($this->input->getString('return'))
{
	$back = Url::get_back('return');
}

$isMe = $this->isMe;
$current_user = JFactory::getUser($this->input->getInt('user_id', $this->user->get('id')));
?>
<?php if($markup->get('main.css')):?>
<style>
<!--
	<?php echo $markup->get('main.css');?>
-->
</style>
<?php endif;?>
<!--  ---------------------------- Show page header ---------------------------------- -->

<!--  If section is personalized load user block -->
<?php if(($this->section->params->get('personalize.personalize') && $this->input->getInt('user_id')) || $this->isMe):?>
	<?php echo $this->loadTemplate('user_block');?>


<!-- If title is allowed to be shown -->
<?php elseif($markup->get('title.title_show')):?>
	<div class="page-header">
		<?php if(in_array($this->section->params->get('events.subscribe_category'), $this->user->getAuthorisedViewLevels()) && $this->input->getInt('cat_id')):?>
			<div class="pull-right">
				<?php echo HTMLFormatHelper::followcat($this->input->getInt('cat_id'), $this->section);?>
			</div>
		<?php elseif(in_array($this->section->params->get('events.subscribe_section'), $this->user->getAuthorisedViewLevels())):?>
			<div class="pull-right">
				<?php echo HTMLFormatHelper::followsection($this->section);?>
			</div>
		<?php endif;?>
		<h1>
			<?php echo $this->escape(Mint::_($this->title)); ?>
			<?php if($this->category->id):?>
				<?php echo CEventsHelper::showNum('category', $this->category->id, TRUE);?>
			<?php else:?>
				<?php echo CEventsHelper::showNum('section', $this->section->id, TRUE);?>
			<?php endif;?>
		</h1>
	</div>


<!-- If menu parameters title is set -->
<?php elseif ($this->appParams->get('show_page_heading', 0) && $this->appParams->get('page_heading', '')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->appParams->get('page_heading')); ?>
		</h1>
	</div>
<?php endif;?>

<div id="compare" <?php echo !$this->compare ? 'class="hide"' : '';?>>
	<div class="alert alert-info alert-block">
		<h4><?php echo JText::sprintf('CCOMPAREMSG', $this->compare) ?></h4>
		<br><a rel="nofollow" href="<?php echo JRoute::_('index.php?option=com_cobalt&view=compare&section_id='.$this->section->id.'&return='.Url::back()); ?>" class="btn btn-primary"><?php echo JText::_('CCOMPAREVIEW');?></a>
		<button onclick="Cobalt.CleanCompare(null, '<?php echo @$this->section->id ?>')" class="btn"><?php echo JText::_('CCLEANCOMPARE');?></button>
	</div>
</div>

<!-- --------------  Show description of the current category or section ---------------------- -->
<?php if($this->description):?>
	<?php echo $this->description; ?>
<?php endif;?>
<form method="post" action="<?php echo $this->action; ?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
	
	<!-- --------------  Show menu and filters ---------------------- -->
	
	<?php if(in_array($markup->get('menu.menu'), $this->user->getAuthorisedViewLevels()) || in_array($markup->get('menu.menu'), $this->user->getAuthorisedViewLevels())): ?>
		<DIV class="clearfix"></DIV>
		<div class="navbar" id="cnav">
			<div class="navbar-inner">
			<?php if($markup->get('filters.filters')):?>
				<div class="form-inline navbar-form pull-right search-form">
					<div class="searchform1">
					
<!-- ПРАВКА КОДА -->
					<?php if(in_array($markup->get('filters.show_search'), $this->user->getAuthorisedViewLevels())):?>
						<input type="text"  class="search-input" placeholder="<?php echo JText::_('CSEARCHPLACEHOLDER');  ?>" name="filter_search"
							   value="<?php echo htmlentities($this->state->get('records.search'), ENT_COMPAT, 'utf-8');?>" />
							   
							  <svg class="searchloop" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M7 2C5.67392 2 4.40215 2.52678 3.46447 3.46447C2.52678 4.40215 2 5.67392 2 7C2 8.32608 2.52678 9.59785 3.46447 10.5355C4.40215 11.4732 5.67392 12 7 12C8.32608 12 9.59785 11.4732 10.5355 10.5355C11.4732 9.59785 12 8.32608 12 7C12 5.67392 11.4732 4.40215 10.5355 3.46447C9.59785 2.52678 8.32608 2 7 2ZM2.20582e-07 7C2.1098e-06 5.89126 0.263375 4.79838 0.768432 3.81135C1.27349 2.82433 2.00578 1.97139 2.90501 1.32278C3.80423 0.674163 4.84467 0.248431 5.94064 0.0806374C7.03661 -0.0871566 8.15676 0.00778714 9.20885 0.35765C10.2609 0.707513 11.2149 1.30229 11.9921 2.093C12.7693 2.8837 13.3476 3.84773 13.6794 4.90568C14.0111 5.96363 14.0868 7.08525 13.9001 8.17817C13.7135 9.27109 13.27 10.3041 12.606 11.192L17.707 16.292C17.8946 16.4795 18.0001 16.7339 18.0002 16.9991C18.0003 17.2644 17.895 17.5189 17.7075 17.7065C17.52 17.8941 17.2656 17.9996 17.0004 17.9997C16.7351 17.9998 16.4806 17.8945 16.293 17.707L11.193 12.607C10.1525 13.3853 8.91592 13.8587 7.62167 13.9741C6.32741 14.0895 5.02658 13.8424 3.86481 13.2604C2.70304 12.6784 1.72618 11.7846 1.0436 10.6789C0.361026 9.57322 -0.000325925 8.29939 2.20582e-07 7Z" fill="black"/>
</svg>
<!-- ПРАВКА КОДА КОНЕЦ -->
							   
				<?php endif;?>
					</div>
	<!-- ПРАВКА КОДА -->
					<?php //if(in_array($markup->get('filters.show_more'), $this->user->getAuthorisedViewLevels())):?>
             		<!--<a class="btn btn-mini btn-link" data-toggle="collapse" data-target="#filter-collapse" rel="tooltip" data-original-title="<?php echo JText::_('CMORESEARCHOPTIONS')?>">
							<?php //echo HTMLFormatHelper::icon('binocular.png');  ?>
						</a>-->
					<?php //endif;?>
					
	<!-- ПРАВКА КОДА КОНЕЦ -->				
					
				</div>
			<?php endif;?>

			<?php if($markup->get('menu.menu')):?>
				<ul class="nav">
				
				
				
				
				<?php if($markup->get('filters.filters')):?>
				<div class="Filter1">
					<span style="display: none;">Search box</span>
					
					<?php if(in_array($markup->get('filters.show_more'), $this->user->getAuthorisedViewLevels())):?>
						<a class="btn-filter" data-toggle="collapse" data-target="#filter-collapse" rel="tooltip" data-original-title="<?php echo JText::_('CMORESEARCHOPTIONS')?>">
							<svg class="filter-icon" width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 2C3.25 2.33152 3.1183 2.64946 2.88388 2.88388C2.64946 3.1183 2.33152 3.25 2 3.25C1.66848 3.25 1.35054 3.1183 1.11612 2.88388C0.881696 2.64946 0.75 2.33152 0.75 2C0.75 1.66848 0.881696 1.35054 1.11612 1.11612C1.35054 0.881696 1.66848 0.75 2 0.75C2.33152 0.75 2.64946 0.881696 2.88388 1.11612C3.1183 1.35054 3.25 1.66848 3.25 2ZM6 1C5.73478 1 5.48043 1.10536 5.29289 1.29289C5.10536 1.48043 5 1.73478 5 2C5 2.26522 5.10536 2.51957 5.29289 2.70711C5.48043 2.89464 5.73478 3 6 3H16C16.2652 3 16.5196 2.89464 16.7071 2.70711C16.8946 2.51957 17 2.26522 17 2C17 1.73478 16.8946 1.48043 16.7071 1.29289C16.5196 1.10536 16.2652 1 16 1H6ZM6 6C5.73478 6 5.48043 6.10536 5.29289 6.29289C5.10536 6.48043 5 6.73478 5 7C5 7.26522 5.10536 7.51957 5.29289 7.70711C5.48043 7.89464 5.73478 8 6 8H16C16.2652 8 16.5196 7.89464 16.7071 7.70711C16.8946 7.51957 17 7.26522 17 7C17 6.73478 16.8946 6.48043 16.7071 6.29289C16.5196 6.10536 16.2652 6 16 6H6ZM6 11C5.73478 11 5.48043 11.1054 5.29289 11.2929C5.10536 11.4804 5 11.7348 5 12C5 12.2652 5.10536 12.5196 5.29289 12.7071C5.48043 12.8946 5.73478 13 6 13H16C16.2652 13 16.5196 12.8946 16.7071 12.7071C16.8946 12.5196 17 12.2652 17 12C17 11.7348 16.8946 11.4804 16.7071 11.2929C16.5196 11.1054 16.2652 11 16 11H6ZM2 8.25C2.33152 8.25 2.64946 8.1183 2.88388 7.88388C3.1183 7.64946 3.25 7.33152 3.25 7C3.25 6.66848 3.1183 6.35054 2.88388 6.11612C2.64946 5.8817 2.33152 5.75 2 5.75C1.66848 5.75 1.35054 5.8817 1.11612 6.11612C0.881696 6.35054 0.75 6.66848 0.75 7C0.75 7.33152 0.881696 7.64946 1.11612 7.88388C1.35054 8.1183 1.66848 8.25 2 8.25ZM2 13.25C2.33152 13.25 2.64946 13.1183 2.88388 12.8839C3.1183 12.6495 3.25 12.3315 3.25 12C3.25 11.6685 3.1183 11.3505 2.88388 11.1161C2.64946 10.8817 2.33152 10.75 2 10.75C1.66848 10.75 1.35054 10.8817 1.11612 11.1161C0.881696 11.3505 0.75 11.6685 0.75 12C0.75 12.3315 0.881696 12.6495 1.11612 12.8839C1.35054 13.1183 1.66848 13.25 2 13.25Z" fill="black"/>
</svg> <span class="filter-lable">Фильтр</span>

						</a>
					<?php endif;?>
				</div>
			<?php endif;?>
				
				
				
				
				
				
					<?php if(($app->input->getString('view_what') || $app->input->getInt('user_id') || $app->input->getInt('ucat_id') || $back) && $markup->get('menu.menu_all')):?>
						<li>
						
		
						
							<a href="<?php echo $back ? $back : JRoute::_(Url::records($this->section))?>">
								<?php if($markup->get('menu.menu_all_records_icon')):?>
									<?php echo HTMLFormatHelper::icon('navigation-180.png');  ?>
								<?php endif;?>
								<?php echo $back ? JText::_('CGOBACK') : JText::_($markup->get('menu.menu_all_records', 'All Records'));?>
							</a>
						</li>
					<?php endif;?>

					<?php if($app->input->getString('cat_id') && $markup->get('menu.menu_home_button')):?>
						<li>
							<a href="<?php echo Url::records($this->section)?>">
								<?php if($markup->get('menu.menu_home_icon')):?>
									<?php echo HTMLFormatHelper::icon($this->section->get('personalize.text_icon', 'home.png'));  ?>
								<?php endif;?>
								<?php echo JText::_($markup->get('menu.menu_home_label', 'Home'));?>
							</a>
						</li>
					<?php endif;?>

					<?php if(!empty($this->category->parent_id) && ($this->category->parent_id > 1) && $markup->get('menu.menu_up')):?>
						<li>
							<a href="<?php echo Url::records($this->section, $this->category->parent_id)?>">
								<?php if($markup->get('menu.menu_up_icon')):?>
									<?php echo HTMLFormatHelper::icon('arrow-curve-090-left.png');  ?>
								<?php endif;?>
								<?php echo JText::_($markup->get('menu.menu_up_label', 'Up'));?>
							</a>
						</li>
					<?php endif;?>

					<?php if(!empty($this->postbuttons)):?>
						<?php if(count($this->postbuttons) > 1):?>
							<?php $l = array(); foreach ($this->postbuttons AS $type)
							{
								$o = array();
								if(in_array($type->params->get('submission.submission'),  $this->user->getAuthorisedViewLevels()) || MECAccess::allowNew($type, $this->section))
								{
									$o[] = '<a href="'.Url::add($this->section, $type, $this->category).'">'.JText::_($type->name).'</a>';
								}
								else
								{
									$o[] = '<a class="disabled" rel="tooltipright" data-original-title="'.JText::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit %s'), JText::_($type->name)).'">'.JText::_($type->name).'</a>';
								}
								if($o)
								{
									$l[] = '<li>'.implode('', $o).'</li>';
								}
							}
							?>
							<?php if($l):?>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">
										<?php if($markup->get('menu.menu_newrecord_icon')):?>
											<?php echo HTMLFormatHelper::icon('plus.png');  ?>
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_newrecord_label', 'Post here'))?>
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu">
										<?php echo implode("\n", $l);?>
									</ul>
								</li>
							<?php endif;?>
						<?php elseif(count($this->postbuttons) == 1) : ?>
							<?php $submit = array_values($this->postbuttons); $submit = array_shift($submit);?>
							<li class="dropdown" id="add-company">
								<a class="btn-add-company"
									<?php if(!(in_array($submit->params->get('submission.submission'),  $this->user->getAuthorisedViewLevels()) || MECAccess::allowNew($submit, $this->section))): ?>
										class="disabled tip-bottom" rel="tooltip" href="#"
										data-original-title="<?php echo JText::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit <b>%s</b>'), JText::_($submit->name))?>"
									<?php else:?>
										href="<?php echo Url::add($this->section, $submit, $this->category);?>"
									<?php endif;?>
								>
									<?php if($markup->get('menu.menu_newrecord_icon')):?>
	<!-- ПРАВКА КОДА -->			<?php //echo HTMLFormatHelper::icon('plus.png'); УБИРАЕМ ИКОНКУ + к ДОБАВИТЬ ОТЕЛЬ ?>
									<?php endif;?>
									<?php echo JText::sprintf($markup->get('menu.menu_user_single', 'Post %s here'), JText::_($submit->name));?>
								</a>
							</li>
						<?php endif;?>
					<?php endif;?>

					<?php if(count($this->list_templates) > 1 && in_array($markup->get('menu.menu_templates'), $this->user->getAuthorisedViewLevels()) && $this->items):?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?php if($markup->get('menu.menu_templates_icon')):?>
									<?php echo HTMLFormatHelper::icon('zones.png');  ?>
								<?php endif;?>
								<?php echo JText::_($markup->get('menu.menu_templates_label', 'Switch view'))?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<?php foreach ($this->list_templates AS $id => $template):?>
									<?php $tmpl = explode('.', $id);
										  $tmpl = $tmpl[0];
									?>
									<li>
										<a href="javascript:void(0)" onclick="Cobalt.applyFilter('filter_tpl', '<?php echo $id?>')">
										<?php echo ($this->list_template == $tmpl) ? '<strong>' : '';?>
										<?php echo $template;?>
										<?php echo ($this->list_template == $tmpl) ? '</strong>' : '';?>
										</a>
									</li>
								<?php endforeach;?>
							</ul>
						</li>
					<?php endif;?>

					<?php if(in_array($markup->get('menu.menu_ordering'), $this->user->getAuthorisedViewLevels()) && $this->items):?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?php if($markup->get('menu.menu_ordering_icon')):?>
									<?php echo HTMLFormatHelper::icon('sort.png');  ?>
								<?php endif;?>
								<?php echo JText::_($markup->get('menu.menu_ordering_label', 'Sort By'))?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<?php if(@$this->items[0]->searchresult):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('document-search-result.png'): null ).' '.JText::_('CORDERRELEVANCE'), 'searchresult', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_ctime'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('core-ctime.png'): null ).' '.JText::_($markup->get('menu.menu_order_ctime_label', 'Created')), 'r.ctime', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_mtime'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_mtime_icon') ? HTMLFormatHelper::icon('core-ctime.png'): null ).' '.JText::_($markup->get('menu.menu_order_mtime_label', 'Modified')), 'r.mtime', $listDirn, $listOrder); ?></li>
								<?php endif;?>
								<?php if(in_array($markup->get('menu.menu_order_extime'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_extime_icon') ? HTMLFormatHelper::icon('core-ctime.png'): null ).' '.JText::_($markup->get('menu.menu_order_extime_label', 'Expire')), 'r.extime', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_title'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_title_icon') ? HTMLFormatHelper::icon('edit.png'): null ).' '.JText::_($markup->get('menu.menu_order_title_label', 'Title')), 'r.title', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_hits'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_hits_icon') ? HTMLFormatHelper::icon('hand-point-090.png'): null ).' '.JText::_($markup->get('menu.menu_order_hits_label', 'Hist')), 'r.hits', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_votes_result'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_votes_result_icon') ? HTMLFormatHelper::icon('star.png'): null ).' '.JText::_($markup->get('menu.menu_order_votes_result_label', 'Votes')), 'r.votes_result', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_comments'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_comments_icon') ? HTMLFormatHelper::icon('balloon-left.png'): null ).' '.JText::_($markup->get('menu.menu_order_comments_label', 'Comments')), 'r.comments', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_favorite_num'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_favorite_num_icon') ? '<img src="'.JURI::root(TRUE) . '/media/mint/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png" > ': null ).' '.JText::_($markup->get('menu.menu_order_favorite_num_label', 'Number of bookmarks')), 'r.favorite_num', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_username'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_username_icon') ? HTMLFormatHelper::icon('user.png'): null ).' '.JText::_($markup->get('menu.menu_order_username_label', 'user name')), $this->section->params->get('personalize.author_mode'), $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_fields'),  $this->user->getAuthorisedViewLevels())):?>
									<?php foreach ($this->sortable AS $field):?>
										<li>
										<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_fields_icon') && ($icon = $field->params->get('core.icon')) ? HTMLFormatHelper::icon($icon): null ).' '.JText::_($field->label), FieldHelper::sortName($field), $listDirn, $listOrder); ?></li>
									<?php endforeach;?>
								<?php endif;?>
							</ul>
						</li>
					<?php endif;?>

					<?php if(in_array($markup->get('menu.menu_user'), $this->user->getAuthorisedViewLevels()) && $this->user->id && !$this->isMe):?>
						<?php $counts = $this->_getUsermenuCounts($markup);?>
						<li class="dropdown" id="cobalt-user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?php if($markup->get('menu.menu_user_icon')):?>
									<?php echo HTMLFormatHelper::icon('user.png');  ?>
								<?php endif;?>
								<?php echo JText::_($markup->get('menu.menu_user_label', 'My Menu'))?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<?php if($markup->get('menu.menu_user_my')):?>
									<li><a href="<?php echo JRoute::_(Url::user('created'));?>">
										<?php if($markup->get('menu.menu_user_my_icon')):?>
											<?php echo HTMLFormatHelper::icon($this->section->params->get('personalize.text_icon', 'home.png'));?>
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_my_label', 'My Homepage'))?>
										<span class="badge"><?php echo $counts->created;?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_followed') && $counts->followed):?>
									<li><a href="<?php echo JRoute::_(Url::user('follow'));?>">
										<?php if($markup->get('menu.menu_user_follow_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/follow1.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_follow_label', 'Watched'))?>
										<span class="badge"><?php echo $counts->followed;?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_evented') && CEventsHelper::getNum('section', $this->section->id)):?>
									<li><a href="<?php echo JRoute::_(Url::user('events'));?>">
										<?php if($markup->get('menu.menu_user_events_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/bell.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_events_label', 'With new events'))?>
										<?php echo CEventsHelper::showNum('section', $this->section->id)?>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_favorite') && $counts->favorited):?>
									<li><a href="<?php echo JRoute::_(Url::user('favorited'));?>">
										<?php if($markup->get('menu.menu_user_favorite_icon')):?>
											<img src="<?php echo JURI::root(TRUE) . '/media/mint/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png';?>" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_favorite_label', 'Bookmarked'))?>
										<span class="badge"><?php echo $counts->favorited; ?></span>
									</a></li>
								<?php endif;?>
								<?php if($markup->get('menu.menu_user_rated') && $counts->rated):?>
									<li><a href="<?php echo JRoute::_(Url::user('rated'));?>">
										<?php if($markup->get('menu.menu_user_rated_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/star.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_rated_label', 'Rated'))?>
										<span class="badge"><?php echo $counts->rated; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_commented') && $counts->commented):?>
									<li><a href="<?php echo JRoute::_(Url::user('commented'));?>">
										<?php if($markup->get('menu.menu_user_commented_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/balloon-left.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_commented_label', 'Commented'))?>
										<span class="badge"><?php echo $counts->commented; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_visited') && $counts->visited):?>
									<li><a href="<?php echo JRoute::_(Url::user('visited'));?>">
										<?php if($markup->get('menu.menu_user_visited_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/hand-point-090.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_visited_label', 'Visited'))?>
										<span class="badge"><?php echo $counts->visited; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_expire') && $counts->expired):?>
									<li><a href="<?php echo JRoute::_(Url::user('expired'));?>">
										<?php if($markup->get('menu.menu_user_expire_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/clock--exclamation.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_expire_label', 'Expired'))?>
										<span class="badge"><?php echo $counts->expired; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_hidden') && $counts->hidden):?>
									<li><a href="<?php echo JRoute::_(Url::user('hidden'));?>">
										<?php if($markup->get('menu.menu_user_hidden_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/eye-half.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_hidden_label', 'Hidden'))?>
										<span class="badge"><?php echo $counts->hidden; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_feature') && $counts->featured):?>
									<li><a href="<?php echo JRoute::_(Url::user('featured'));?>">
										<?php if($markup->get('menu.menu_user_feature_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/arrow-curve-090-left.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_feature_label', 'Fetured'))?>
										<span class="badge"><?php echo $counts->featured; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_unpublished') && $counts->unpublished):?>
									<li><a href="<?php echo JRoute::_(Url::user('unpublished'));?>">
										<?php if($markup->get('menu.menu_user_unpublished_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/minus-circle.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_unpublished_label', 'On Approval'))?>
										<span class="badge"><?php echo $counts->unpublished; ?></span>
									</a></li>
								<?php endif;?>


								<?php if($markup->get('menu.menu_user_moder') && MECAccess::allowModerate(NULL, NULL, $this->section)):?>
									<li class="divider"></li>
									<li><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=moderators&filter_section='.$this->section->id.'&return='.Url::back());?>">
										<?php if($markup->get('menu.menu_user_moder_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/user-share.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_moder_label', 'Moderators'))?>
									</a></li>
								<?php endif;?>

								<?php if($this->section->params->get('personalize.personalize') && $this->section->params->get('personalize.allow_section_set')):?>
									<li class="divider"></li>
									<li><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=options&layout=section&section_id='.$this->section->id.'&return='.Url::back());?>">
										<?php if($markup->get('menu.menu_user_subscribe_icon')):?>
											<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/gear.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_subscribe_label', 'Options'))?>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_cat_manage') && in_array($this->section->params->get('personalize.pcat_submit'), $this->user->getAuthorisedViewLevels())):?>
									<li class="divider"></li>
									<li class="dropdown-submenu">
										<a tabindex="-1" href="<?php echo JRoute::_(Url::_('categories').'&return='.Url::back())?>">
											<?php if($markup->get('menu.menu_user_cat_manage_icon')):?>
												<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/category.png" align="absmiddle" />
											<?php endif;?>
											<?php echo JText::_($markup->get('menu.menu_user_cat_manage_label', 'Categories'))?>
											<span class="badge"><?php echo $counts->categories; ?></span>
										</a>
										<?php if($markup->get('menu.menu_user_cat_add')):?>
											<ul class="dropdown-menu">
												<li>
													<a tabindex="-1" href="<?php echo JRoute::_(Url::_('category'))?>">
													<?php if($markup->get('menu.menu_user_cat_add_icon')):?>
														<img src="<?php echo JURI::root(TRUE);?>/media/mint/icons/16/plus.png" align="absmiddle" />
													<?php endif;?>
													<?php echo JText::_($markup->get('menu.menu_user_cat_add_label', 'Add new category'))?>
													</a>
												</li>
											</ul>
										<?php endif;?>
									</li>
								<?php endif;?>
							</ul>
						</li>
					<?php endif;?>
				</ul>
				<div class="clearfix"></div>
			<?php endif;?>
			</div>
		</div>
		<script>
			(function($){
				if(!$('#cnav .navbar-inner').text().trim()) {
					$('#adminForm').hide();
				}

				var el = $('#cobalt-user-menu');
				var list = $('ul.dropdown-menu li', el);
				if(!list || list.length == 0) {
				   el.hide();
				}
			}(jQuery))
		</script>


		<?php if(in_array($markup->get('filters.show_more'), $this->user->getAuthorisedViewLevels()) && $markup->get('filters.filters')):?>
			<div class="fade collapse separator-box" id="filter-collapse">
				<div class="btn-group pull-right">
					<button class="btn btn-primary" onclick="Joomla.submitbutton('records.filters')">
						<img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/tick-button.png" align="absmiddle" alt="<?php echo JText::_('CAPPLY');?>" />
						<?php echo JText::_('CAPPLY');?></button>
					<?php if(count($this->worns)):?>
						<button class="btn" type="button" onclick="Joomla.submitbutton('records.cleanall')">
							<img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/cross-button.png" align="absmiddle" alt="<?php echo JText::_('CRESETFILTERS');?>" />
							<?php echo JText::_('CRESETFILTERS');?></button>
					<?php endif;?>
					<button class="btn" type="button"  data-toggle="collapse" data-target="#filter-collapse">
						<img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/minus-button.png" align="absmiddle" alt="<?php echo JText::_('CCLOSE');?>" />
						<?php echo JText::_('CCLOSE');?></button>
				</div>
				
<!-- ПРАВКА КОДА  УБИРАЕМ ЗАГОЛОВОК ПОКАЗАТЬ БОЛЬШЕ ФИЛЬТРОВ С БЛЕВОТНОЙ ИКОНКОЙ
				<h3>
					<img src="<?php //echo JURI::root(TRUE)?>/media/mint/icons/16/funnel.png" align="absmiddle" alt="<?php //echo JText::_('CMORESEARCHOPTIONS');?>" />
					<?php //echo JText::_('CMORESEARCHOPTIONS')?>
				</h3>
ПРАВКА КОДА КОНЕЦ -->
				
				<div class="clearfix"></div>
				<div class="tabbable tabs-left">
					<ul class="nav nav-tabs" id="vtabs">
						<?php if(in_array($markup->get('filters.filter_type'), $this->user->getAuthorisedViewLevels()) && (count($this->submission_types) > 1)):?>
							<li><a href="#tab-types" data-toggle="tab"><?php echo ($markup->get('filters.filter_type_icon') ? HTMLFormatHelper::icon('block.png') : NULL).JText::_($markup->get('filters.type_label', 'Content Type'))?></a></li>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_tags'), $this->user->getAuthorisedViewLevels())):?>
							<li><a href="#tab-tags" data-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') ? HTMLFormatHelper::icon('price-tag.png') : NULL).JText::_($markup->get('filters.tag_label', 'CTAGS'))?></a></li>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_user'), $this->user->getAuthorisedViewLevels())):?>
							<li><a href="#tab-users" data-toggle="tab"><?php echo ($markup->get('filters.filter_user_icon') ? HTMLFormatHelper::icon('user.png') : NULL).JText::_($markup->get('filters.user_label', 'CAUTHOR'))?></a></li>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_cat'), $this->user->getAuthorisedViewLevels()) && $this->section->categories  && ($this->section->params->get('general.filter_mode') == 0)):?>
							<li><a href="#tab-cats" data-toggle="tab"><?php echo ($markup->get('filters.filter_category_icon') ? HTMLFormatHelper::icon('category.png') : NULL).JText::_($markup->get('filters.category_label', 'CCATEGORY'))?></a></li>
						<?php endif;?>

						<?php if(count($this->filters) && $markup->get('filters.filter_fields')):?>
							<?php foreach ($this->filters AS $filter):?>
								<?php if($filter->params->get('params.filter_hide')) continue;  ?>
								<li><a href="#tab-<?php echo $filter->key?>" id="<?php echo $filter->key?>" data-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') && $filter->params->get('core.icon') ? HTMLFormatHelper::icon($filter->params->get('core.icon')) : NULL).' '.$filter->label?></a></li>
							<?php endforeach;?>
						<?php endif;?>
					</ul>
					<div class="tab-content" id="vtabs-content">
						<?php if(in_array($markup->get('filters.filter_type'), $this->user->getAuthorisedViewLevels()) && (count($this->submission_types) > 1)):?>
							<div class="tab-pane active" id="tab-types">
								<?php if($markup->get('filters.filter_type_type') == 1):?>
									<?php echo JHtml::_('types.checkbox', $this->total_types, $this->submission_types, $this->state->get('records.type'));?>
								<?php elseif($markup->get('filters.filter_type_type') == 3):?>
									<?php echo JHtml::_('types.toggle', $this->total_types, $this->submission_types, $this->state->get('records.type'));?>
								<?php else :?>
									<?php echo JHtml::_('types.select', $this->total_types_option, $this->state->get('records.type'));?>
								<?php endif;?>
							</div>
						<?php endif;?>


						<?php if(in_array($markup->get('filters.filter_tags'), $this->user->getAuthorisedViewLevels())):?>
							<div class="tab-pane" id="tab-tags">
								<?php if($markup->get('filters.filter_tags_type') == 1):?>
									<?php echo JHtml::_('tags.tagform', $this->section, $this->state->get('records.tag'));?>
								<?php elseif($markup->get('filters.filter_tags_type') == 2):?>
									<?php echo JHtml::_('tags.tagcheckboxes', $this->section, $this->state->get('records.tag'));?>
								<?php elseif($markup->get('filters.filter_tags_type') == 3):?>
									<?php echo JHtml::_('tags.tagselect', $this->section, $this->state->get('records.tag'));?>
								<?php elseif($markup->get('filters.filter_tags_type') == 4):?>
									<?php echo JHtml::_('tags.tagpills', $this->section, $this->state->get('records.tag'));?>
								<?php endif;?>
							</div>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_user'), $this->user->getAuthorisedViewLevels())):?>
							<div class="tab-pane" id="tab-users">
								<?php if($markup->get('filters.filter_users_type') == 1):?>
									<?php echo JHtml::_('cusers.form', $this->section, $this->state->get('records.user'));?>
								<?php elseif($markup->get('filters.filter_users_type') == 2):?>
									<?php echo JHtml::_('cusers.checkboxes', $this->section, $this->state->get('records.user'));?>
								<?php elseif($markup->get('filters.filter_users_type') == 3):?>
									<?php echo JHtml::_('cusers.select', $this->section, $this->state->get('records.user'));?>
								<?php endif;?>
							</div>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_cat'), $this->user->getAuthorisedViewLevels()) && $this->section->categories && ($this->section->params->get('general.filter_mode') == 0)):?>
							<div class="tab-pane" id="tab-cats">
								<?php if($markup->get('filters.filter_category_type') == 1):?>
									<?php echo JHtml::_('categories.form', $this->section, $this->state->get('records.category'));?>
								<?php elseif($markup->get('filters.filter_category_type') == 2):?>
									<?php echo JHtml::_('categories.checkboxes', $this->section, $this->state->get('records.category'), array('columns' => 3));?>
								<?php elseif($markup->get('filters.filter_category_type') == 3):?>
									<?php echo JHtml::_('categories.select', $this->section, $this->state->get('records.category'), array('multiple' => 0));?>
								<?php elseif($markup->get('filters.filter_category_type') == 4):?>
									<?php echo JHtml::_('categories.select', $this->section, $this->state->get('records.category'), array('multiple' => 1, 'size' => 25));?>
								<?php elseif($markup->get('filters.filter_category_type') == 5):?>
									<?php echo JHtml::_('mrelements.catselector', "filters[cats][]", $this->section->id, $this->state->get('records.category'));?>
								<?php endif;?>
							</div>
						<?php endif;?>
						<?php if(count($this->filters) && $markup->get('filters.filter_fields')):?>
							<?php foreach ($this->filters AS $filter):?>
								<?php if($filter->params->get('params.filter_hide')) continue;  ?>
								<div class="tab-pane" id="tab-<?php echo $filter->key?>">
									<?php if($filter->params->get('params.filter_descr') && $markup->get('filters.filter_descr')):?>
										<p><small><?php echo JText::_($filter->params->get('params.filter_descr'));?></small></p>
									<?php endif;?>
									<?php echo $filter->onRenderFilter($this->section);?>
								</div>
							<?php endforeach;?>
						<?php endif;?>
					</div><!--  tab-content -->
				</div><!--  tabable -->
				<br>
			</div><!--  collapse -->

			<script type="text/javascript">
				jQuery('#vtabs a:first').tab('show');
			</script>
		<?php endif;?>
	<?php endif;?>

	<input type="hidden" name="section_id" value="<?php echo $this->state->get('records.section_id')?>">
	<input type="hidden" name="cat_id" value="<?php echo $app->input->getInt('cat_id');?>">
	<input type="hidden" name="option" value="com_cobalt">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="limitstart" value="0">
	<input type="hidden" name="filter_order" value="<?php //echo $this->ordering; ?>">
	<input type="hidden" name="filter_order_Dir" value="<?php //echo $this->ordering_dir; ?>">
	<?php echo JHtml::_( 'form.token' ); ?>
	<?php if($this->worns):?>
		<?php foreach ($this->worns AS $worn):?>
			<input type="hidden" name="clean[<?php echo $worn->name; ?>]" id="<?php echo $worn->name; ?>" value="">
		<?php endforeach;?>
	<?php endif;?>
</form>

<!-- --------------  Show category index ---------------------- -->
<?php if($this->show_category_index):?>
	<DIV class="clearfix"></DIV>
	<?php echo $this->loadTemplate('cindex_'.$this->section->params->get('general.tmpl_category'));?>
<?php endif;?>

<?php if($markup->get('main.alpha') && $this->alpha && $this->alpha_list && $this->items):?>
	<div class="alpha-index">
		<?php foreach ($this->alpha AS $set):?>
			<div class="alpha-set">
				<?php foreach ($set AS $alpha):?>
					<?php if(in_array($alpha, $this->alpha_list)):?>
						<span class="label label-warning" onclick="Cobalt.applyFilter('filter_alpha', '<?php echo $alpha?>')"
							<?php echo $markup->get('main.alpha_num') ? 'rel="tooltip" data-original-title="'.JText::plural('CXNRECFOUND',
								@$this->alpha_totals[$alpha]).'"' : NULL;?>><?php echo $alpha; ?></span>
					<?php else:?>
						<span class="label"><?php echo $alpha; ?></span>
					<?php endif;?>
				<?php endforeach;?>
			</div>
		<?php endforeach;?>
	</div>
	<br>
<?php endif;?>




<?php if($markup->get('filters.worns') && count($this->worns)):?>
<div class="filter-worns">
	<?php foreach ($this->worns AS $worn):?>
		<div class="alert pull-left">
			<button type="button" class="close" data-dismiss="alert" onclick="Cobalt.cleanFilter('<?php echo $worn->name?>')" rel="tooltip" data-original-title="<?php echo JText::_('CDELETEFILTER')?>">
			<img alt="X" src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/cross.png"></button>
			<div><?php echo $worn->label?></div>
			<?php echo $worn->text?>
		</div>
	<?php endforeach;?>
	<?php if(count($this->worns) > 1): ?>
		<button onclick="Joomla.submitbutton('records.cleanall');" class="alert alert-error  pull-left">
			<div><?php echo JText::_('CORESET'); ?></div>
			<?php echo JText::_('CODELETEALLFILTERS'); ?>
		</button>
	<?php endif;?>

	<div class="clearfix"></div>
</div>
<br>
<?php endif;?>




<?php if($this->items):?>

	<?php echo $this->loadTemplate('list_'.$this->list_template);?>

	<?php if ($this->tmpl_params['list']->def('tmpl_core.item_pagination', 1)) : ?>
		<form method="post">
			<div style="text-align: center;">
				<small>
					<?php if($this->pagination->getPagesCounter()):?>
						<?php echo $this->pagination->getPagesCounter(); ?>
					<?php endif;?>
					<?php  if ($this->tmpl_params['list']->def('tmpl_core.item_limit_box', 0)) : ?>
						<?php echo str_replace('<option value="0">'.JText::_('JALL').'</option>', '', $this->pagination->getLimitBox());?>
					<?php endif; ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</small>
			</div>
			<?php if($this->pagination->getPagesLinks()): ?>
				<div style="text-align: center;" class="pagination">
					<?php echo str_replace('<ul>', '<ul class="pagination-list">', $this->pagination->getPagesLinks()); ?>
				</div>
				<div class="clearfix"></div>
			<?php endif; ?>
		</form>
	<?php endif; ?>

<?php elseif($this->worns):?>
	<h4 align="center"><?php echo JText::_('CNORECFOUNDSEARCH');?></h4>
<?php else:?>
	<?php if(((!empty($this->category->id) && $this->category->params->get('submission')) || (empty($this->category->id) && $this->section->params->get('general.section_home_items'))) && !$this->input->get('view_what')):?>
		<h4 align="center" class="no-records" id="no-records<?php echo $this->section->id; ?>"><?php echo JText::_('CNOARTICLESHERE');?></h4>
	<?php endif;?>
<?php endif;?>
