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

$back = null;
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



<!----------- Show Menu razdela and add winodelnya -------------->

<?php if(in_array($markup->get('menu.menu_user'), $this->user->getAuthorisedViewLevels()) && $this->user->id && !$this->isMe):?>
						<?php $counts = $this->_getUsermenuCounts($markup);?>
				<ul class="nav">
				<div class="umen_add">
						<li class="dropdown" id="cobalt-user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?php //if($markup->get('menu.menu_user_icon')):?>
									<?php //echo HTMLFormatHelper::icon('user.png');  ?>
								<?php //endif;?>
								<?php echo JText::_($markup->get('menu.menu_user_label', 'My Menu'))?>
								<svg class="usmenarrow" width="15" height="10" viewBox="0 0 15 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.6664 1.22659C13.4166 0.978256 13.0787 0.838867 12.7264 0.838867C12.3742 0.838867 12.0363 0.978256 11.7864 1.22659L6.99977 5.94659L2.27977 1.22659C2.02995 0.978256 1.69202 0.838867 1.33977 0.838867C0.987521 0.838867 0.649585 0.978256 0.399769 1.22659C0.274798 1.35054 0.175605 1.49801 0.107913 1.66049C0.0402218 1.82297 0.00537109 1.99724 0.00537109 2.17326C0.00537109 2.34927 0.0402218 2.52355 0.107913 2.68603C0.175605 2.84851 0.274798 2.99597 0.399769 3.11992L6.0531 8.77326C6.17705 8.89823 6.32452 8.99742 6.487 9.06511C6.64948 9.1328 6.82375 9.16766 6.99977 9.16766C7.17578 9.16766 7.35006 9.1328 7.51254 9.06511C7.67502 8.99742 7.82248 8.89823 7.94643 8.77326L13.6664 3.11992C13.7914 2.99597 13.8906 2.84851 13.9583 2.68603C14.026 2.52355 14.0608 2.34927 14.0608 2.17326C14.0608 1.99724 14.026 1.82297 13.9583 1.66049C13.8906 1.49801 13.7914 1.35054 13.6664 1.22659Z" fill="#253544"/>
                                </svg>
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
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/follow1.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_follow_label', 'Watched'))?>
										<span class="badge"><?php echo $counts->followed;?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_evented') && CEventsHelper::getNum('section', $this->section->id)):?>
									<li><a href="<?php echo JRoute::_(Url::user('events'));?>">
										<?php if($markup->get('menu.menu_user_events_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/bell.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_events_label', 'With new events'))?>
										<?php echo CEventsHelper::showNum('section', $this->section->id)?>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_favorite') && $counts->favorited):?>
									<li><a href="<?php echo JRoute::_(Url::user('favorited'));?>">
										<?php if($markup->get('menu.menu_user_favorite_icon')):?>
											<img src="<?php echo JURI::root(true) . '/media/mint/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png';?>" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_favorite_label', 'Bookmarked'))?>
										<span class="badge"><?php echo $counts->favorited; ?></span>
									</a></li>
								<?php endif;?>
								<?php if($markup->get('menu.menu_user_rated') && $counts->rated):?>
									<li><a href="<?php echo JRoute::_(Url::user('rated'));?>">
										<?php if($markup->get('menu.menu_user_rated_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/star.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_rated_label', 'Rated'))?>
										<span class="badge"><?php echo $counts->rated; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_commented') && $counts->commented):?>
									<li><a href="<?php echo JRoute::_(Url::user('commented'));?>">
										<?php if($markup->get('menu.menu_user_commented_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/balloon-left.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_commented_label', 'Commented'))?>
										<span class="badge"><?php echo $counts->commented; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_visited') && $counts->visited):?>
									<li><a href="<?php echo JRoute::_(Url::user('visited'));?>">
										<?php if($markup->get('menu.menu_user_visited_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/hand-point-090.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_visited_label', 'Visited'))?>
										<span class="badge"><?php echo $counts->visited; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_expire') && $counts->expired):?>
									<li><a href="<?php echo JRoute::_(Url::user('expired'));?>">
										<?php if($markup->get('menu.menu_user_expire_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/clock--exclamation.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_expire_label', 'Expired'))?>
										<span class="badge"><?php echo $counts->expired; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_hidden') && $counts->hidden):?>
									<li><a href="<?php echo JRoute::_(Url::user('hidden'));?>">
										<?php if($markup->get('menu.menu_user_hidden_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/eye-half.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_hidden_label', 'Hidden'))?>
										<span class="badge"><?php echo $counts->hidden; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_feature') && $counts->featured):?>
									<li><a href="<?php echo JRoute::_(Url::user('featured'));?>">
										<?php if($markup->get('menu.menu_user_feature_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/arrow-curve-090-left.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_feature_label', 'Fetured'))?>
										<span class="badge"><?php echo $counts->featured; ?></span>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_unpublished') && $counts->unpublished):?>
									<li><a href="<?php echo JRoute::_(Url::user('unpublished'));?>">
										<?php if($markup->get('menu.menu_user_unpublished_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/minus-circle.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_unpublished_label', 'On Approval'))?>
										<span class="badge"><?php echo $counts->unpublished; ?></span>
									</a></li>
								<?php endif;?>


								<?php if($markup->get('menu.menu_user_moder') && MECAccess::allowModerate(null, null, $this->section)):?>
									<li class="divider"></li>
									<li><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=moderators&filter_section='.$this->section->id.'&return='.Url::back());?>">
										<?php if($markup->get('menu.menu_user_moder_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/user-share.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_moder_label', 'Moderators'))?>
									</a></li>
								<?php endif;?>

								<?php if($this->section->params->get('personalize.personalize') && $this->section->params->get('personalize.allow_section_set')):?>
									<li class="divider"></li>
									<li><a href="<?php echo JRoute::_('index.php?option=com_cobalt&view=options&layout=section&section_id='.$this->section->id.'&return='.Url::back());?>">
										<?php if($markup->get('menu.menu_user_subscribe_icon')):?>
											<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/gear.png" align="absmiddle" />
										<?php endif;?>
										<?php echo JText::_($markup->get('menu.menu_user_subscribe_label', 'Options'))?>
									</a></li>
								<?php endif;?>

								<?php if($markup->get('menu.menu_user_cat_manage') && in_array($this->section->params->get('personalize.pcat_submit'), $this->user->getAuthorisedViewLevels())):?>
									<li class="divider"></li>
									<li class="dropdown-submenu">
										<a tabindex="-1" href="<?php echo JRoute::_(Url::_('categories').'&return='.Url::back())?>">
											<?php if($markup->get('menu.menu_user_cat_manage_icon')):?>
												<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/category.png" align="absmiddle" />
											<?php endif;?>
											<?php echo JText::_($markup->get('menu.menu_user_cat_manage_label', 'Categories'))?>
											<span class="badge"><?php echo $counts->categories; ?></span>
										</a>
										<?php if($markup->get('menu.menu_user_cat_add')):?>
											<ul class="dropdown-menu">
												<li>
													<a tabindex="-1" href="<?php echo JRoute::_(Url::_('category'))?>">
													<?php if($markup->get('menu.menu_user_cat_add_icon')):?>
														<img src="<?php echo JURI::root(true);?>/media/mint/icons/16/plus.png" align="absmiddle" />
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
			
			 <?php endif;?>
			 
			 
			 
			 
		<?php if($markup->get('menu.menu')):?>
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
							<?php $l = array(); foreach ($this->postbuttons as $type)
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
							<li class="dropdown" id= "addproperty">
							
								<a class="adproperty"
									<?php if(!(in_array($submit->params->get('submission.submission'),  $this->user->getAuthorisedViewLevels()) || MECAccess::allowNew($submit, $this->section))): ?>
										class="disabled tip-bottom" rel="tooltip" href="#"
										data-original-title="<div class='addneed'>Для добавления своей организации в каталог вам необходимо отправить запрос администратору <a href='/533-ivan/profile'>ВОТ СЮДА :)</a></div>"
									<?php else:?>
										href="<?php echo Url::add($this->section, $submit, $this->category);?>"
									<?php endif;?>
								>
									<?php //if($markup->get('menu.menu_newrecord_icon')):?>
										
										<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M20.3335 9.66683H12.3335V1.66683C12.3335 1.31321 12.193 0.974069 11.943 0.724021C11.6929 0.473972 11.3538 0.333496 11.0002 0.333496C10.6465 0.333496 10.3074 0.473972 10.0574 0.724021C9.80731 0.974069 9.66683 1.31321 9.66683 1.66683V9.66683H1.66683C1.31321 9.66683 0.974069 9.80731 0.724021 10.0574C0.473972 10.3074 0.333496 10.6465 0.333496 11.0002C0.333496 11.3538 0.473972 11.6929 0.724021 11.943C0.974069 12.193 1.31321 12.3335 1.66683 12.3335H9.66683V20.3335C9.66683 20.6871 9.80731 21.0263 10.0574 21.2763C10.3074 21.5264 10.6465 21.6668 11.0002 21.6668C11.3538 21.6668 11.6929 21.5264 11.943 21.2763C12.193 21.0263 12.3335 20.6871 12.3335 20.3335V12.3335H20.3335C20.6871 12.3335 21.0263 12.193 21.2763 11.943C21.5264 11.6929 21.6668 11.3538 21.6668 11.0002C21.6668 10.6465 21.5264 10.3074 21.2763 10.0574C21.0263 9.80731 20.6871 9.66683 20.3335 9.66683Z" fill="white"/>
</svg>

									<?php //endif;?>
									<?php echo JText::sprintf($markup->get('', ''), JText::_($submit->name));?>
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
								<?php foreach ($this->list_templates as $id => $template):?>
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
					</ul>
					<?php endif;?>
					<div class="clearfix"></div>
			</div>
<!-- END ----------- menu razdela and add winodelnya -------------->



	<div style ="margin:0 0 20px;" class="page-header">
	 <a href ="/craft" class="btn-back"><i class="fas fa-arrow-left fa-2x"></i></a>
		
		<h1>
			<?php echo $this->escape(Mint::_($this->title)); ?>
			<?php if($this->category->id):?>
				<?php echo CEventsHelper::showNum('category', $this->category->id, true);?>
			<?php else:?>
				<?php echo CEventsHelper::showNum('section', $this->section->id, true);?>
			<?php endif;?>
		</h1>
		
		<?php if(in_array($this->section->params->get('events.subscribe_category'), $this->user->getAuthorisedViewLevels()) && $this->input->getInt('cat_id')):?>
			<div class="pull-right">
				<?php echo HTMLFormatHelper::followcat($this->input->getInt('cat_id'), $this->section);?>
			</div>
		<?php elseif(in_array($this->section->params->get('events.subscribe_section'), $this->user->getAuthorisedViewLevels())):?>
			<div class="pull-right">
				<?php echo HTMLFormatHelper::followsection($this->section);?>
			</div>
		<?php endif;?>
		
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


<!-- --------------  Show filters ---------------------- -->
	
	<?php if(in_array($markup->get('menu.menu'), $this->user->getAuthorisedViewLevels()) || in_array($markup->get('menu.menu'), $this->user->getAuthorisedViewLevels())): ?>
		<DIV class="clearfix"></DIV>
		 <div class="navbar" id="cnav">
			<div class="navbar-inner">
			<?php if($markup->get('filters.filters')):?>
			 <div class="form-inline navbar-form pull-right search-form">
				<!-- SORT BLOCK-->
			  <?php if(in_array($markup->get('menu.menu_ordering'), $this->user->getAuthorisedViewLevels()) && $this->items):?>
						<li class="dropdown sort">
							<a href="#" class="dropdown-toggle sortbtn h8" data-toggle="dropdown">
								<?php if($markup->get('menu.menu_ordering_icon')):?>
									<?php //echo HTMLFormatHelper::icon('sort.png');  ?>
									<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.33317 5.99984H5.99984V1.99984C5.99984 1.64622 5.85936 1.30708 5.60931 1.05703C5.35926 0.80698 5.02013 0.666504 4.6665 0.666504C4.31288 0.666504 3.97374 0.80698 3.7237 1.05703C3.47365 1.30708 3.33317 1.64622 3.33317 1.99984V5.99984H1.99984C1.64622 5.99984 1.30708 6.14031 1.05703 6.39036C0.80698 6.64041 0.666504 6.97955 0.666504 7.33317C0.666504 7.68679 0.80698 8.02593 1.05703 8.27598C1.30708 8.52603 1.64622 8.6665 1.99984 8.6665H7.33317C7.68679 8.6665 8.02593 8.52603 8.27598 8.27598C8.52603 8.02593 8.6665 7.68679 8.6665 7.33317C8.6665 6.97955 8.52603 6.64041 8.27598 6.39036C8.02593 6.14031 7.68679 5.99984 7.33317 5.99984ZM4.6665 11.3332C4.31288 11.3332 3.97374 11.4736 3.7237 11.7237C3.47365 11.9737 3.33317 12.3129 3.33317 12.6665V25.9998C3.33317 26.3535 3.47365 26.6926 3.7237 26.9426C3.97374 27.1927 4.31288 27.3332 4.6665 27.3332C5.02013 27.3332 5.35926 27.1927 5.60931 26.9426C5.85936 26.6926 5.99984 26.3535 5.99984 25.9998V12.6665C5.99984 12.3129 5.85936 11.9737 5.60931 11.7237C5.35926 11.4736 5.02013 11.3332 4.6665 11.3332ZM13.9998 21.9998C13.6462 21.9998 13.3071 22.1403 13.057 22.3904C12.807 22.6404 12.6665 22.9795 12.6665 23.3332V25.9998C12.6665 26.3535 12.807 26.6926 13.057 26.9426C13.3071 27.1927 13.6462 27.3332 13.9998 27.3332C14.3535 27.3332 14.6926 27.1927 14.9426 26.9426C15.1927 26.6926 15.3332 26.3535 15.3332 25.9998V23.3332C15.3332 22.9795 15.1927 22.6404 14.9426 22.3904C14.6926 22.1403 14.3535 21.9998 13.9998 21.9998ZM25.9998 11.3332H24.6665V1.99984C24.6665 1.64622 24.526 1.30708 24.276 1.05703C24.0259 0.80698 23.6868 0.666504 23.3332 0.666504C22.9795 0.666504 22.6404 0.80698 22.3904 1.05703C22.1403 1.30708 21.9998 1.64622 21.9998 1.99984V11.3332H20.6665C20.3129 11.3332 19.9737 11.4736 19.7237 11.7237C19.4736 11.9737 19.3332 12.3129 19.3332 12.6665C19.3332 13.0201 19.4736 13.3593 19.7237 13.6093C19.9737 13.8594 20.3129 13.9998 20.6665 13.9998H25.9998C26.3535 13.9998 26.6926 13.8594 26.9426 13.6093C27.1927 13.3593 27.3332 13.0201 27.3332 12.6665C27.3332 12.3129 27.1927 11.9737 26.9426 11.7237C26.6926 11.4736 26.3535 11.3332 25.9998 11.3332ZM23.3332 16.6665C22.9795 16.6665 22.6404 16.807 22.3904 17.057C22.1403 17.3071 21.9998 17.6462 21.9998 17.9998V25.9998C21.9998 26.3535 22.1403 26.6926 22.3904 26.9426C22.6404 27.1927 22.9795 27.3332 23.3332 27.3332C23.6868 27.3332 24.0259 27.1927 24.276 26.9426C24.526 26.6926 24.6665 26.3535 24.6665 25.9998V17.9998C24.6665 17.6462 24.526 17.3071 24.276 17.057C24.0259 16.807 23.6868 16.6665 23.3332 16.6665ZM16.6665 16.6665H15.3332V1.99984C15.3332 1.64622 15.1927 1.30708 14.9426 1.05703C14.6926 0.80698 14.3535 0.666504 13.9998 0.666504C13.6462 0.666504 13.3071 0.80698 13.057 1.05703C12.807 1.30708 12.6665 1.64622 12.6665 1.99984V16.6665H11.3332C10.9795 16.6665 10.6404 16.807 10.3904 17.057C10.1403 17.3071 9.99984 17.6462 9.99984 17.9998C9.99984 18.3535 10.1403 18.6926 10.3904 18.9426C10.6404 19.1927 10.9795 19.3332 11.3332 19.3332H16.6665C17.0201 19.3332 17.3593 19.1927 17.6093 18.9426C17.8594 18.6926 17.9998 18.3535 17.9998 17.9998C17.9998 17.6462 17.8594 17.3071 17.6093 17.057C17.3593 16.807 17.0201 16.6665 16.6665 16.6665Z" fill="#F1BA2C"/>
                                    </svg>
								<?php endif;?>
								<span class="sortbtn">Сортировка</span>
								
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
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_favorite_num_icon') ? '<img src="'.JURI::root(true) . '/media/mint/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png" > ': null ).' '.JText::_($markup->get('menu.menu_order_favorite_num_label', 'Number of bookmarks')), 'r.favorite_num', $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_username'),  $this->user->getAuthorisedViewLevels())):?>
									<li>
									<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_username_icon') ? HTMLFormatHelper::icon('user.png'): null ).' '.JText::_($markup->get('menu.menu_order_username_label', 'user name')), $this->section->params->get('personalize.author_mode'), $listDirn, $listOrder); ?></li>
								<?php endif;?>

								<?php if(in_array($markup->get('menu.menu_order_fields'),  $this->user->getAuthorisedViewLevels())):?>
									<?php foreach ($this->sortable as $field):?>
										<li>
										<?php echo JHtml::_('mrelements.sort',  ($markup->get('menu.menu_order_fields_icon') && ($icon = $field->params->get('core.icon')) ? HTMLFormatHelper::icon($icon): null ).' '.JText::_($field->label), FieldHelper::sortName($field), $listDirn, $listOrder); ?></li>
									<?php endforeach;?>
								<?php endif;?>
							</ul>
						</li>
					<?php endif;?>
			   <!-- SORT BLOCK END-->
					<!-- Filter Button-->
					<div class="fltr">
                    <?php 
                    /*
                    if(in_array($markup->get('filters.show_more'), $this->user->getAuthorisedViewLevels())):?>	
					   <a class="btn-filter" data-toggle="collapse" data-target="#filter-collapse" rel="tooltip" data-original-title="<?php echo JText::_('CMORESEARCHOPTIONS')?>">
						<?php echo HTMLFormatHelper::icon('binocular.png');  ?>
						 <span class="filter-lable">Фильтр</span>

						</a>
					   <?php endif;
                       */
                       ?>
					</div>
					<!-- Filter Button END-->
				</div>
			 <?php endif;?>
			</div>
			 <!-- Search BLOCK-->
			<div class="searchbox" style="margin-top: 17px;">
			<span style="display: none;">Search box</span>
					<?php 
                    
                    /*
                    if(in_array($markup->get('filters.show_search'), $this->user->getAuthorisedViewLevels())):?>
						<svg class="lupa" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M26.9465 25.0532L21.9999 20.1465C23.92 17.7524 24.8499 14.7136 24.5983 11.6549C24.3467 8.59627 22.9328 5.75025 20.6472 3.70206C18.3617 1.65388 15.3783 0.559219 12.3104 0.643166C9.24259 0.727114 6.3235 1.98329 4.15339 4.15339C1.98329 6.3235 0.727114 9.24259 0.643166 12.3104C0.559219 15.3783 1.65388 18.3617 3.70206 20.6472C5.75025 22.9328 8.59627 24.3467 11.6549 24.5983C14.7136 24.8499 17.7524 23.92 20.1465 21.9999L25.0532 26.9065C25.1772 27.0315 25.3246 27.1307 25.4871 27.1984C25.6496 27.2661 25.8239 27.3009 25.9999 27.3009C26.1759 27.3009 26.3502 27.2661 26.5126 27.1984C26.6751 27.1307 26.8226 27.0315 26.9465 26.9065C27.1869 26.6579 27.3212 26.3257 27.3212 25.9799C27.3212 25.6341 27.1869 25.3018 26.9465 25.0532ZM12.6665 21.9999C10.8206 21.9999 9.01608 21.4525 7.48122 20.4269C5.94636 19.4014 4.75008 17.9437 4.04366 16.2383C3.33725 14.5328 3.15241 12.6562 3.51254 10.8457C3.87267 9.03521 4.76159 7.37217 6.06688 6.06688C7.37217 4.76159 9.03521 3.87267 10.8457 3.51254C12.6562 3.15241 14.5328 3.33725 16.2383 4.04366C17.9437 4.75008 19.4014 5.94636 20.4269 7.48122C21.4525 9.01608 21.9999 10.8206 21.9999 12.6665C21.9999 15.1419 21.0165 17.5159 19.2662 19.2662C17.5159 21.0165 15.1419 21.9999 12.6665 21.9999Z" fill="#F1BA2C"/>
</svg>

						<input class="srchinput" type="text"  placeholder="<?php echo "Поиск по винодельням";  ?>" name="filter_search"
							   value="<?php echo htmlentities($this->state->get('records.search'), ENT_COMPAT, 'utf-8');?>" />
					<?php endif;
                    
                    */
                    ?>
			
			</div>
			<!-- Search BLOCK END-->
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
						<img src="<?php echo JURI::root(true)?>/media/mint/icons/16/tick-button.png" align="absmiddle" alt="<?php echo JText::_('CAPPLY');?>" />
						<?php echo JText::_('CAPPLY');?></button>
					<?php if(count($this->worns)):?>
						<button class="btn" type="button" onclick="Joomla.submitbutton('records.cleanall')">
							<img src="<?php echo JURI::root(true)?>/media/mint/icons/16/cross-button.png" align="absmiddle" alt="<?php echo JText::_('CRESETFILTERS');?>" />
							<?php echo JText::_('CRESETFILTERS');?></button>
					<?php endif;?>
					<button class="btn" type="button"  data-toggle="collapse" data-target="#filter-collapse">
						<img src="<?php echo JURI::root(true)?>/media/mint/icons/16/minus-button.png" align="absmiddle" alt="<?php echo JText::_('CCLOSE');?>" />
						<?php echo JText::_('CCLOSE');?></button>
				</div>
				<h3>
					<img src="<?php echo JURI::root(true)?>/media/mint/icons/16/funnel.png" align="absmiddle" alt="<?php echo JText::_('CMORESEARCHOPTIONS');?>" />
					<?php echo JText::_('CMORESEARCHOPTIONS')?>
				</h3>
				<div class="clearfix"></div>


				<div class="tabbable tabs-left">
					<ul class="nav nav-tabs" id="vtabs">
						<?php if(in_array($markup->get('filters.filter_type'), $this->user->getAuthorisedViewLevels()) && (count($this->submission_types) > 1)):?>
							<li><a href="#tab-types" data-toggle="tab"><?php echo ($markup->get('filters.filter_type_icon') ? HTMLFormatHelper::icon('block.png') : null).JText::_($markup->get('filters.type_label', 'Content Type'))?></a></li>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_tags'), $this->user->getAuthorisedViewLevels())):?>
							<li><a href="#tab-tags" data-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') ? HTMLFormatHelper::icon('price-tag.png') : null).JText::_($markup->get('filters.tag_label', 'CTAGS'))?></a></li>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_user'), $this->user->getAuthorisedViewLevels())):?>
							<li><a href="#tab-users" data-toggle="tab"><?php echo ($markup->get('filters.filter_user_icon') ? HTMLFormatHelper::icon('user.png') : null).JText::_($markup->get('filters.user_label', 'CAUTHOR'))?></a></li>
						<?php endif;?>

						<?php if(in_array($markup->get('filters.filter_cat'), $this->user->getAuthorisedViewLevels()) && $this->section->categories  && ($this->section->params->get('general.filter_mode') == 0)):?>
							<li><a href="#tab-cats" data-toggle="tab"><?php echo ($markup->get('filters.filter_category_icon') ? HTMLFormatHelper::icon('category.png') : null).JText::_($markup->get('filters.category_label', 'CCATEGORY'))?></a></li>
						<?php endif;?>

						<?php if(count($this->filters) && $markup->get('filters.filter_fields')):?>
							<?php foreach ($this->filters as $filter):?>
								<?php if($filter->params->get('params.filter_hide')) continue;  ?>
								<li><a href="#tab-<?php echo $filter->key?>" id="<?php echo $filter->key?>" data-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') && $filter->params->get('core.icon') ? HTMLFormatHelper::icon($filter->params->get('core.icon')) : null).' '.$filter->label?></a></li>
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
							<?php foreach ($this->filters as $filter):?>
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
		<?php foreach ($this->worns as $worn):?>
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
		<?php foreach ($this->alpha as $set):?>
			<div class="alpha-set">
				<?php foreach ($set as $alpha):?>
					<?php if(in_array($alpha, $this->alpha_list)):?>
						<span class="label label-warning" onclick="Cobalt.applyFilter('filter_alpha', '<?php echo $alpha?>')"
							<?php echo $markup->get('main.alpha_num') ? 'rel="tooltip" data-original-title="'.JText::plural('CXNRECFOUND',
								@$this->alpha_totals[$alpha]).'"' : null;?>><?php echo $alpha; ?></span>
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
	<?php foreach ($this->worns as $worn):?>
		<div class="alert pull-left">
			<button type="button" class="close" data-dismiss="alert" onclick="Cobalt.cleanFilter('<?php echo $worn->name?>')" rel="tooltip" data-original-title="<?php echo JText::_('CDELETEFILTER')?>">
			<img alt="X" src="<?php echo JURI::root(true)?>/media/mint/icons/16/cross.png"></button>
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

<script>
  document.addEventListener("DOMContentLoaded", function(event) {

      let u_menu = document.getElementById('cobalt-user-menu');
      let add_btn = document.querySelector('.adproperty');
      let add_section = document.getElementById('add_record');

      if (document.querySelector('.umen_add').contains(add_btn)) {
      document.querySelector('.fltr').append(u_menu);
      document.getElementById('addpost').remove();
      add_section.append(add_btn);
      document.querySelector('.umen_add').remove();
      }
      
      
      console.log(1);
  });
</script>
