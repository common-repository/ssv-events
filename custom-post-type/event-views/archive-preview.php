<?php
use mp_ssv_events\models\Event;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

#region setup variables
global $post;
$event               = Event::getByID($post->ID);
$event_registrations = $event->getRegistrations();
$content             = get_the_content('');
#endregion
?>
<article id="post-<?php the_ID(); ?>">
    <div class="card hoverable large">
        <div class="card-image waves-effect waves-block waves-light">
            <?php mp_ssv_post_thumbnail(true, array('class' => 'activator')); ?>
        </div>
        <div class="card-content">
            <header class="entry-header">
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>
                <h2 class="card-title activator">
                    <?= the_title() ?>
                    <?php if ($event->isRegistrationEnabled()) : ?>
                        <span class="new badge" data-badge-caption="Registrations"><?= esc_html(count($event_registrations)) ?></span>
                    <?php endif; ?>
                </h2>
            </header>
            <div class="row">
                <div class="col s12 m8">
                    <?= $content ?>
                </div>
                <div class="col s12 m4">
                    <div class="row" style="border-left: solid">
                        <div class="col s3">From:</div>
                        <div class="col s9"><?= esc_html($event->getStart()) ?></div>
                        <?php if ($event->getEnd()) : ?>
                            <div class="col s3">Till:</div>
                            <div class="col s9"><?= esc_html($event->getEnd()) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <footer class="card-action" style="background-color: #E6E6E6;">
            <a href="<?= esc_url(get_permalink()) ?>" class="btn waves-effect waves-light">Full Post</a>
        </footer>
        <div class="card-reveal" style="overflow: hidden;">
            <header class="entry-header">
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>
                <h2 class="card-title activator"><?= the_title() ?><i class="material-icons right">close</i></h2>
            </header>
            <?php if (count($event_registrations) > 0) : ?>
                <div class="row" style="max-height: <?= $event->canRegister() ? '435px' : '413px' ?>; overflow: auto">
                    <div class="col s12 m8">
                        <?= $content ?>
                    </div>
                    <div class="col s12 m4">
                        <?php $event->showRegistrations(true, false); ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="row" style="max-height: <?= $event->canRegister() ? '435px' : '515px' ?>; overflow: auto">
                    <?= $content ?>
                </div>
            <?php endif; ?>
            <?php if ($event->isRegistrationPossible()) : ?>
                <div class="card-action">
                    <?php if (is_user_logged_in()) : ?>
                        <form action="<?= esc_url(get_permalink()) ?>" method="POST" style="margin: 0">
                            <?php if ($event->isRegistered(User::getCurrent())) : ?>
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" name="submit" class="btn waves-effect waves-light">Cancel Registration</button>
                                <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                            <?php else : ?>
                                <input type="hidden" name="action" value="register">
                                <button type="submit" name="submit" class="btn waves-effect waves-light">Register</button>
                                <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                            <?php endif; ?>
                        </form>
                    <?php elseif ($event->isRegistrationMembersOnly() && !is_user_logged_in()) : ?>
                        <a href="<?= SSV_General::getLoginURL() ?>" class="btn waves-effect waves-light">Login</a>
                    <?php else : ?>
                        <a href="<?= esc_url(get_permalink()) ?>">Open Event to Register</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>
