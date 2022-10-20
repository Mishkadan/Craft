<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

$item = $this->item;
$params = $this->tmpl_params['record'];
$icons = array();
$category = array();
$author = array();
$details = array();
$started = FALSE;
$i = $o = 0;

?>
<style>
	.dl-horizontal dd {
		margin-bottom: 10px;
	}

.line-brk {
	margin-left: 0px !important;
}
<?php echo $params->get('tmpl_params.css');?>
</style>

<?php
if($params->get('tmpl_core.item_categories') && $item->categories_links)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', (count($item->categories_links) > 1 ? JText::_('CCATEGORIES') : JText::_('CCATEGORY')), implode(', ', $item->categories_links));
}
if($params->get('tmpl_core.item_user_categories') && $item->ucatid)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', JText::_('CUCAT'), $item->ucatname_link);
}
if($params->get('tmpl_core.item_author') && $item->user_id)
{
	$a[] = JText::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $this->section));
	if($params->get('tmpl_core.item_author_filter'))
	{
		$a[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
	}
	$author[] = implode(' ', $a);
}
if($params->get('tmpl_core.item_ctime'))
{
	$author[] = JText::sprintf('CONDATE', JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
}

if($params->get('tmpl_core.item_mtime'))
{
	$author[] = JText::_('CMTIME').': '.JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));
}
if($params->get('tmpl_core.item_extime'))
{
	$author[] = JText::_('CEXTIME').': '.($item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));
}

if($params->get('tmpl_core.item_type'))
{
	$details[] = sprintf('%s: %s %s', JText::_('CTYPE'), $this->type->name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $this->type->name), $this->section) : NULL));
}
if($params->get('tmpl_core.item_hits'))
{
	$details[] = sprintf('%s: %s', JText::_('CHITS'), $item->hits);
}
if($params->get('tmpl_core.item_comments_num'))
{
	$details[] = sprintf('%s: %s', JText::_('CCOMMENTS'), CommentHelper::numComments($this->type, $this->item));
}
if($params->get('tmpl_core.item_favorite_num'))
{
	$details[] = sprintf('%s: %s', JText::_('CFAVORITED'), $item->favorite_num);
}
if($params->get('tmpl_core.item_follow_num'))
{
	$details[] = sprintf('%s: %s', JText::_('CFOLLOWERS'), $item->subscriptions_num);
}
?>

<!-- <article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
	<?php if(!$this->print):?>
		<div class="pull-right controls">
			<div class="btn-group">
				<?php if($params->get('tmpl_core.item_print')):?>
					<a class="btn btn-mini" onclick="window.open('<?php echo JRoute::_($this->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
						<?php echo HTMLFormatHelper::icon('printer.png', JText::_('CPRINT'));  ?></a>
				<?php endif;?>

				<?php if($this->user->get('id')):?>
					<?php echo HTMLFormatHelper::bookmark($item, $this->type, $params);?>
					<?php echo HTMLFormatHelper::follow($item, $this->section);?>
					<?php echo HTMLFormatHelper::repost($item, $this->section);?>
					<?php if($item->controls):?>
						<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-mini">
							<?php echo HTMLFormatHelper::icon('gear.png');  ?></a>
						<ul class="dropdown-menu">
							<?php echo list_controls($item->controls);?>
						</ul>
					<?php endif;?>
				<?php endif;?>
			</div>
		</div>
	<?php else:?>
		<div class="pull-right controls">
			<a href="#" class="btn btn-mini" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png', JText::_('CPRINT'));  ?></a>
		</div>
	<?php endif;?> -->

<article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
	<?php if(!$this->print):?>
		<div class="pull-right controls">
			<div class="btn-group">
				<?php if($params->get('tmpl_core.item_print')):?>
					<a class="btn btn-mini" onclick="window.open('<?php echo JRoute::_($this->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
						<?php echo HTMLFormatHelper::icon('printer.png', JText::_('CPRINT'));  ?></a>
				<?php endif;?>

				
			</div>
		</div>
	<?php else:?>
		<div class="pull-right controls">
			<a href="#" class="btn btn-mini" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png', JText::_('CPRINT'));  ?></a>
		</div>
	<?php endif;?>

	<!-- <?php if($params->get('tmpl_core.item_title')):?>
		<?php if($this->type->params->get('properties.item_title')):?>
			<div class="page-header">
				<<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
					<?php echo $item->title?>
					<?php echo CEventsHelper::showNum('record', $item->id);?>
				</<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
			</div>
		<?php endif;?>
	<?php endif;?>
	<div class="clearfix"></div> -->

		<?php if($params->get('tmpl_core.item_title')):?>
		<?php if($this->type->params->get('properties.item_title')):?>
		
		
		<div class="maincath1">
            <a href ="/breweries" class="btn-back"><i class="fas fa-arrow-left fa-2x"></i></a>
				<<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
					<?php echo $item->title?>
					<?php echo CEventsHelper::showNum('record', $item->id);?>
				</<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
		<!--from Рейтиг и слежение(лайки)!-->
		<?php if($item->controls):?>
			<a href="#" data-toggle="dropdown" class="btn-settings">
				<svg class="svg-inline--fa fa-tools fa-w-16 ttools" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tools" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M501.1 395.7L384 278.6c-23.1-23.1-57.6-27.6-85.4-13.9L192 158.1V96L64 0 0 64l96 128h62.1l106.6 106.6c-13.6 27.8-9.2 62.3 13.9 85.4l117.1 117.1c14.6 14.6 38.2 14.6 52.7 0l52.7-52.7c14.5-14.6 14.5-38.2 0-52.7zM331.7 225c28.3 0 54.9 11 74.9 31l19.4 19.4c15.8-6.9 30.8-16.5 43.8-29.5 37.1-37.1 49.7-89.3 37.9-136.7-2.2-9-13.5-12.1-20.1-5.5l-74.4 74.4-67.9-11.3L334 98.9l74.4-74.4c6.6-6.6 3.4-17.9-5.7-20.2-47.4-11.7-99.6.9-136.6 37.9-28.5 28.5-41.9 66.1-41.2 103.6l82.1 82.1c8.1-1.9 16.5-2.9 24.7-2.9zm-103.9 82l-56.7-56.7L18.7 402.8c-25 25-25 65.5 0 90.5s65.5 25 90.5 0l123.6-123.6c-7.6-19.9-9.9-41.6-5-62.7zM64 472c-13.2 0-24-10.8-24-24 0-13.3 10.7-24 24-24s24 10.7 24 24c0 13.2-10.7 24-24 24z"></path></svg></a>
		<!--end from Рейтиг и слежение(лайки)!-->
				<ul class="dropdown-menu incard">
					<?php echo list_controls($item->controls);?>
				</ul>
		<?php endif;?>	

				
			</div>
		
		
		
<!---------Рейтиг и слежение(лайки)------------->
		<div class="cardhead">
			<div id="comments" class="commtop">	
			<?php if($params->get('tmpl_core.item_rating')):?>
					<div class="span2">
						<?php echo $item->rating;?>
					</div>
				<?php endif;?>		
			</div>


			<div class="likecard">
                <?php if($this->user->get('id')):?>		
		
					<?php //echo HTMLFormatHelper::bookmark($item, $this->type, $params);?>
					<?php echo HTMLFormatHelper::follow($item, $this->section);?>
					<?php //echo HTMLFormatHelper::repost($item, $this->section);?>				
				<?php endif;?>	
			</div>

                <span id="share-button1" class="Share"><i class="fas fa-share-alt"></i></span>

                <div class="ya-share2" data-curtain data-shape="round" data-limit="0" data-more-button-type="short"
                     data-services="whatsapp,telegram,viber,vkontakte">
                </div>

		</div>				
		<?php endif;?>
	<?php endif;?>
    <div class="ya-share2" data-curtain data-shape="round" data-limit="0" data-more-button-type="short"
         data-services="whatsapp,telegram,viber,vkontakte">
    </div>
	<div class="clearfix"></div>
<!---------КОНЕЦ Рейтиг и слежение(лайки)------------->
	
	<?php if(isset($this->item->fields_by_groups[null])):?>
		<dl class="dl-horizontal fields-list"><!-- 1209 !-->
			<?php foreach ($this->item->fields_by_groups[null] as $field_id => $field):?>
				<dt id="<?php echo 'dt-'.$field_id; ?>" class="<?php echo $field->class;?>">
					<?php if($field->params->get('core.show_lable') > 1):?>
						<label id="<?php echo $field->id;?>-lbl">
							<?php echo $field->label; ?>
							<?php if($field->params->get('core.icon')):?>
								<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
							<?php endif;?>
						</label>
						<?php if($field->params->get('core.label_break') > 1):?>
						<?php endif;?>
					<?php endif;?>
				</dt>
				<dd id="<?php echo 'dd-'.$field_id; ?>" class="<?php echo $field->fieldclass;?><?php echo ($field->params->get('core.label_break') > 1 ? ' line-brk' : NULL) ?>">
					<?php echo $field->result; ?>
				</dd>
			<?php endforeach;?>
		</dl>
		<?php unset($this->item->fields_by_groups[null]);?>
	<?php endif;?>

	<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1)) && count($this->item->fields_by_groups)):?>
		<div class="clearfix"></div>
		<div class="tabbable <?php echo $params->get('tmpl_params.tabs_position');  ?>">
			<ul class="nav <?php echo $params->get('tmpl_params.tabs_style', 'nav-tabs');  ?>" id="tabs-list">
				<?php if(isset($this->item->fields_by_groups)):?>
					<?php foreach ($this->item->fields_by_groups as $group_id => $fields) :?>
						<li>
							<a href="#tab-<?php echo $o++?>" data-toggle="tab">
								<?php if(!empty($item->field_groups[$group_id]['icon']) && $params->get('tmpl_params.show_groupicon', 1)): ?>
									<?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon']) ?>
								<?php endif; ?>
								<?php echo JText::_($group_id)?>
							</a>
						</li>
					<?php endforeach;?>
				<?php endif;?>
			</ul>
	<?php endif;?>

	<?php if(isset($this->item->fields_by_groups)):?>
		<?php foreach ($this->item->fields_by_groups as $group_name => $fields) :?>
			<?php $started = true;?>
			<?php group_start($this, $group_name, 'tab-'.$i++);?>
			<dl class="dl-horizontal fields-list fields-group<?php echo $i;?>">
				<?php foreach ($fields as $field_id => $field):?>
					<dt id="<?php echo 'dt-'.$field_id; ?>" class="<?php echo $field->class;?>">
						<?php if($field->params->get('core.show_lable') > 1):?>
							<label id="<?php echo $field->id;?>-lbl">
								<?php echo $field->label; ?>
								<?php if($field->params->get('core.icon')):?>
									<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
								<?php endif;?>
							</label>
							<?php if($field->params->get('core.label_break') > 1):?>
							<?php endif;?>
						<?php endif;?>
					</dt>
					<dd id="<?php echo 'dd-'.$field_id; ?>" class="<?php echo $field->fieldclass;?><?php echo ($field->params->get('core.label_break') > 1 ? ' line-brk' : NULL) ?>">
						<?php echo $field->result; ?>
					</dd>
				<?php endforeach;?>
			</dl>
			<?php group_end($this);?>
		<?php endforeach;?>
	<?php endif;?>

	<?php if($started):?>
		<?php total_end($this);?>
	<?php endif;?>

	<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1))  && count($this->item->fields_by_groups)):?>
		</div>
		<div class="clearfix"></div>
		<br />
	<?php endif;?>

	<?php echo $this->loadTemplate('tags');?>
<!-- 
	<?php if($category || $author || $details || $params->get('tmpl_core.item_rating')): ?>
		<div class="well article-info">
			<div class="row-fluid">
				<?php if($params->get('tmpl_core.item_rating')):?>
					<div class="span2">
						<?php echo $item->rating;?>
					</div>
				<?php endif;?>
				<div class="span<?php echo ($params->get('tmpl_core.item_rating') ? 8 : 10);?>">
					<small>
						<dl class="dl-horizontal user-info">
							<?php if($category):?>
								<?php echo implode(' ', $category);?>
							<?php endif;?>
							<?php if($author):?>
								<dt><?php echo JText::_('Posted');?></dt>
								<dd>
									<?php echo implode(', ', $author);?>
								</dd>
							<?php endif;?>
							<?php if($details):?>
								<dt>Info</dt>
								<dd class="hits">
									<?php echo implode(', ', $details);?>
								</dd>
							<?php endif;?>
						</dl>
					</small>
				</div>
				<?php if($params->get('tmpl_core.item_author_avatar')):?>
					<div class="span2 avatar">
						<img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
					</div>
				<?php endif;?>
			</div>
		</div>
	<?php endif;?> -->

		<?php if($category || $author || $details || $params->get('tmpl_core.item_rating')): ?>
		<div class="well article-info">
		
			<div class="row-fluid">

				<div class="span<?php echo ($params->get('tmpl_core.item_rating') ? 8 : 10);?>">
					<small>
					<!--<label class="admins"><?php//echo JText::_('Профили');?></label>-->
						<li id ="admininfo" class="joms-list__item c-raft">
							<?php if($category):?>
								<?php echo implode(' ', $category);?>
							<?php endif;?>
							<?php if($author):?>
		
                      <?php if($params->get('tmpl_core.item_author_avatar')):?>
				       <div class="joms-list__avatar joms-avatar  craft">
  <svg class="svg-inline--fa fa-user-shield fa-w-20 admin" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user-shield" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M622.3 271.1l-115.2-45c-4.1-1.6-12.6-3.7-22.2 0l-115.2 45c-10.7 4.2-17.7 14-17.7 24.9 0 111.6 68.7 188.8 132.9 213.9 9.6 3.7 18 1.6 22.2 0C558.4 489.9 640 420.5 640 296c0-10.9-7-20.7-17.7-24.9zM496 462.4V273.3l95.5 37.3c-5.6 87.1-60.9 135.4-95.5 151.8zM224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm96 40c0-2.5.8-4.8 1.1-7.2-2.5-.1-4.9-.8-7.5-.8h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c6.8 0 13.3-1.5 19.2-4-54-42.9-99.2-116.7-99.2-212z"></path></svg>
						<img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
					     </div>
				          <?php endif;?>
						  <div class="joms-list__body craft">
								<dd class="admf">
									<?php echo implode(', ', $author);?>
								</dd>
								</div>
							<?php endif;?>
							<?php if($details):?>
								<dt class="detall">Info</dt>
								<dd class="hits">
									<?php echo implode(', ', $details);?>
								</dd>
							<?php endif;?>
						</li>
					</small>
				</div>
				
			</div>
		</div>
	<?php endif;?>
</article>

<?php if($started):?>
	<script type="text/javascript">
		<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1))):?>
			jQuery('#tabs-list a:first').tab('show');
		<?php elseif(in_array($params->get('tmpl_params.item_grouping_type', 0), array(2))):?>
			jQuery('#tab-main').collapse('show');
		<?php endif;?>
	</script>
<?php endif;?>






<?php
function group_start($data, $label, $name)
{
	static $start = false;
	$icon = '';
	if(!empty($data->item->field_groups[$label]['icon']) && $data->tmpl_params['record']->get('tmpl_params.show_groupicon', 1)) {
		$icon = HTMLFormatHelper::icon($data->item->field_groups[$label]['icon']);
	}
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			if(!$start)
			{
				echo '<div class="tab-content" id="tabs-box">';
				$start = TRUE;
			}
			echo '<div class="tab-pane" id="'.$name.'">';
			break;
		//slider
		case 2:
			if(!$start)
			{
				echo '<div class="accordion" id="accordion2">';
				$start = TRUE;
			}
			echo '<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#'.$name.'">
					     '.$icon. ' '. $label.'
					</a>
				</div>
				<div id="'.$name.'" class="accordion-body collapse">
					<div class="accordion-inner">';
			break;
		// fieldset
		case 3:
			echo "<legend>{$icon} {$label}</legend>";
		break;
	}

	if($data->tmpl_params['record']->get('tmpl_params.show_groupdescr') && !empty($data->item->field_groups[$label]['descr']))
	{
		echo $data->item->field_groups[$label]['descr'];
	}
}

function group_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div></div></div>';
		break;
	}
}

function total_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div>';
		break;
	}
}
?>
<style>
    .ya-share2__list.ya-share2__list_direction_horizontal > .ya-share2__item {
        margin: 2px 18px 0 0 !important;
    }
    .ya-share2.ya-share2_inited {
        position: absolute;
        right: 90px;
    }
    .ya-share2__container_size_m .ya-share2__item_more.ya-share2__item_has-pretty-view .ya-share2__link_more.ya-share2__link_more-button-type_short {
        background: rgb(255 200 55) !important;
        padding: 0 !important;
    }
    .ya-share--g {
        position: absolute !important;
        right: 20px !important;
        background:rgb(255 200 55);
        padding: 6px;
        border-radius: 50%;
        width: 32px;
        height: 32px;
    }
    svg.svg-inline--fa.fa-share-alt.fa-w-14 {
        width: 18px;
        height: 18px;
        color: #253544;
        margin-top: 2px;
    }
    span#share-button1 {
        padding: 5px;
        background: rgb(255 200 55);
        border-radius: 50%;
        width: 32px;
    }
    svg.svg-inline--fa.fa-share-alt.fa-w-14 {
        width: 18px;
        height: 18px;
        color: #253544;
        margin-top: 2px;
        margin-left: 1px;
    }
</style>
<!--<script src="https://yastatic.net/share2/share.js"></script>-->
<!--<script src="/templates/g5_helium/js/share.js"></script>-->
<!--<script src="/templates/g5_helium/js/share2.js"></script>-->
<script>

    var shareButton = document.getElementById('share-button1');
    shareButton.addEventListener('click', function () {
        if (navigator.share) {
            navigator.share({
                title: "CRAFT",
                text: "",
                url: window.location.href
            })
                .then(function () {
                    console.log("Shareing successfull")
                })
                .catch(function () {
                    console.log("Sharing failed")
                })

        } else {
            console.log("Sorry! Your browser does not support Web Share API")
        }
    })
</script>
