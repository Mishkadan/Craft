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
?>

<div class="joms-landing <?php if (isset($settings) && !$settings['general']['enable-frontpage-image']) echo "no-image"; ?>">
    <?php if (isset($settings) && $settings['general']['enable-frontpage-login']) { ?>

        <h2 id="hello_user">
            Привет!<br>
            Craft - это место, где Ты можешь встретиться с  производителями, рестораторами и ремесленниками своего дела.
            Регистрация не обязательна, но она понадобится для того, чтобы писать отзывы, комментарии и общаться в чатах.
            Ремесленники размещают себя в разделе Craft.
            Если У тебя есть своё ремесло - нажми «+» в разделе Craft и расскажи о Себе.
            По любым вопросам о работе сайта пиши в чат
            <a class ="sendadmin" href="javascript:joms.api.pmSend('532')"> <img class="admincraft" src="/images/L1.jpg">RAFT</a>
            или на почту <a href="mailto:craft.ru.net@ya.ru">craft.ru.net@ya.ru</a>
        </h2>
        <button class="login_u">Подробнее</button>

        <div class="joms-landing__action <?php if(CSystemHelper::tfaEnabled()) { echo 'tfaenabled'; } ?>">
            <form class="joms-form joms-js-form--login" action="<?php echo CRoute::getURI(); ?>" method="post" name="login" id="form-login">
                <div class="joms-input--append">
                    <svg viewBox="0 0 16 16" class="joms-icon">
                        <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-user"></use>
                    </svg>
                    <input type="text" name="username" class="joms-input" placeholder="<?php echo JText::_('COM_COMMUNITY_USERNAME'); ?>">
                </div>
                <div class="joms-input--append">
                    <svg viewBox="0 0 16 16" class="joms-icon">
                        <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-lock"></use>
                    </svg>
                    <input type="password" name="password" class="joms-input" placeholder="<?php echo JText::_('COM_COMMUNITY_PASSWORD'); ?>">
                </div>
                <?php if(CSystemHelper::tfaEnabled()){?>
                    <div class="joms-input--append">
                        <svg viewBox="0 0 16 16" class="joms-icon">
                            <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-key"></use>
                        </svg>
                        <input type="text" name="secretkey" class="joms-input" placeholder="<?php echo JText::_('COM_COMMUNITY_AUTHENTICATION_KEY'); ?>">
                    </div>
                <?php } ?>
	            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                    <div class="joms-checkbox">
                        <input type="checkbox" value="yes" name="remember" id="joms-remember-main">
                        <span><label for="joms-remember-main"><?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS'); ?></label></span>
                    </div>
	            <?php endif; ?>
                <button class=" login-u"><?php echo JText::_('COM_COMMUNITY_LOGIN') ?></button>

                <div class="forget">
                <a href="<?php echo CRoute::_('index.php?option=' . COM_USER_NAME . '&view=remind'); ?>"><?php echo JText::_('COM_COMMUNITY_FORGOT_USERNAME_LOGIN'); ?></a>
                <a href="<?php echo CRoute::_('index.php?option=' . COM_USER_NAME . '&view=reset'); ?>"
                   tabindex="6"><?php echo JText::_('COM_COMMUNITY_FORGOT_PASSWORD_LOGIN'); ?></a>
                </div>
                <?php if ($useractivation) { ?>
                    <a href="<?php echo CRoute::_('index.php?option=com_community&view=register&task=activation'); ?>"
                       class="login-forgot-username"><?php echo JText::_('COM_COMMUNITY_RESEND_ACTIVATION_CODE'); ?></a>
                <?php } ?>
                <input type="hidden" name="option" value="<?php echo COM_USER_NAME; ?>"/>
                <input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGIN; ?>"/>
                <input type="hidden" name="return" value="<?php echo $return; ?>"/>
                <div class="joms-js--token"><?php echo JHTML::_('form.token'); ?></div>
            </form>

            <script>
                joms.onStart(function( $ ) {
                    $('.joms-js-form--login').on( 'submit', function( e ) {
                        e.preventDefault();
                        e.stopPropagation();
                        joms.ajax({
                            func: 'system,ajaxGetLoginFormToken',
                            data: [],
                            callback: function( json ) {
                                var form = $('.joms-js-form--login');
                                if ( json.token ) {
                                    form.find('.joms-js--token input').prop('name', json.token);
                                }
                                form.off('submit').submit();
                            }
                        });
                    }).find('[name=username],[name=password],[name=secretkey]').attr('autocapitalize', 'off');
                });
            </script>

            <?php echo isset($fbHtml) ? $fbHtml : ''; ?>
            <?php echo isset($googleHtml) ? $googleHtml : ''; ?>
            <?php echo isset($twitterHtml) ? $twitterHtml : ''; ?>
            <?php echo isset($linkedinHtml) ? $linkedinHtml : ''; ?>
        </div>
    <?php } ?>





		<?php if (isset($settings) && ($settings['general']['enable-frontpage-paragraph'] || $settings['general']['enable-frontpage-login'])) { ?>
            <div class="joms-land__content">

				<?php  if ($allowUserRegister && (isset($settings) && $settings['general']['enable-frontpage-login'])) : ?>

                    <div class="joms-landing__signup">

                        <button class="joms-button--signup bblue">
                                <a href="/craft">&nbsp;<?php echo JText::_('COM_COMMUNITY_VISIT_CRAFT'); ?></a></button>
                    </div>


                            <button class="joms-button--signup bblue zzz"
                                    onclick="location.href='<?php echo CRoute::_('index.php?option=com_community&view=register', false); ?>'">
                                <?php echo JText::_('COM_COMMUNITY_JOIN_US_NOW'); ?> </button>
                    <p>или войти через</p>
                    <jdoc:include type="modules" name="Soclogin" />



				<?php  endif; ?>

				<?php  if (!$allowUserRegister && $inviteOnlyRegister && (isset($settings) && $settings['general']['enable-frontpage-login'])) : ?>
                    <div class="joms-landing__invite">
                        <button class="joms-button--invite"
                                onclick="location.href='<?php echo CRoute::_('index.php?option=com_community&view=registerinvite', false); ?>'">
							<?php echo JText::_('COM_COMMUNITY_REQUEST_INVITE'); ?></button>
                    </div>
				<?php  endif; ?>
            </div>
		<?php  } ?>

</div>

<script>
    // show read more about CRAFT
    jQuery(document).ready(function($) {
        $('.login_u').click(function() {
            $('#hello_user').toggleClass('active');
             if ($('#hello_user').hasClass('active')) {
                $('.login_u').html('Скрыть')
            } else {
                $('.login_u').html('Подробнее');
            }
            return false;
        });
    });
</script>
