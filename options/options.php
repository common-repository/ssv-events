<?php
use mp_ssv_general\SSV_General;

if (!defined('ABSPATH')) {
    exit;
}
function ssv_add_ssv_events_options()
{
    add_submenu_page('ssv_settings', 'Events Options', 'Events', 'manage_options', 'ssv-events-settings', 'ssv_events_options_page_content');
}

function ssv_events_options_page_content()
{
    $active_tab = "general";
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    ?>
    <div class="wrap">
        <h1>Events Options</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?= esc_html($_GET['page']) ?>&tab=general" class="nav-tab <?= SSV_General::currentNavTab($active_tab, 'general') ?>">General</a>
            <a href="?page=<?= esc_html($_GET['page']) ?>&tab=email" class="nav-tab <?= SSV_General::currentNavTab($active_tab, 'email') ?>">Email</a>
            <a href="http://bosso.nl/ssv-events/" target="_blank" class="nav-tab">
                Help <img src="<?= esc_url(SSV_General::URL) ?>/images/link-new-tab-small.png" width="14" style="vertical-align:middle">
            </a>
        </h2>
        <?php
        /** @noinspection PhpIncludeInspection */
        require_once $active_tab . '.php';
        ?>
    </div>
    <?php
}

add_action('admin_menu', 'ssv_add_ssv_events_options');

function ssv_events_general_options_page_content()
{
    ?><h2><a href="?page=<?= __FILE__ ?>">Events Options</a></h2><?php
}

add_action(SSV_General::HOOK_GENERAL_OPTIONS_PAGE_CONTENT, 'ssv_events_general_options_page_content');
