<?php
/**
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
 * @license       GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author        iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
defined('_JEXEC') or die();

$user     = CFactory::getUser();
$config   = CFactory::getConfig();
$enablepm = $config->get('enablepm');
$cover = CFactory::getUser()->_chat_cover;
$background = $cover ? 'url(/images/chatcovers/'.$cover.')' : 'url(/images/wine-bkg.jpg)';
?>
<?php if ($enablepm): ?>
    <div class="joms-page joms-js-page-chat-loading" style="background:<?= $background?>; background-position: center; background-size: cover">
        <div style="text-align:center; padding:50px 0">
            <div class="joms-js-loading" style="padding:14px; text-align:center">
                <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif" alt=""/>
            </div>
            <div class="alert alert-notice joms-js-loading-error" style="display:none">
                <div><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_ERROR'); ?></div>
            </div>
            <div class="joms-js-loading-empty" style="display:none">
                <h1><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_TITLE'); ?></h1>
                <p><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_DESCRIPTION'); ?></p>
                <br/>
                <span class="joms-button--primary joms-button--small joms-js-loading-no-conv" style="display:none">
                <?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_BTN'); ?>
            </span>
                <div class="alert alert-notice joms-js-loading-no-friend" style="display:none">
                    <div><?php echo JText::_('COM_COMMUNITY_CHAT_NOCONV_NOFRIEND'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="joms-page joms-page--chat joms-js-page-chat" style="display:none">
        <div class="joms-chat__wrapper js--bkg" style="background:<?= $background?>">
            <input id = "js--bkg-input" type="file" name="file[]" accept="image/*" hidden/>
            <!-- Sidebar -->
            <div id="joms-chat1" class="joms-chat__conversations-wrapper">
                <div class="joms-chat__search">


                    <div class="joms-js--chat-header">
                        <a class="back" onclick="window.location.href = '/';">
                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.6669 7.66675H4.54685L8.94685 3.28008C9.19793 3.02901 9.33898 2.68849 9.33898 2.33342C9.33898 1.97835 9.19793 1.63782 8.94685 1.38675C8.69578 1.13568 8.35526 0.994629 8.00019 0.994629C7.64512 0.994629 7.30459 1.13568 7.05352 1.38675L0.386854 8.05342C0.265467 8.18022 0.170313 8.32975 0.106854 8.49342C-0.0265034 8.81803 -0.0265034 9.18214 0.106854 9.50675C0.170313 9.67042 0.265467 9.81995 0.386854 9.94675L7.05352 16.6134C7.17747 16.7384 7.32494 16.8376 7.48742 16.9053C7.6499 16.973 7.82417 17.0078 8.00019 17.0078C8.1762 17.0078 8.35048 16.973 8.51296 16.9053C8.67544 16.8376 8.8229 16.7384 8.94685 16.6134C9.07183 16.4895 9.17102 16.342 9.23871 16.1795C9.3064 16.017 9.34125 15.8428 9.34125 15.6667C9.34125 15.4907 9.3064 15.3165 9.23871 15.154C9.17102 14.9915 9.07183 14.844 8.94685 14.7201L4.54685 10.3334H14.6669C15.0205 10.3334 15.3596 10.1929 15.6097 9.94289C15.8597 9.69284 16.0002 9.35371 16.0002 9.00008C16.0002 8.64646 15.8597 8.30732 15.6097 8.05728C15.3596 7.80723 15.0205 7.66675 14.6669 7.66675Z"
                                      fill="white"/>
                            </svg>
                        </a>
                        <div class="js-forhide">
                            <div class="joms-js--chat-header-info">
                                <div class="joms-chat__header">
                                    <div class="joms-chat__recipents"></div>
                                    <div class="joms-chat__actions">
                                        <script type="text/template" id="joms-js-template-chat-sidebar-item">
                                            <div class="joms-chat__item joms-js--chat-item-{{= data.id }} {{= +data.unread ? 'unread' : '' }} {{= +data.active ? 'active' : '' }} {{= data.online ? 'joms-online' : '' }}"
                                                 data-chat-type="{{= data.type }}" data-chat-id="{{= data.id }}" data-users-id ="{{= data.users_id }}">
                                                <span class="msgcount" data-unread =""></span>
                                                <div class="avaava joms-avatar {{= data.online ? 'joms-online' : '' }}">
                                                    <a href="#">
                                                        <img src="{{= data.avatar }}"/>
                                                    </a>
                                                </div>
                                                <div class="joms-chat__item-body">
                                                    <b class="toupp" class="lable-name" href="#">{{= data.name }}</b>
                                                    <span class="joms-js--chat-item-msg"></span>
                                                </div>
                                                {{ if (data.mute) { }}
                                                <div class="joms-chat__item-actions">
                                                    <svg viewBox="0 0 16 16" class="joms-icon">
                                                        <use xlink:href="#joms-icon-close"></use>
                                                    </svg>
                                                </div>
                                                {{ } }}
                                            </div>



                                        </script>

                                    </div>
                                </div>
                            </div>
                            <div class="joms-chat__search-box" style="position: relative;">

                                <!--                                <button type="button"  id="subscribe">+</button>-->

                                <span class="title-chat">Чат</span>
                                <input id ="seach-message"
                                       class="joms-input joms-chat__search_conversation"
                                       type="text"
                                       maxlength="50"
                                       placeholder="<?php echo JText::_('COM_COMMUNITY_CHAT_SEARCH'); ?>"/>
                                <svg class="search" width="28" height="28" viewBox="0 0 28 28" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M26.9465 25.0534L21.9999 20.1468C23.92 17.7527 24.8499 14.7138 24.5983 11.6552C24.3467 8.59652 22.9328 5.75049 20.6472 3.70231C18.3617 1.65412 15.3783 0.559463 12.3104 0.643411C9.24259 0.727358 6.3235 1.98353 4.15339 4.15364C1.98329 6.32375 0.727114 9.24283 0.643166 12.3107C0.559219 15.3785 1.65388 18.3619 3.70206 20.6475C5.75025 22.933 8.59627 24.3469 11.6549 24.5985C14.7136 24.8501 17.7524 23.9202 20.1465 22.0001L25.0532 26.9068C25.1772 27.0318 25.3246 27.1309 25.4871 27.1986C25.6496 27.2663 25.8239 27.3012 25.9999 27.3012C26.1759 27.3012 26.3502 27.2663 26.5126 27.1986C26.6751 27.1309 26.8226 27.0318 26.9465 26.9068C27.1869 26.6582 27.3212 26.3259 27.3212 25.9801C27.3212 25.6343 27.1869 25.3021 26.9465 25.0534ZM12.6665 22.0001C10.8206 22.0001 9.01608 21.4527 7.48122 20.4272C5.94636 19.4016 4.75008 17.9439 4.04366 16.2385C3.33725 14.5331 3.15241 12.6564 3.51254 10.8459C3.87267 9.03545 4.76159 7.37241 6.06688 6.06712C7.37217 4.76183 9.03521 3.87292 10.8457 3.51279C12.6562 3.15266 14.5328 3.33749 16.2383 4.04391C17.9437 4.75033 19.4014 5.9466 20.4269 7.48146C21.4525 9.01632 21.9999 10.8208 21.9999 12.6668C21.9999 15.1421 21.0165 17.5161 19.2662 19.2664C17.5159 21.0168 15.1419 22.0001 12.6665 22.0001Z"
                                          fill="white"/>
                                </svg>
                                <span class="searchclose" style="display: none">+</span>
                            </div>
                        </div>

                        <div class="joms-focus__button--options--desktop joms-js--chat-options">
                            <a id="chat-m-btn" href="#" ">
                            <svg width="5" height="25" viewBox="0 0 5 25" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <ellipse cx="2.5" cy="2.47429" rx="2.5" ry="2.47429" fill="white"/>
                                <ellipse cx="2.5" cy="12.5002" rx="2.5" ry="2.47429" fill="white"/>
                                <ellipse cx="2.5" cy="22.5256" rx="2.5" ry="2.47429" fill="white"/>
                            </svg>
                            </a>
                            <div class="chatmenu-lyout" style="display: none">
                                <ul id="chatmenu" class="joms-dropdown joms-js--chat-dropdown chat-menu">
                                    <li class="drop1 joms-js--chat-new-message">
                                        <svg class="svg-inline--fa fa-user nnn1" aria-hidden="true" focusable="false"
                                             data-prefix="far" data-icon="user" role="img"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 448 512" data-fa-i2svg="">
                                            <path d="M313.6 304c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 304 0 364.2 0 438.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-25.6c0-74.2-60.2-134.4-134.4-134.4zM400 464H48v-25.6c0-47.6 38.8-86.4 86.4-86.4 14.6 0 38.3 16 89.6 16 51.7 0 74.9-16 89.6-16 47.6 0 86.4 38.8 86.4 86.4V464zM224 288c79.5 0 144-64.5 144-144S303.5 0 224 0 80 64.5 80 144s64.5 144 144 144zm0-240c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z"></path>
                                        </svg>
                                        Новый чат
                                    </li>
                                    <li class="drop1 joms-js--chat-mute" style="cursor:pointer;"
                                        data-text-mute="<?php echo JText::_('COM_COMMUNITY_CHAT_MUTE') ?>"
                                        data-text-unmute="<?php echo JText::_('COM_COMMUNITY_CHAT_UNMUTE') ?>">
                                    </li>
                                    <li class="drop1 joms-js--chat-change-active-group-name"
                                        style="cursor:pointer; display:none">
                                        <?php echo JText::_('COM_COMMUNITY_CHAT_CHANGE_NAME') ?>
                                    </li>
                                    <li class="drop1 joms-js--chat-add-people" style="cursor:pointer; display:none"
                                        onclick="joms.popup.chat.addRecipient();">
                                        <?php echo JText::_('COM_COMMUNITY_CHAT_ADD_PEOPLE') ?>
                                    </li>
                                    <li class="drop1 joms-js--all-peoples" style="cursor:pointer; display:none">
                                        <i class="fas fa-users nnn1"></i>
                                        <?php echo 'Участники чата' ?>
                                    </li>
                                    <li class="drop1 joms-js--change-background" style="cursor:pointer; display:">
                                        <i class="far fa-image nnn1"></i>
                                        <label for="js--bkg-input">
                                            <?php echo 'Изменить фон чата'?>
                                        </label>
                                    </li>
                                    <li class="drop1 joms-js--change-ava" style="cursor:pointer; display:none">
                                        <i class="far fa-user-circle nnn1"></i>
                                        <?php echo 'Изменить аватар чата'?>
                                    </li>
                                    <li class="drop1 joms-js--chat-leave" style="cursor:pointer">
                                        <?php echo JText::_('COM_COMMUNITY_CHAT_LEAVE_CHAT') ?>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                </div>

                <!--                <div class="joms-chat__search-results" style="display:none;">-->
                <!--                    <div class="joms-js__results-list" style="max-height: 495px; overflow:auto;">-->
                <!--                        <div class="joms-js__result-heading">--><?php //echo JText::_('COM_COMMUNITY_CHAT_SEARCH_CONTACT_RESULTS') ?><!--</div>-->
                <!--                        <div class="joms-js__contact-results">-->
                <!--                        </div>-->
                <!--                        <div class="joms-js--chat-sidebar-loading" style="text-align:center;display:none">-->
                <!--                            <img src="--><?php //echo JURI::root(true) ?><!--/components/com_community/assets/ajax-loader.gif"-->
                <!--                                 alt="loader"/>-->
                <!--                        </div>-->
                <!---->
                <!--                        <div class="joms-js__result-heading">--><?php //echo JText::_('COM_COMMUNITY_CHAT_SEARCH_GROUP_RESULTS') ?><!--</div>-->
                <!--                        <div class="joms-js__group-results">-->
                <!--                        </div>-->
                <!--                        <div class="joms-js--chat-sidebar-loading" style="text-align:center;display:none">-->
                <!--                            <img src="--><?php //echo JURI::root(true) ?><!--/components/com_community/assets/ajax-loader.gif"-->
                <!--                                 alt="loader"/>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->


                <div class="joms-chat__conversations">
                    <div class="joms-js-list" style="display:none; overflow:auto;">
                        <div class="joms-js--chat-sidebar-loading" style="text-align:center;display:none">
                            <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif"
                                 alt="loader"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wrapping one conversation -->
            <div class="joms-chat__messages-wrapper">
                <div class="joms-js--chat-header">

                    <div class="joms-js--chat-header-selector" style="display:none">
                        <div class="joms-chat__search">
                            <div class="joms-chat-selected"></div>
                            <input class="joms-input joms-chat__search_user" type="text"
                                   placeholder="<?php echo JText::_('COM_COMMUNITY_CHAT_TYPE_YOUR_FRIEND_NAME'); ?>"/>
                            <div style="position:relative">
                                <div class="joms-js--chat-header-selector-div"
                                     data-text-no-result="<?php echo JText::_('COM_COMMUNITY_CHAT_NO_RESULT') ?>"
                                     style="display:none;background:white;border:1px solid rgba(0,0,0,.2);border-top:0 none;left:0;padding:5px;position:absolute;right:0;top:0px;z-index:1">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="joms-chat__messages" style="position:relative;">
                    <div class="joms-js--chat-conversation-loading"
                         style="position: absolute;left: 49%;text-align:center;display:none">
                        <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif"
                             alt="loader"/>
                    </div>
                    <div id ="joms-js--chat-conversation-messages" class="joms-js--chat-conversation-messages"
                         style="padding-top: 20px;height:350px;overflow:auto;"></div>
                    <div class="joms-js--chat-conversation-no-participants" style="display:none;">
                        <div class="alert alert-notice">
                            <div><?php echo JText::_('COM_COMMUNITY_CHAT_NO_PARTICIPANTS'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="joms-chat__messagebox craft-chat__messagebox" style="position:relative;">




                    <div id="smile-chat" class="joms-icon joms-icon--emoticon">
                        <div style="position:relative">
                            <svg onclick="joms.view.comment.showEmoticonBoard(this);" class="svg-inline--fa fa-smile fa-w-16" aria-hidden="true" focusable="false" data-prefix="far" data-icon="smile" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512" data-fa-i2svg=""><path fill="currentColor" d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 448c-110.3 0-200-89.7-200-200S137.7 56 248 56s200 89.7 200 200-89.7 200-200 200zm-80-216c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32-32 14.3-32 32 14.3 32 32 32zm4 72.6c-20.8 25-51.5 39.4-84 39.4s-63.2-14.3-84-39.4c-8.5-10.2-23.7-11.5-33.8-3.1-10.2 8.5-11.5 23.6-3.1 33.8 30 36 74.1 56.6 120.9 56.6s90.9-20.6 120.9-56.6c8.5-10.2 7.1-25.3-3.1-33.8-10.1-8.4-25.3-7.1-33.8 3.1z"></path></svg>


                        </div>
                    </div>


                    <div class="attachents-lyout" style="display: none">
                        <div id="craft-attachents" style="display: none">
                            <div class="dropfile" onclick="joms.view.comment.addAttachment(this, 'image');">
                                <svg viewBox="0 0 16 16" class="nnn3 joms-icon joms-icon--add">
                                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-camera"></use>
                                </svg>
                                Прикрепить фото
                            </div>
                            <?php if ($config->get('message_file_sharing')): ?>
                                <div class="dropfile"
                                     onclick="joms.view.comment.addAttachment(this, 'file', { type: 'chat', id: joms.chat.active.chat_id, max_file_size: '<?php echo $config->get('message_file_maxsize', 0) ?>', exts: 'mp4,bmp,csv,doc,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS' });">
                                    <svg viewBox="0 0 16 16" class="nnn3 joms-icon joms-icon--add"
                                         style="<?php echo (JFactory::getLanguage()->isRTL()) ? 'left' : 'right'; ?>:43px">
                                        <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-file-zip"></use>
                                    </svg>
                                    Отправить файл
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>





                    <!--                    <div class="joms-textarea__wrapper">-->
                    <!--                        <div class="joms-textarea joms-textarea__beautifier"></div>-->
                    <!--                        <textarea id="inchat" rows="1" class="joms-textarea" name="comment" data-id="331" placeholder="Сообщение..."></textarea>-->
                    <!--                        <div class="joms-textarea__loading">-->
                    <!--                            <img src="--><?php //echo JURI::root(true); ?><!--/components/com_community/assets/ajax-loader.gif" alt="loader">-->
                    <!--                        </div>-->
                    <!--                        <div class="joms-textarea joms-textarea__attachment">-->
                    <!---->
                    <!--                            <button class="removeAttachment" onclick="joms.view.comment.removeAttachment(this);">×</button>-->
                    <!--                            <input type="hidden" class="joms-textarea__hidden" value="">-->
                    <!---->
                    <!---->
                    <!---->
                    <!--                            <div class="joms-textarea__attachment--loading">-->
                    <!--                                <img src="-->
                    <!--					--><?php //echo JURI::root(true); ?><!--/components/com_community/assets/ajax-loader.gif"-->
                    <!--                                     alt="loader">-->
                    <!--                            </div>-->
                    <!--                            <div class="joms-textarea__attachment--thumbnail">-->
                    <!--                                <img alt="attachment">-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->




                    <div class="joms-textarea__wrapper joms-js-wrapper">
                        <div class="joms-textarea joms-textarea__beautifier"></div>
                        <textarea id="inchat" rows="1" class="joms-textarea chat-textarea" disabled="disabled" placeholder="Сообщение"></textarea>

                        <div class="joms-textarea__loading">
                            <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                        </div>
                        <div class="joms-textarea joms-textarea__attachment">
                            <button class="removeAttachment" onclick="joms.view.comment.removeAttachment(this);">×</button>
                            <input type="hidden" class="joms-textarea__hidden" value="">
                            <div class="joms-textarea__attachment--loading">
                                <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                            </div>
                            <div class="joms-textarea__attachment--thumbnail">
                                <img alt="attachment">
                            </div>
                        </div>
                    </div>


                    <svg class="svg-inline--fa fa-paperclip dropbtn" width="24" height="27" viewBox="0 0 24 27"
                         fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.1066 14.56L12.8666 22.8133C11.7863 23.7733 10.3801 24.2843 8.93552 24.2418C7.49095 24.1993 6.11722 23.6064 5.09532 22.5845C4.07341 21.5626 3.48057 20.1889 3.43804 18.7443C3.39552 17.2998 3.90653 15.8935 4.86655 14.8133L15.5332 4.14662C16.1701 3.54169 17.0149 3.2044 17.8932 3.2044C18.7716 3.2044 19.6164 3.54169 20.2532 4.14662C20.8737 4.77541 21.2216 5.62325 21.2216 6.50662C21.2216 7.39 20.8737 8.23784 20.2532 8.86662L11.0532 18.0533C10.9622 18.1513 10.8527 18.2305 10.7311 18.2863C10.6094 18.342 10.478 18.3732 10.3443 18.3782C10.2106 18.3831 10.0772 18.3617 9.95173 18.3151C9.8263 18.2685 9.71127 18.1977 9.61322 18.1066C9.51517 18.0156 9.43601 17.9061 9.38026 17.7845C9.32451 17.6628 9.29327 17.5314 9.28832 17.3977C9.28337 17.264 9.3048 17.1306 9.3514 17.0051C9.39799 16.8797 9.46884 16.7647 9.55989 16.6666L16.3999 9.83996C16.651 9.58889 16.792 9.24836 16.792 8.89329C16.792 8.53822 16.651 8.1977 16.3999 7.94662C16.1488 7.69555 15.8083 7.5545 15.4532 7.5545C15.0982 7.5545 14.7576 7.69555 14.5066 7.94662L7.66655 14.8C7.32429 15.1396 7.05264 15.5436 6.86726 15.9887C6.68188 16.4337 6.58644 16.9111 6.58644 17.3933C6.58644 17.8754 6.68188 18.3528 6.86726 18.7979C7.05264 19.243 7.32429 19.647 7.66655 19.9866C8.36572 20.6526 9.2943 21.0241 10.2599 21.0241C11.2255 21.0241 12.1541 20.6526 12.8532 19.9866L22.0399 10.7866C23.0997 9.64923 23.6767 8.14485 23.6493 6.59044C23.6219 5.03604 22.9922 3.55295 21.8929 2.45365C20.7936 1.35435 19.3105 0.72465 17.7561 0.697224C16.2017 0.669798 14.6973 1.24678 13.5599 2.30662L2.89322 12.9733C1.45482 14.5664 0.686592 16.6531 0.748599 18.7986C0.810606 20.9441 1.69806 22.9829 3.22607 24.4903C4.75408 25.9977 6.80479 26.8574 8.95093 26.8902C11.0971 26.9231 13.1731 26.1265 14.7466 24.6666L22.9999 16.4266C23.1242 16.3023 23.2228 16.1547 23.2901 15.9923C23.3574 15.8299 23.392 15.6558 23.392 15.48C23.392 15.3041 23.3574 15.1301 23.2901 14.9676C23.2228 14.8052 23.1242 14.6576 22.9999 14.5333C22.8756 14.409 22.728 14.3104 22.5656 14.2431C22.4031 14.1758 22.229 14.1412 22.0532 14.1412C21.8774 14.1412 21.7033 14.1758 21.5409 14.2431C21.3785 14.3104 21.2309 14.409 21.1066 14.5333V14.56Z"
                              fill-opacity="0.5"/>
                    </svg>


                    <svg class="joms-js--send"
                         width="28" height="28" viewBox="0 0 28 28" fill="curentcolor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M25.1198 10.4267L6.45318 1.09334C5.7165 0.726687 4.88466 0.596156 4.07109 0.719544C3.25751 0.842932 2.50179 1.21424 1.90695 1.78283C1.31211 2.35142 0.907112 3.08964 0.747165 3.89682C0.587219 4.70401 0.68011 5.54088 1.01317 6.29334L4.21317 13.4533C4.28578 13.6265 4.32318 13.8123 4.32318 14C4.32318 14.1877 4.28578 14.3736 4.21317 14.5467L1.01317 21.7067C0.742108 22.3156 0.627516 22.9827 0.679811 23.6472C0.732106 24.3117 0.949631 24.9526 1.31262 25.5116C1.6756 26.0707 2.17254 26.5301 2.75827 26.8483C3.344 27.1664 3.99995 27.3332 4.66651 27.3333C5.29082 27.3271 5.90583 27.1813 6.46651 26.9067L25.1332 17.5733C25.7953 17.2403 26.3519 16.7297 26.7408 16.0988C27.1297 15.4678 27.3356 14.7412 27.3356 14C27.3356 13.2588 27.1297 12.5322 26.7408 11.9012C26.3519 11.2703 25.7953 10.7598 25.1332 10.4267H25.1198ZM23.9332 15.1867L5.26651 24.52C5.02139 24.6377 4.74615 24.6776 4.47769 24.6345C4.20923 24.5913 3.96039 24.4671 3.76452 24.2785C3.56866 24.0899 3.43515 23.8459 3.38188 23.5792C3.32862 23.3126 3.35814 23.0361 3.46651 22.7867L6.65317 15.6267C6.69443 15.5311 6.73004 15.4331 6.75984 15.3333H15.9465C16.3001 15.3333 16.6393 15.1929 16.8893 14.9428C17.1394 14.6928 17.2798 14.3536 17.2798 14C17.2798 13.6464 17.1394 13.3072 16.8893 13.0572C16.6393 12.8072 16.3001 12.6667 15.9465 12.6667H6.75984C6.73004 12.5669 6.69443 12.469 6.65317 12.3733L3.46651 5.21334C3.35814 4.96396 3.32862 4.68741 3.38188 4.42077C3.43515 4.15413 3.56866 3.91015 3.76452 3.72154C3.96039 3.53293 4.20923 3.40871 4.47769 3.36554C4.74615 3.32237 5.02139 3.36231 5.26651 3.48001L23.9332 12.8133C24.1516 12.9252 24.3349 13.0952 24.4629 13.3046C24.5908 13.514 24.6586 13.7546 24.6586 14C24.6586 14.2454 24.5908 14.486 24.4629 14.6954C24.3349 14.9048 24.1516 15.0748 23.9332 15.1867Z"
                              fill="curentcolor" fill-opacity="0.5"/>
                    </svg>


                    <div class="joms-textarea__wrapper joms-js-disabler"
                         style="position:absolute; top:0; left:0; right:0; bottom:0; opacity:.5; display:none"></div>
                </div>


            </div>
            <!-- //conversation -->
        </div>
    </div>
<?php else: ?>
    <div class="joms-page joms-js-page-chat-loading">
        <div style="text-align: center;">
            <h1><?php echo JText::_('COM_COMMUNITY_CHAT_IS_DISABLED') ?></h1>
            <p><?php echo JText::sprintf('COM_COMMUNITY_CHAT_DISABLED_BY_ADMIN', JFactory::getConfig()->get('sitename')); ?></p>
            <p>&nbsp;</p>
            <svg viewBox="0 0 16 16" class="joms-icon joms-icon-support" style="width:64px; height:64px;">
                <use xlink:href="#joms-icon-support"></use>
            </svg>
            <p>&nbsp;</p>
        </div>
    </div>
<?php endif ?>


<!--  <svg viewBox="0 0 16 16" class="joms-icon joms-icon--add" onclick="joms.view.comment.addAttachment(this, 'image');">
                    <use xlink:href="<?php //echo CRoute::getURI(); ?>#joms-icon-camera"></use>
                </svg>
                <?php //if ($config->get('message_file_sharing')): ?>
                <svg class="loadfile" viewBox="0 0 16 16" class="joms-icon joms-icon--add" onclick="joms.view.comment.addAttachment(this, 'file', { type: 'chat', id: joms.chat.active.chat_id, max_file_size: '<?php //echo $config->get('message_file_maxsize', 0) ?>', exts: 'bmp,csv,doc,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS' });" style="<?php //echo (JFactory::getLanguage()->isRTL()) ? 'left' : 'right'; ?>:43px">
                    <use xlink:href="<?php //echo CRoute::getURI(); ?>#joms-icon-file-zip"></use>
                </svg>
                <?php // endif; ?>-->
<!-- template: chat message grouped by date -->
<script type="text/template" id="joms-tpl-chat-message-dgroup">
    <div class="joms-js-chat-message-dgroup" data-id="{{= data.id }}">
        <div class="joms-chat__message-item" style="text-align:center">
            <small class="date-chat">{{= data.date }}</small>
        </div>
        <div class="joms-js-content"></div>

    </div>
</script>

<div class="joms-tooltip joms-js-chat-tooltip"></div>

<!-- Conversation message template -->
<script type="text/template" id="joms-js-template-chat-message">
    <div class="joms-chat__message-item {{= data.timestamp }} {{= data.name }}"
         data-user-id="{{= data.user_id }}"
         data-timestamp="{{= data.timestamp }}">
        <div class="joms-avatar {{= data.online ? 'joms-online' : '' }}">
            <a class="thisuser" target="_blank" href="{{= data.profile_link }}">
                <!--                <img ОТКЛ ПОКАЗА АВАТАРА В ЧАТЕ ВОЗЛЕ СООБЩЕНИЯ, для показа раскомментировать
										src="{{= data.user_avatar }}" joms-chat__item
										title="{{= data.user_name }}"
										alt="{{= data.user_name }} avatar"
										data-author="{{= data.user_id }}"/>-->
                {{= data.user_name }}
            </a>
        </div>
        <div class="joms-chat__message-body joms-js-chat-message-item-body">
        </div>
    </div>
</script>

<!--<script type="text/template" id="joms-js-template-chat-seen-by">-->
<!--    <div class="joms-chat__seen clearfix"-->
<!--         title="--><?php //echo JText::sprintf('COM_COMMUNITY_CHAT_SEEN_BY', '{{= data.names }}'); ?><!--">-->
<!--        {{ for ( var i in data.seen ) { }}-->
<!--        <img src="{{= data.seen[ i ].avatar }}"/>-->
<!--        {{ } }}-->
<!--    </div>-->
<!--</script>-->


<!-- Conversation message's content template -->
<script type="text/template" id="joms-js-template-chat-message-content">



    <div class="cr-msg-{{= data.name }}" data-timestamp="{{= data.timestamp }}" data-tooltip="{{= data.time }}">

        <span data-testid="tail-out" class="_3nrYb">
            <svg viewBox="0 0 8 13" width="8" height="13" class=""><path opacity=".13" d="M5.188 1H0v11.193l6.467-8.625C7.526 2.156 6.958 1 5.188 1z"></path><path fill="currentColor" d="M5.188 0H0v11.193l6.467-8.625C7.526 1.156 6.958 0 5.188 0z"></path>
            </svg>
        </span>

        <div class="joms-js-chat-loading" style="position:absolute; top:4px; right:6px; display:none">
            <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
        </div>
        <span class="joms-chat__message-content joms-js-chat-content {{= data.timestamp }}"
              {{=data.id }} data-id="{{= data.id }}">{{= data.message }}</span>

        <!-- ADD DATE TIME ON CHAT-->
        <span class="chat-time {{= data.name }}">
            <?php echo "{{= data.time}}" ?>
{{ if ( data.name == 'you') { }}
 {{ if ( data.is_seen == 1 || data.is_seen == 0) { }} <!-- craft if recived but not not yet seen -->
           <span  data-seen="{{= data.is_seen }}" aria-label="unread" data-icon=""
                  class="check-done-msg unread">
           <svg viewBox="0 0 16 15" width="16" height="15" class=""><path fill="currentColor" d="m15.01 3.316-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>
            </svg>
           </span>
            {{ } }}
 {{ if ( data.is_seen == 2) { }} <!-- craft if is seen -->
           <span  data-seen="{{= data.is_seen }}" aria-label="unread" data-icon="" class="check-done-msg">
           <svg viewBox="0 0 16 15" width="16" height="15" class=""><path fill="currentColor" d="m15.01 3.316-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>
            </svg>
           </span>
            {{ } else if (data.is_seen == null || !data.is_seen) { }} <!-- craft if not recive and no in database -->
           <span  data-seen="{{= data.is_seen }}" aria-label="unread" data-icon=""
                  class="check-done-msg unread unrevive">
          <svg viewBox="0 0 16 15" width="16" height="15" class=""><path fill="currentColor" d="m10.91 3.316-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>
          </svg>
           </span>
    {{ } }}
 {{ } }}


            <? // {{ console.log(data.is_seen) }} ?>


        </span>
        {{= data.attachment }}
        {{ if ( data.mine ) { }}

        <div class="joms-chat__message-actions">

            <a href="javascript:">
                <svg viewBox="0 0 16 16" class="joms-icon" style="width:12px; height:12px">
                    <use xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-close"></use>
                </svg>
            </a>
        </div>
        {{ } }}
    </div>
</script>




<!-- Conversation message's image attachment template -->
<script type="text/template" id="joms-js-template-chat-message-image">
    <div class="joms-chat__attachment-image joms-js-chat-attachment">
        <a href="javascript:" onclick="joms.api.photoZoom('{{= data.url }}');">
            <img src="{{= data.url }}" alt="photo thumbnail">
        </a>
    </div>
</script>

<!-- Conversation message's file attachment template -->
<script type="text/template" id="joms-js-template-chat-message-file">
    <div class="joms-chat__attachment-file joms-js-chat-attachment">
        <svg viewBox="0 0 16 16" class="joms-icon">
            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                 xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-file-zip"></use>
        </svg>
        <a href="{{= data.url }}" target="_blank" title="{{= data.name }}"><strong>{{= data.name }}</strong></a>
    </div>
</script>

<!-- Conversation message's video attachment template -->
<script type="text/template" id="joms-js-template-chat-message-video">
    <div class="joms-chat__attachment-video joms-js-chat-attachment" style="background:white">
        <?php if ($config->get('enable_embedly')) { ?>

            <a href="{{= data.url }}" class="embedly-card"
               data-card-controls="0" data-card-recommend="0"
               data-card-theme="<?php echo $config->get('enable_embedly_card_template'); ?>"
               data-card-key="<?php echo $config->get('embedly_card_apikey'); ?>"
               data-card-align="<?php echo $config->get('enable_embedly_card_position') ?>"
            ><?php echo JText::_('COM_COMMUNITY_EMBEDLY_LOADING'); ?></a>

        <?php } else { ?>

            <div class="joms-media--video joms-js--video"
                 data-type="{{= data.type }}"
                 data-id="{{= data.id }}"
                 data-path="<{{= data.path }}"
                 style="margin-top:10px">

                <div class="joms-media__thumbnail">
                    <img src="{{= data.thumbnail }}" alt="{{= data.title }}">
                    <a href="javascript:" class="mejs-overlay mejs-layer mejs-overlay-play joms-js--video-play">
                        <div class="mejs-overlay-button"></div>
                    </a>
                </div>
                <div class="joms-media__body">
                    <h4 class="joms-media__title">{{= data.title_short }}</h4>
                    <p class="joms-media__desc">{{= data.desc_short }}</p>
                </div>
            </div>

        <?php } ?>
    </div>
</script>

<!-- Conversation message's url attachment template -->
<script type="text/template" id="joms-js-template-chat-message-url">
    <div class="joms-chat__attachment-url joms-js-chat-attachment" style="background:white">
        <?php if ($config->get('enable_embedly')) { ?>

            <a href="{{= data.url }}" class="embedly-card"
               data-card-controls="0" data-card-recommend="0"
               data-card-theme="<?php echo $config->get('enable_embedly_card_template'); ?>"
               data-card-key="<?php echo $config->get('embedly_card_apikey'); ?>"
               data-card-align="<?php echo $config->get('enable_embedly_card_position') ?>"
            ><?php echo JText::_('COM_COMMUNITY_EMBEDLY_LOADING'); ?></a>

        <?php } else { ?>

            <div style="position:relative">
                <div class="row-fluid">
                    {{ if ( data.images && data.images.length ) { }}
                    <div class="span4">
                        <a href="{{= data.url }}" onclick="joms.api.photoZoom('{{= data.images[0] }}');">
                            <img class="joms-stream-thumb" src="{{= data.images[0] }}" alt="photo thumbnail"/>
                        </a>
                    </div>
                    {{ } }}
                    <div class="span{{= data.images && data.images.length ? 8 : 12 }}">
                        <article class="joms-stream-fetch-content" style="margin-left:0; padding-top:0">
                            <a href="{{= data.url }}"><span class="joms-stream-fetch-title">{{= data.title }}</span></a>
                            <span class="joms-stream-fetch-desc">{{= data.description }}</span>
                        </article>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>
</script>

<!-- Conversation message time template  -->
<script type="text/template" id="joms-js-template-chat-message-time">
    <div class="joms-chat__message-item" style="text-align:center">
        <small><strong>{{= data.time }}</strong></small>
    </div>
</script>

<!-- End of conversation notice template  -->
<script type="text/template" id="joms-js-template-chat-message-end">
    <div class="joms-chat__message-item joms-js--chat-conversation-end" style="text-align:center">
        <small><?php echo JText::_('COM_COMMUNITY_CHAT_MSG_NO_MORE'); ?></small>
    </div>
</script>

<!-- Leave conversation template  -->
<script type="text/template" id="joms-js-template-chat-leave">
    <div class="joms-chat__message-item joms-js-chat-content" data-id="{{= data.id }}" style="text-align:center">
        <div class="joms-avatar"></div>
        <div class="joms-chat__item-body">
            <div data-tooltip="{{= data.time }}">
                <small>
                    {{ if ( data.mine ) { }}
                    <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_YOU_LEAVE'); ?></span>
                    {{ } else { }}
                    <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_LEAVE', '{{= data.name }}'); ?></span>
                    {{ } }}
                </small>
            </div>
        </div>
    </div>
</script>

<!-- Added to conversation template -->
<script type="text/template" id="joms-js-template-chat-added">
    <div class="joms-chat__message-item joms-js-chat-content" data-id="{{= data.id }}" style="text-align:center">
        <div class="joms-avatar"></div>
        <div class="joms-chat__item-body">
            <div data-tooltip="{{= data.time }}">
                <small>
                    {{ if ( data.mine ) { }}
                    <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_YOU_ADDED', '{{= data.by }}'); ?></span>
                    {{ } else { }}
                    <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_MSG_ADDED', '{{= data.name }}', '{{= data.by }}'); ?></span>
                    {{ } }}
                </small>
            </div>
        </div>
    </div>
</script>

<!-- Change chat name template -->
<script type="text/template" id="joms-js-template-chat-name-changed">
    <div class="joms-chat__message-item joms-js-chat-content" data-id="{{= data.id }}" style="text-align:center">
        <div class="joms-avatar"></div>
        <div class="joms-chat__item-body">
            <div data-tooltip="{{= data.time }}">
                <small>
                    {{ if ( data.mine ) { }}
                    <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_YOU_CHANGE_GROUP_NAME', '{{= data.groupname }}'); ?></span>
                    {{ } else { }}
                    <span><?php echo JText::sprintf('COM_COMMUNITY_CHAT_GROUP_NAME_HAS_CHANGED', '{{= data.name }}', '{{= data.groupname }}'); ?></span>
                    {{ } }}
                </small>
            </div>
        </div>
    </div>
</script>

<!-- Seen by template -->
<script type="text/template" id="joms-js-template-chat-seen-by">
    <div class="joms-chat__seen clearfix"
         title="<?php echo JText::sprintf('COM_COMMUNITY_CHAT_SEEN_BY', '{{= data.names }}'); ?>">
        <!--        <img src="{{= data.seen[ 0 ].avatar }}"/>-->
        {{ if ( !data ) { }}

        <span data-testid="msg-dblcheck" aria-label=" Доставлено " data-icon="" class="check-done-msg unread">
           <svg viewBox="0 0 16 15" width="16" height="15" class=""><path fill="currentColor" d="m15.01 3.316-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>
            </svg></span>

        {{ } }}


        {{ if ( data ) { }}

        <span data-testid="msg-dblcheck" aria-label=" Доставлено " data-icon="" class="check-done-msg">
           <svg viewBox="0 0 16 15" width="16" height="15" class=""><path fill="currentColor" d="m15.01 3.316-.478-.372a.365.365 0 0 0-.51.063L8.666 9.879a.32.32 0 0 1-.484.033l-.358-.325a.319.319 0 0 0-.484.032l-.378.483a.418.418 0 0 0 .036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0-.478-.372a.365.365 0 0 0-.51.063L4.566 9.879a.32.32 0 0 1-.484.033L1.891 7.769a.366.366 0 0 0-.515.006l-.423.433a.364.364 0 0 0 .006.514l3.258 3.185c.143.14.361.125.484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"></path>
            </svg></span>

        {{ } }}
        {{ console.log(data) }}

        <!--        <img src="{{ //= data.seen[ i ].avatar }}"/>  TODO Craft Для групп чатов перенести в условие ниже аватары-->
        <? /***  {{  for ( var i in data.seen ) { }}
        {{ } }} ***/ ?>

    </div>
</script>

<!-- Sidebar item template --
<script type="text/template" id="joms-js-template-chat-sidebar-item">
    <div class="joms-chat__item joms-js--chat-item-{{= data.id }} {{= +data.unread ? 'unread' : '' }} {{= +data.active ? 'active' : '' }}"
            data-chat-type="{{= data.type }}" data-chat-id="{{= data.id }}">
        <div class="joms-avatar {{= data.online ? 'joms-online' : '' }}">
            <img src="{{= data.avatar }}" />
        </div>
        <div class="joms-chat__item-body">
            <b href="#">{{= data.name }}</b>
            <span class="joms-js--chat-item-msg"></span>
        </div>
        {{ if (data.mute) { }}
        <div class="joms-chat__item-actions">
            <svg viewBox="0 0 16 16" class="joms-icon">
                <use xlink:href="#joms-icon-close"></use>
            </svg>
        </div>
        {{ } }}
    </div>
</script>-->

<!-- Sidebar search no contacts found -->
<script type="text/template" id="joms-js-template-chat-no-contact-found">
    <div class="joms-chat__search--no-result">
        <span><?php echo JText::_('COM_COMMUNITY_CHAT_SEARCH_NO_CONTACT') ?></span>
    </div>
</script>

<!-- Sidebar search no groups found -->
<script type="text/template" id="joms-js-template-chat-no-group-found">
    <div class="joms-chat__search--no-result">
        <span><?php echo JText::_('COM_COMMUNITY_CHAT_SEARCH_NO_GROUP') ?></span>
    </div>
</script>

<!-- Sidebar search result item template -->
<script type="text/template" id="joms-js-template-chat-sidebar-search-result-item">
    <div class="joms-chat__item joms-js--chat-item-{{= data.id }} {{= data.online ? 'joms-online' : '' }} {{= +data.unread ? 'unread' : '' }} result-item"
         data-chat-type="{{= data.type }}" data-chat-id="{{= data.id }}">
        <div class="joms-avatar">
            <img src="{{= data.avatar }}"/>
        </div>
        <div class="joms-chat__item-body">
            <b class="lable-name" href="#">{{= data.name }}</b>
            <span class="joms-js--chat-item-msg"></span>
        </div>
    </div>
</script>

<!-- Sidebar draft item template -->
<script type="text/template" id="joms-js-template-chat-sidebar-draft">
    <div class="joms-chat__item joms-js--chat-item-0" data-chat-type="new" data-chat-id="0">
        <span class="joms-js--remove-draft" style="position:absolute;right: 5px; top: 0px;">+</span>
        <div class="joms-avatar">
            <svg class="joms-avatar-svg" width="40" height="40" viewBox="0 0 32 32" fill="none"
                 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <circle cx="16" cy="16" r="20" fill="url(#pattern0)"/>
                <defs>
                    <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <use xlink:href="#image0_1014_2358" transform="translate(-0.115385) scale(0.00153846)"/>
                    </pattern>
                    <image id="image0_1014_2358" width="800" height="650"
                           xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAyAAAAKKCAIAAABK4qgDAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDIxIDc5LjE1NTc3MiwgMjAxNC8wMS8xMy0xOTo0NDowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzAxNTE5RjhEQjg0MTFFNDhFOTJDMTUwNjhFQUE1RkEiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzAxNTE5RjdEQjg0MTFFNDhFOTJDMTUwNjhFQUE1RkEiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RTk2MjcwRkNCQzUzMTFFNEE2N0FERjQ2NDg5MDEyNDMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RTk2MjcwRkRCQzUzMTFFNEE2N0FERjQ2NDg5MDEyNDMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz46CkcGAAAmjklEQVR42uzd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"/>
                </defs>
            </svg>

        </div>
        <div class="joms-chat__item-body {{= data.online ? 'joms-online' : '' }}">
            <b class="lable-name" href="#"><?php echo JText::_('COM_COMMUNITY_CHAT_NEW_CHAT'); ?></b
            <span class="joms-js--chat-item-msg"></span>
        </div>
    </div>
</script>

<!--
**** WINDOW CHAT
**** This is a popup chat which can be used on multiple pages, fixed to bottom.
****
-->
<div class="joms-chat__wrapper joms-chat--window">
    <div class="joms-chat__windows clearfix">
        <!-- Message window wrapper -->
        <div class="joms-chat__window" style="display:none">
            <div class="joms-chat__window-title">
                <span class="joms-chat__status"></span>
                Username
                <a href="#" class="joms-chat__window-close">
                    <svg viewBox="0 0 16 16" class="joms-icon">
                        <use xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-close"></use>
                    </svg>
                </a>
            </div>

            <div class="joms-chat__window-body">
                <!-- Message wrapper -->
                <div class="joms-chat__message">
                    <div class="joms-chat__message-avatar">
                        <img src="" alt="">
                    </div>

                    <div class="joms-chat__message-bubble">
                        <p>Message</p>
                    </div>

                    <div class="joms-chat__message-media">
                        <img src="" alt="">
                    </div>
                </div>
                <!-- //message -->
            </div>

            <div class="joms-chat__input-wrapper">
                <input type="text">

                <div class="joms-chat__input-actions">
                    <a href="#">
                        <svg viewBox="0 0 16 16" class="joms-icon">
                            <use xlink:href="<?php echo JUri::getInstance(); ?>#joms-icon-camera"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <!-- //window -->
    </div>

    <div class="joms-chat__sidebar"></div>
</div>
<!-- //popup chat -->

<style>
    .img-item {
        position: relative;
        user-select: none;
    }
    .img-item img {
        border-radius: 50%;
        object-fit: cover;
        width: 100px;
        height: 100px;
    }
    .img-item a {
        display: inline-block;
        background: url(/images/remove.png) 0 0 no-repeat;
        position: absolute;
        top: -5px;
        right: -9px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    #js-file-list {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }
    .js-popup-button {
        color: #253544;
        font-size: 16px;
        margin: 25px auto;
        display: grid;
        text-align: center;
        padding: 10px;
        font-weight: 600;
    }
    #saveava {
        background: none;
        width: 100%;
    }
    .form-row {
        margin-bottom: 14px;
    }
</style>


<script>

    jQuery(document).ready(function ($) {

        $('#g-navigation, #g-footer').remove();

        // show chat menu
        var newchat = $('.joms-js--chat-new-message');
        $('#chat-m-btn, .chatmenu-lyout').click(function () {
            $('#chatmenu, .chatmenu-lyout').toggle();
            if (newchat.is(':hidden')) newchat.show();
            if($('.joms-chat__item.active').data('chat-type') == 'group') {
                $('.joms-js--all-peoples, .joms-js--change-ava').toggle();
            }
        });
        $('.dropbtn, .attachents-lyout').click(function () {
            $('.attachents-lyout, #craft-attachents').toggle();

        });
        $('.search').click(function () {
            $('input.joms-input, span.title-chat, .searchclose').toggle();
            $('.search').addClass('search-close');
        });
        // resize textarea
        $("textarea").each(function () {
            this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:hidden;");
        }).on("input", function () {
            this.style.height = "auto";
            this.style.height = (this.scrollHeight) + "px";
        });
        $('svg.joms-js--send').click(function resizetextarea() {
            $('textarea#inchat').css('height', 'auto');
        });

        setTimeout(function () {
            $('.joms-chat__item').click(function () {
                    if($(this).hasClass('active') && $(this).data('chat-type') == 'group') {
                        $('#chatmenu, .chatmenu-lyout, .joms-js--all-peoples, .joms-js--change-ava').fadeToggle();
                        newchat.hide();
                    }
                });
             }, 3000);

        // change chat background
        var b_inut = $('#js--bkg-input');
        b_inut.change(function(){
            if (window.FormData === undefined) {
                alert('В вашем браузере загрузка файлов не поддерживается, а ведь уже 21-й первый век ;)');
            } else {
                var formData = new FormData();
                $.each(b_inut[0].files, function(key, input){
                    formData.append('file[]', input);
                });
                $.ajax({
                    type: 'POST',
                    url: '/index.php?option=com_community&view=chat&task=ajaxUpload&action=chat_backgroung',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    dataType : 'json',
                    success: function(r){
                            if (!r.error) {
                                $('.js--bkg').css('background', 'url(/images/chatcovers/'+ r.file +')');
                            } else {
                                alert(r.error);
                            }
                    }
                });

            }
        });
    });

</script>

