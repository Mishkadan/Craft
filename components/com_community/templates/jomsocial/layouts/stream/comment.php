<?php
/**
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */
defined('_JEXEC') or die();

if (!$config->get('enablewallcomment')) {
    return '';
}

$mainframe = JFactory::getApplication();
$jinput = $mainframe->input;
 
$commentLimit = ($jinput->get('actid',0) > 0) ? 100000000 : $config->get('stream_default_comments', 0);
?>

<?php if ($allowComment || $allowLike || $showLike) { ?>

    <?php if ($allowComment) : ?>
        <div class="joms-comment__reply joms-js--newcomment joms-js--newcomment-<?php echo $act->id; ?>" data-id="<?php echo $act->id; ?>">
            <div class="joms-textarea__wrapper">
                <div class="joms-textarea joms-textarea__beautifier"></div>
                <textarea class="joms-textarea" name="comment" data-id="<?php echo $act->id; ?>"
                    <?php

                    // We need to do this because photo upload stream comments saved with reference to album->id, not stream->id.
                    if ( $act->app === 'photos' ) {
                        $photos = array();
                        if ( $act->params ) {
                            $photos = $act->params->get('photosId');
                            $photos = explode(',', $photos);
                        }
                        if ( count($photos) === 1 ) {
                            echo 'data-tag-func="photo" data-tag-id="' . $photos[0] . '"';
                        } else {
                            echo 'data-tag-func="album" data-tag-id="' . $act->cid . '"';
                        }
                    } else if ( $act->app === 'videos.linking' ) {
                        echo 'data-tag-func="video" data-tag-id="' . $act->cid . '"';
                    }

                    ?>
                    placeholder="<?php echo JText::_('COM_COMMUNITY_WRITE_A_COMMENT'); ?>"></textarea>
                <div class="joms-textarea__loading"><img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader" ></div>
                <div class="joms-textarea joms-textarea__attachment">
                    <button onclick="joms.view.comment.removeAttachment(this);">×</button>
                    <div class="joms-textarea__attachment--loading"><img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader" ></div>
                    <div class="joms-textarea__attachment--thumbnail"><img alt="attachment"></div>
                </div>
            </div>

            <div class="joms-icon joms-icon--emoticon" >
                <div style="position:relative">
                    <!--                    <svg viewBox="0 0 16 16" onclick="joms.view.comment.showEmoticonBoard(this);">-->
                    <!--                        <use xlink:href="--><?php //echo JUri::getInstance(); ?><!--#joms-icon-smiley"></use>-->

                    <i onclick="joms.view.comment.showEmoticonBoard(this);" class="far fa-smile"></i>

                    </svg>
                </div>
            </div>

            <!--            <svg viewBox="0 0 16 16" class="joms-icon joms-icon--add" onclick="joms.view.comment.addAttachment(this);">-->
            <!--                <use xlink:href="--><?php //echo JUri::getInstance(); ?><!--#skrepka"></use>-->
            <!--            </svg>-->

            <svg class="joms-icon joms-icon--add skrepka" joms-icon--addwidth="24" onclick="joms.view.comment.addAttachment(this);" height="27" viewBox="0 0 24 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.1066 14.56L12.8666 22.8133C11.7863 23.7733 10.3801 24.2843 8.93552 24.2418C7.49095 24.1993 6.11722 23.6064 5.09532 22.5845C4.07341 21.5626 3.48057 20.1889 3.43804 18.7443C3.39552 17.2998 3.90653 15.8935 4.86655 14.8133L15.5332 4.14662C16.1701 3.54169 17.0149 3.2044 17.8932 3.2044C18.7716 3.2044 19.6164 3.54169 20.2532 4.14662C20.8737 4.77541 21.2216 5.62325 21.2216 6.50662C21.2216 7.39 20.8737 8.23784 20.2532 8.86662L11.0532 18.0533C10.9622 18.1513 10.8527 18.2305 10.7311 18.2863C10.6094 18.342 10.478 18.3732 10.3443 18.3782C10.2105 18.3831 10.0772 18.3617 9.95173 18.3151C9.8263 18.2685 9.71127 18.1977 9.61322 18.1066C9.51517 18.0156 9.43601 17.9061 9.38026 17.7845C9.32451 17.6628 9.29327 17.5314 9.28832 17.3977C9.28337 17.264 9.3048 17.1306 9.3514 17.0051C9.39799 16.8797 9.46884 16.7647 9.55989 16.6666L16.3999 9.83996C16.651 9.58889 16.792 9.24836 16.792 8.89329C16.792 8.53822 16.651 8.1977 16.3999 7.94662C16.1488 7.69555 15.8083 7.5545 15.4532 7.5545C15.0982 7.5545 14.7576 7.69555 14.5066 7.94662L7.66655 14.8C7.32429 15.1396 7.05264 15.5436 6.86726 15.9887C6.68188 16.4337 6.58644 16.9111 6.58644 17.3933C6.58644 17.8754 6.68188 18.3528 6.86726 18.7979C7.05264 19.243 7.32429 19.647 7.66655 19.9866C8.36572 20.6526 9.2943 21.0241 10.2599 21.0241C11.2255 21.0241 12.1541 20.6526 12.8532 19.9866L22.0399 10.7866C23.0997 9.64922 23.6767 8.14485 23.6493 6.59044C23.6219 5.03604 22.9922 3.55295 21.8929 2.45365C20.7936 1.35435 19.3105 0.72465 17.7561 0.697224C16.2017 0.669798 14.6973 1.24678 13.5599 2.30662L2.89322 12.9733C1.45482 14.5664 0.686592 16.6531 0.748599 18.7986C0.810606 20.9441 1.69806 22.9829 3.22607 24.4903C4.75408 25.9977 6.80479 26.8574 8.95093 26.8902C11.0971 26.9231 13.1731 26.1265 14.7466 24.6666L22.9999 16.4266C23.1242 16.3023 23.2228 16.1547 23.2901 15.9923C23.3574 15.8299 23.392 15.6558 23.392 15.48C23.392 15.3041 23.3574 15.1301 23.2901 14.9676C23.2228 14.8052 23.1242 14.6576 22.9999 14.5333C22.8756 14.409 22.728 14.3104 22.5656 14.2431C22.4031 14.1758 22.229 14.1412 22.0532 14.1412C21.8774 14.1412 21.7033 14.1758 21.5409 14.2431C21.3785 14.3104 21.2309 14.409 21.1066 14.5333V14.56Z" fill="#253544" fill-opacity="0.5"/>
            </svg>


            <span>
                <button class="joms-button--comment joms-js--btn-send">
                      <svg class="paper-plane" width="28" height="28" viewBox="0 0 28 28" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
<path d="M25.1198 10.4267L6.45317 1.09334C5.7165 0.726687 4.88466 0.596156 4.07109 0.719544C3.25751 0.842932 2.50179 1.21424 1.90695 1.78283C1.31211 2.35142 0.907112 3.08964 0.747165 3.89682C0.587219 4.70401 0.68011 5.54088 1.01317 6.29334L4.21317 13.4533C4.28578 13.6265 4.32318 13.8123 4.32318 14C4.32318 14.1877 4.28578 14.3736 4.21317 14.5467L1.01317 21.7067C0.742108 22.3156 0.627516 22.9827 0.679811 23.6472C0.732106 24.3117 0.949631 24.9526 1.31262 25.5116C1.6756 26.0707 2.17254 26.5301 2.75827 26.8483C3.344 27.1664 3.99995 27.3332 4.66651 27.3333C5.29082 27.3271 5.90583 27.1813 6.46651 26.9067L25.1332 17.5733C25.7953 17.2403 26.3519 16.7297 26.7408 16.0988C27.1297 15.4678 27.3356 14.7412 27.3356 14C27.3356 13.2588 27.1297 12.5322 26.7408 11.9012C26.3519 11.2703 25.7953 10.7598 25.1332 10.4267H25.1198ZM23.9332 15.1867L5.26651 24.52C5.02139 24.6377 4.74615 24.6776 4.47769 24.6345C4.20923 24.5913 3.96039 24.4671 3.76452 24.2785C3.56866 24.0899 3.43515 23.8459 3.38188 23.5792C3.32862 23.3126 3.35814 23.0361 3.46651 22.7867L6.65317 15.6267C6.69443 15.5311 6.73004 15.4331 6.75984 15.3333H15.9465C16.3001 15.3333 16.6393 15.1929 16.8893 14.9428C17.1394 14.6928 17.2798 14.3536 17.2798 14C17.2798 13.6464 17.1394 13.3072 16.8893 13.0572C16.6393 12.8072 16.3001 12.6667 15.9465 12.6667H6.75984C6.73004 12.5669 6.69443 12.469 6.65317 12.3733L3.46651 5.21334C3.35814 4.96396 3.32862 4.68741 3.38188 4.42077C3.43515 4.15413 3.56866 3.91015 3.76452 3.72154C3.96039 3.53293 4.20923 3.40871 4.47769 3.36554C4.74615 3.32237 5.02139 3.36231 5.26651 3.48001L23.9332 12.8133C24.1516 12.9252 24.3349 13.0952 24.4629 13.3046C24.5908 13.514 24.6586 13.7546 24.6586 14C24.6586 14.2454 24.5908 14.486 24.4629 14.6954C24.3349 14.9048 24.1516 15.0748 23.9332 15.1867V15.1867Z" fill="" fill-opacity="0.5"/>
</svg>

                    <!--                       <i class="fas fa-paper-plane fa-2x"></i>-->
                        </button>
            </span>
        </div>
    <?php endif; ?>

    <div class="joms-comment joms-js--comments joms-js--comments-<?php echo $act->id; ?>" data-id="<?php echo $act->id; ?>">
        <?php

        $commentDiff = $act->commentCount - $commentLimit;
        if ($commentDiff > 0) { ?>
            <div class="joms-comment__more joms-js--more-comments">
                <a href="javascript:" data-lang="<?php echo JText::_("COM_COMMUNITY_SHOW_PREVIOUS_COMMENTS") . ' (%d)'  ?>"><?php
                    echo JText::_("COM_COMMUNITY_SHOW_PREVIOUS_COMMENTS") . ' (' . $commentDiff . ')'; ?></a>
            </div>
        <?php } ?>

        <?php if ($act->commentCount > 0) { ?>
            <?php

            #echo $act->commentLast;

            $comments = $act->commentsAll;
            #echo "<pre>";var_dump($comments);die();
            #$comments = $comments[$act->id];

           // $commentLimit = $config->get('stream_default_comments');
            $comments = array_reverse($comments);

            if($act->commentCount > $commentLimit) {
                $comments = array_slice($comments, sizeof($comments) - $commentLimit, $commentLimit);
            }
            CWall::triggerWallComments($comments, false);
            foreach($comments as $comment) {
                $comment->params		= new CParameter($comment->params);
                echo CWall::formatComment($comment);
            }

            ?>
        <?php } ?>
    </div>

<?php } ?>
