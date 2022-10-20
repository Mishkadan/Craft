<?php
/**
 * Cobalt by MintJoomla
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: http://www.mintjoomla.com/
 * @copyright Copyright (C) 2012 MintJoomla (http://www.mintjoomla.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$started = false;
$params = $this->tmpl_params;
if($params->get('tmpl_params.form_grouping_type', 0))
{
    $started = true;
}
$k = 0;

?>
<style>
    .licon {
        float: right;
        margin-left: 5px;
    }
    .line-brk {
        margin-left: 0px !important;
    }
    .editor textarea {
        box-sizing: border-box;
    }
    .control-group {
        margin-bottom: 10px;
        padding: 8px 0;
        -webkit-transition: all 200ms ease-in-out;
        -moz-transition: all 200ms ease-in-out;
        -o-transition: all 200ms ease-in-out;
        -ms-transition: all 200ms ease-in-out;
        transition: all 200ms ease-in-out;
    }
    .highlight-element {
        -webkit-animation-name: glow;
        -webkit-animation-duration: 1.5s;
        -webkit-animation-iteration-count: 1;
        -webkit-animation-direction: alternate;
        -webkit-animation-timing-function: ease-out;
        
        -moz-animation-name: glow;
        -moz-animation-duration: 1.5s;
        -moz-animation-iteration-count: 1;
        -moz-animation-direction: alternate;
        -moz-animation-timing-function: ease-out;
        
        -ms-animation-name: glow;
        -ms-animation-duration: 1.5s;
        -ms-animation-iteration-count: 1;
        -ms-animation-direction: alternate;
        -ms-animation-timing-function: ease-out;
    }


    .control-group input,select{ font-weight: 600; font-size: 16px; line-height: 150%; text-indent: 10px; letter-spacing: 0.005em; color: #253544; background: #fff; border: 1px solid #DDE0E2 !important; box-sizing: border-box; box-shadow: 0px 6px 24px rgb(37 53 68 / 15%); border-radius: 20px !important; height: 44px !important; margin-bottom: 14px !important; margin-top: 5px !important;}

    .form-horizontal .control-label {
        font-weight: 600;
        font-size: 16px;
        line-height: 150%;
        letter-spacing: 0.005em;
        font-feature-settings: "tnum" on, "lnum" on;
        color: #253544;
    }

    .hidden{
        display: none;
    }
    <?php echo $params->get('tmpl_params.css');?>
@-webkit-keyframes glow {   
    0% {
        background-color: #fdd466;
    }   
    100% {
        background-color: transparent;
    }
}
@-moz-keyframes glow {  
    0% {
        background-color: #fdd466;
    }   
    100% {
        background-color: transparent;
    }
}

@-ms-keyframes glow {
    0% {
        background-color: #fdd466;
    }   
    100% {
        background-color: transparent;
    }
}
    
</style>

<div class="form-horizontal">
<?php if(in_array($params->get('tmpl_params.form_grouping_type', 0), array(1, 4))):?>
    <div class="tabbable<?php if($params->get('tmpl_params.form_grouping_type', 0) == 4) echo ' tabs-left' ?>">
        <ul class="nav nav-tabs" id="tabs-list">
            <li><a href="#tab-main" data-toggle="tab"><?php echo JText::_($params->get('tmpl_params.tab_main', 'Main'));?></a></li>

            <?php if(isset($this->sorted_fields)):?>
                <?php foreach ($this->sorted_fields as $group_id => $fields) :?>
                    <?php if($group_id == 0) continue;?>
                    <li><a class="taberlink" href="#tab-<?php echo $group_id?>" data-toggle="tab"><?php echo HTMLFormatHelper::icon($this->field_groups[$group_id]['icon'])?> <?php echo $this->field_groups[$group_id]['name']?></a></li>
                <?php endforeach;?>
            <?php endif;?>

            <?php if(count($this->meta)):?>
                <li><a href="#tab-meta" data-toggle="tab"><?php echo JText::_('Meta Data');?></a></li>
            <?php endif;?>
            <?php if(count($this->core_admin_fields)):?>
                <li><a href="#tab-special" data-toggle="tab"><?php echo JText::_('Special Fields');?></a></li>
            <?php endif;?>
            <?php if(count($this->core_fields)):?>
                <li><a href="#tab-core" data-toggle="tab"><?php echo JText::_('Core Fields');?></a></li>
            <?php endif;?>
        </ul>
<?php endif;?>
    <?php group_start($this, $params->get('tmpl_params.tab_main', 'Main'), 'tab-main');?>

    <?php if($params->get('tmpl_params.tab_main_descr')):?>
        <?php echo $params->get('tmpl_params.tab_main_descr'    ); ?>
    <?php endif;?>

    <?php if($this->type->params->get('properties.item_title', 1) == 1):?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="title-lbl" for="jform_title" class="control-label" >
                <?php if($params->get('tmpl_core.form_title_icon', 1)):?>
                    <?php echo HTMLFormatHelper::icon($params->get('tmpl_core.item_icon_title_icon', 'edit.png'));  ?>
                <?php endif;?>

                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_title', 'Title')) ?>
                <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>">
                    <!-- <?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
            </label>
            <div class="controls123">
                <div id="field-alert-title" class="alert alert-error" style="display:none"></div>
                <div class="row-fluid">
                    <?php echo $this->form->getInput('title'); ?>
                </div>
            </div>
        </div>
    <?php else :?>
        <input type="hidden" name="jform[title]" value="<?php echo htmlentities(!empty($this->item->title) ? $this->item->title : JText::_('CNOTITLE').': '.time(), ENT_COMPAT, 'UTF-8')?>" />
    <?php endif;?>

    <?php if($this->anywhere) : ?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="anywhere-lbl" class="" >
                <?php if($params->get('tmpl_core.form_anywhere_icon', 1)):?>
                    <?php echo HTMLFormatHelper::icon('document-share.png');  ?>
                <?php endif;?>

                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_anywhere', 'Where to post')) ?>
                <!-- <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
            </label>
            <div class="controls123">
                <div id="field-alert-anywhere" class="alert alert-error" style="display:none"></div>
                <?php echo JHTML::_('cusers.wheretopost', @$this->item); ?>
            </div>
        </div>
        
            
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="anywherewho-lbl" for="whorepost" class="" >
                <?php if($params->get('tmpl_core.form_anywhere_who_icon', 1)):?>
                    <?php echo HTMLFormatHelper::icon('arrow-retweet.png');  ?>
                <?php endif;?>

                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_anywhere_who', 'Who can repost')) ?>
            </label>
            <div class="controls123">
                <div id="field-alert-anywhere" class="alert alert-error" style="display:none"></div>
                <?php echo $this->form->getInput('whorepost'); ?>
            </div>
        </div>
    <?php endif;?>

    <?php if(in_array($this->params->get('submission.allow_category'), $this->user->getAuthorisedViewLevels()) && $this->section->categories):?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <?php if($this->catsel_params->get('tmpl_core.category_label', 0)):?>
                <label id="category-lbl" for="category" class="" >
                    <?php if($params->get('tmpl_core.form_category_icon', 1)):?>
                        <?php echo HTMLFormatHelper::icon('category.png');  ?>
                    <?php endif;?>

                    <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

                    <?php if(!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
                        <!-- <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
                    <?php endif;?>
                </label>
            <?php endif;?>
            <div class="controls123">
                <div id="field-alert-category" class="alert alert-error" style="display:none"></div>
                <?php if(!empty($this->allow_multi_msg)): ?>
                    <div class="alert alert-warning">
                        <?php echo JText::_($this->type->params->get('emerald.type_multicat_subscription_msg')); ?>
                        <a href="<?php echo EmeraldApi::getLink('list', TRUE, $this->type->params->get('emerald.type_multicat_subscription')); ?>"><?php echo JText::_('CSUBSCRIBENOW'); ?></a>
                    </div>
                <?php endif;?>
                <?php echo $this->loadTemplate('category_'.$params->get('tmpl_params.tmpl_category', 'default')); ?>
            </div>
        </div>
    <?php elseif(!empty($this->category->id)):?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="category-lbl" for="category" class="">
                <?php if($params->get('tmpl_core.form_category_icon', 1)):?>
                    <?php echo HTMLFormatHelper::icon('category.png');  ?>
                <?php endif;?>

                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

                <?php if(!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
                <!--    <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
                <?php endif;?>
            </label>
            <div class="controls123">
                <div id="field-alert-category" class="alert alert-error" style="display:none"></div>
                <?php echo $this->section->name;?> <?php echo $this->category->crumbs; ?>
            </div>
        </div>
    <?php endif;?>

    
    <?php if($this->ucategory) : ?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="ucategory-lbl" for="ucatid" class="" >
                <?php if($params->get('tmpl_core.form_ucategory_icon', 1)):?>
                    <?php echo HTMLFormatHelper::icon('category.png');  ?>
                <?php endif;?>

                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_ucategory', 'Category')) ?>

                <!-- <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
            </label>
            <div class="controls123">
                <div id="field-alert-ucat" class="alert alert-error" style="display:none"></div>
                <?php echo $this->form->getInput('ucatid'); ?>
            </div>
        </div>
    <?php else:?>
        <?php $this->form->setFieldAttribute('ucatid', 'type', 'hidden'); ?>
        <?php $this->form->setValue('ucatid', null, '0'); ?>
        <?php echo $this->form->getInput('ucatid'); ?>
    <?php endif;?>

    <?php if($this->multirating):?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="jform_multirating-lbl" class="control-label" for="jform_multirating" >
                <?php echo strip_tags($this->form->getLabel('multirating'));?>
            <!--    <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
            </label>
            <div class="controls123">
                <div id="field-alert-rating" class="alert alert-error" style="display:none"></div>
                <?php echo $this->multirating;?>
            </div>
        </div>
    <?php endif;?>


    <?php if(isset($this->sorted_fields[0])):?>
        <?php foreach ($this->sorted_fields[0] as $field_id => $field):?>
            <div id="fld-<?php echo $field->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-'.$field_id; ?> <?php echo $field->fieldclass;?>">
                <?php if(1):?>

                    <label id="lbl-<?php echo $field->id;?>" for="field_<?php echo $field->id;?>" class="
                        <?php $name = $field->label;?>
                        <?php if($name == "Название"):?>
                            lbl-add
                        <?php elseif($name == "Логотип"):?>
                            lbl-add
                        <?php elseif($name == 'Где мы находимся:'):?>
                            hidden
                        <?php else:?>
                        lbl-add
                        <?php endif;?>

                         <?php echo $field->class;?>" >
                        <?=$name?>

                        <?php if($field->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                            <?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
                        <?php endif;?>
                            
                        
                        <?php if ($field->required): ?>
                            <!-- <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
                        <?php endif;?>

                        <?php if ($field->description):?>
                            <span class="pull-right" rel="tooltip" style="cursor: help;"  data-original-title="<?php echo htmlentities(($field->translateDescription ? JText::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8');?>">
                                <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                            </span>
                        <?php endif;?>

                        <!---<?php echo $field->label; ?>!-->
                            
                    </label>
                    <?php if(in_array($field->params->get('core.label_break'), array(1,3))):?>
                        <div style="clear: both;"></div>
                    <?php endif;?>
                <?php endif;?>

                <div class="controls123<?php if(in_array($field->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($field->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $field->fieldclass  ?>">
                    <div id="field-alert-<?php echo $field->id?>" class="alert alert-error" style="display:none"></div>
                    <?php echo $field->result; ?>
                </div>
            </div>
        <?php endforeach;?>
        <?php unset($this->sorted_fields[0]);?>
    <?php endif;?>

     <section id="wineriesadd" class="joms-notifications ">

                <a id="exit1" class="btn-submitadd" onclick="Joomla.submitbutton('form.cancel');">
                    <i class="fas fa-arrow-left fa-2x"></i>Отменить</a>

                <a onclick="joms.popup()" title="addpost">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g filter="url(#filter0_i_550_4603)">
                            <path d="M0 5C0 2.23858 2.23858 0 5 0H27C29.7614 0 32 2.23858 32 5V27C32 29.7614 29.7614 32 27 32H5C2.23858 32 0 29.7614 0 27V5Z"
                                  fill="#F1BA2C"/>
                            <path d="M25.3333 14.6668H17.3333V6.66683C17.3333 6.31321 17.1929 5.97407 16.9428 5.72402C16.6928 5.47397 16.3536 5.3335 16 5.3335C15.6464 5.3335 15.3072 5.47397 15.0572 5.72402C14.8072 5.97407 14.6667 6.31321 14.6667 6.66683V14.6668H6.66668C6.31305 14.6668 5.97392 14.8073 5.72387 15.0574C5.47382 15.3074 5.33334 15.6465 5.33334 16.0002C5.33334 16.3538 5.47382 16.6929 5.72387 16.943C5.97392 17.193 6.31305 17.3335 6.66668 17.3335H14.6667V25.3335C14.6667 25.6871 14.8072 26.0263 15.0572 26.2763C15.3072 26.5264 15.6464 26.6668 16 26.6668C16.3536 26.6668 16.6928 26.5264 16.9428 26.2763C17.1929 26.0263 17.3333 25.6871 17.3333 25.3335V17.3335H25.3333C25.687 17.3335 26.0261 17.193 26.2762 16.943C26.5262 16.6929 26.6667 16.3538 26.6667 16.0002C26.6667 15.6465 26.5262 15.3074 26.2762 15.0574C26.0261 14.8073 25.687 14.6668 25.3333 14.6668Z"
                                  fill="white"/>
                        </g>
                        <defs>
                            <filter id="filter0_i_550_4603" x="0" y="0" width="32" height="40"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                               values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                <feOffset dy="8"/>
                                <feGaussianBlur stdDeviation="12"/>
                                <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                                <feColorMatrix type="matrix"
                                               values="0 0 0 0 0.145098 0 0 0 0 0.207843 0 0 0 0 0.266667 0 0 0 0.15 0"/>
                                <feBlend mode="normal" in2="shape" result="effect1_innerShadow_550_4603"/>
                            </filter>
                        </defs>
                    </svg>

                </a>

                <a id="saveandexit1" class="btn-submitadd" onclick="Joomla.submitbutton('form.save');">
                    Сохранить<i class="fas fa-save fa-2x"></i></i></a>

            </section>

    <?php if((MECAccess::allowAccessAuthor($this->type, 'properties.item_can_add_tag', $this->item->user_id) || MECAccess::allowUserModerate($this->user, $this->section, 'allow_tags') ) &&
        $this->type->params->get('properties.item_can_view_tag')):?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="tags-lbl" for="tags" class="" >
                <?php if($params->get('tmpl_core.form_tags_icon', 1)):?>
                    <?php echo HTMLFormatHelper::icon('price-tag.png');  ?>
                <?php endif;?>
                <?php echo JText::_($this->tmpl_params->get('tmpl_core.form_label_tags', 'Tags')) ?>
            </label>
            <div class="controls123">
                <?php //echo JHtml::_('tags.tagform', $this->section, json_decode($this->item->tags, TRUE), array(), 'jform[tags]'); ?>
                <?php echo $this->form->getInput('tags'); ?>
            </div>
        </div>
    <?php endif;?>

    <?php group_end($this);?>


    <?php if(isset($this->sorted_fields)):?>
        <?php foreach ($this->sorted_fields as $group_id => $fields) :?>
            <?php $started = true;?>
            <?php group_start($this, $this->field_groups[$group_id]['name'], 'tab-'.$group_id);?>
            <?php if(!empty($this->field_groups[$group_id]['descr'])):?>
                <?php echo $this->field_groups[$group_id]['descr'];?>
            <?php endif;?>
            <?php foreach ($fields as $field_id => $field):?>
                <div id="fld-<?php echo $field->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-'.$field_id; ?> <?php echo $field->fieldclass;?>">
                    <?php if($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3):?>
                        <label id="lbl-<?php echo $field->id;?>" for="field_<?php echo $field->id;?>" class=" <?php echo $field->class;?>" >
                            <?php if($field->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
                                <?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
                            <?php endif;?>
                            <?php if ($field->required): ?>
                                <!-- <span class="pull-right" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span> -->
                            <?php endif;?>

                            <?php if ($field->description):?>
                                <span class="pull-right" rel="tooltip" style="cursor: help;" data-original-title="<?php echo htmlspecialchars(($field->translateDescription ? JText::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8');?>">
                                    <?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
                                </span>
                            <?php endif;?>
                            <?php echo $field->label; ?>
                        </label>
                        <?php if(in_array($field->params->get('core.label_break'), array(1,3))):?>
                            <div style="clear: both;"></div>
                        <?php endif;?>
                    <?php endif;?>

                    <div class="controls123<?php if(in_array($field->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($field->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $field->fieldclass  ?>">
                        <div id="field-alert-<?php echo $field->id?>" class="alert alert-error" style="display:none"></div>
                        <?php echo $field->result; ?>
                    </div>
                </div>
            <?php endforeach;?>
            <?php group_end($this);?>
        <?php endforeach;?>
    <?php endif; ?>

    <?php if(count($this->meta)):?>
        <?php $started = true?>
        <?php group_start($this, JText::_('CSEO'), 'tab-meta');?>
            <?php foreach ($this->meta as $label => $meta_name):?>
                <div class="control-group odd<?php echo $k = 1 - $k ?>">
                    <label id="jform_meta_descr-lbl" class="control-label" title="" for="jform_<?php echo $meta_name;?>">
                    <?php echo JText::_($label); ?>
                    </label>
                    <div class="controls123">
                        <div class="row-fluid">
                            <?php echo $this->form->getInput($meta_name); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>

        <?php group_end($this);?>
    <?php endif;?>
    


    <?php if(count($this->core_admin_fields)):?>
        <?php $started = true?>
        <?php group_start($this, 'Special Fields', 'tab-special');?>
            <div class="admin">
            <?php foreach($this->core_admin_fields as $key => $field ):?>
                <div class="control-group odd<?php echo $k = 1 - $k ?>">
                    <label id="jform_<?php echo $field?>-lbl" class="control-label" for="jform_<?php echo $field?>" ><?php echo strip_tags($this->form->getLabel($field));?></label>
                    <div class="controls field-<?php echo $field;  ?>">
                        <?php echo $this->form->getInput($field); ?>
                    </div>
                </div>
            <?php endforeach;?>
            </div>
        <?php group_end($this);?>
    <?php endif;?>  

    <?php if(count($this->core_fields)):?>
        <?php group_start($this, 'Core Fields', 'tab-core');?>
        <?php foreach($this->core_fields as $key => $field ):?>
            <div class="control-group odd<?php echo $k = 1 - $k ?>">
                <label id="jform_<?php echo $field?>-lbl" class="control-label" for="jform_<?php echo $field?>" >
                    <?php if($params->get('tmpl_core.form_'.$field.'_icon', 1)):?>
                        <?php echo HTMLFormatHelper::icon('core-'.$field.'.png');  ?>
                    <?php endif;?>
                    <?php echo strip_tags($this->form->getLabel($field));?>
                </label>
                <div class="controls123">
                    <?php echo $this->form->getInput($field); ?>
                </div>
            </div>
        <?php endforeach;?>
        <?php group_end($this);?>
    <?php endif;?>

    <?php if($started):?>
        <?php total_end($this);?>
    <?php endif;?>
    <br />
</div>

<script type="text/javascript">
    <?php if(in_array($params->get('tmpl_params.form_grouping_type', 0), array(1,4))):?>
        jQuery('#tabs-list a:first').tab('show');
    <?php elseif(in_array($params->get('tmpl_params.form_grouping_type', 0), array(2))):?>
        jQuery('#tab-main').collapse('show');
    <?php endif;?>
</script>






<?php
function group_start($data, $label, $name)
{
    static $start = false;
    switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
    {
        //tab
        case 4:
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
                         '.$label.'
                    </a>
                </div>
                <div id="'.$name.'" class="accordion-body collapse">
                    <div class="accordion-inner">';
            break;
        // fieldset
        case 3:
            if($name != 'tab-main') {
                echo "<legend>{$label}</legend>";
            }
        break;
    }
}

function group_end($data)
{
    switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
    {
        case 4:
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
    switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
    {
        //tab
        case 4:
        case 1:
            echo '</div></div>';
        break;
        case 2:
            echo '</div>';
        break;
    }
}
