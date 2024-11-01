<?php
if (!defined('ABSPATH')) {
    exit;
}
use mp_ssv_events\SSV_Events;
use mp_ssv_general\SSV_General;

#region Register
function mp_ssv_events_register_plugin()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();

    #region Registration Table
    $table_name = SSV_Events::TABLE_REGISTRATION;
    $sql
                = "
		CREATE TABLE IF NOT EXISTS $table_name (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			eventID bigint(20) NOT NULL,
			userID bigint(20),
			registration_status VARCHAR(15) NOT NULL DEFAULT 'pending',
			PRIMARY KEY (ID)
		) $charset_collate;";
    $wpdb->query($sql);
    #endregion

    #region Registration Meta Table
    $table_name = SSV_Events::TABLE_REGISTRATION_META;
    $sql
                = "
		CREATE TABLE IF NOT EXISTS $table_name (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			registrationID bigint(20) NOT NULL,
			meta_key VARCHAR(255) NOT NULL,
			meta_value VARCHAR(255),
			PRIMARY KEY (ID)
		) $charset_collate;";
    $wpdb->query($sql);
    #endregion

    #region Custom Capabilities
    $roles = get_editable_roles();
    /**
     * @var int     $key
     * @var WP_Role $role
     */
    foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
        if (isset($roles[$key]) && $role->has_cap('edit_posts')) {
            $role->add_cap(SSV_Events::CAPABILITY_MANAGE_EVENTS);
            $role->add_cap(SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS);
        }
    }
    #endregion

    SSV_Events::resetOptions();
}

register_activation_hook(SSV_EVENTS_PATH. 'ssv-events.php', 'mp_ssv_events_register_plugin');
register_activation_hook(SSV_EVENTS_PATH. 'ssv-events.php', 'mp_ssv_general_register_plugin');
#endregion

#region Unregister
function mp_ssv_events_unregister()
{
    $roles = get_editable_roles();
    /**
     * @var int     $key
     * @var WP_Role $role
     */
    foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
        if (isset($roles[$key]) && $role->has_cap(SSV_Events::CAPABILITY_MANAGE_EVENTS)) {
            $role->remove_cap(SSV_Events::CAPABILITY_MANAGE_EVENTS);
        }
        if (isset($roles[$key]) && $role->has_cap(SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS)) {
            $role->remove_cap(SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS);
        }
    }
}

register_deactivation_hook(SSV_EVENTS_PATH. 'ssv-events.php', 'mp_ssv_events_unregister');
#endregion

#region UnInstall
function mp_ssv_events_uninstall()
{
    global $wpdb;
    $wpdb->show_errors();
    $table_name = SSV_Events::TABLE_REGISTRATION;
    $sql        = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
    $table_name = SSV_Events::TABLE_REGISTRATION_META;
    $sql        = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}

register_uninstall_hook(SSV_EVENTS_PATH. 'ssv-events.php', 'mp_ssv_events_uninstall');
#endregion

#region Reset Options
/**
 * This function will reset the events options if the admin referer originates from the SSV Events plugin.
 *
 * @param $admin_referer
 */
function mp_ssv_events_reset_options($admin_referer)
{
    if (!mp_ssv_starts_with($admin_referer, 'ssv_events__')) {
        return;
    }
    SSV_Events::resetOptions();
}

add_filter(SSV_General::HOOK_RESET_OPTIONS, 'mp_ssv_events_reset_options');
#endregion
wp_enqueue_script('ssv_events_maps', SSV_Events::URL . '/js/maps.js', array('jquery'));

function mp_ssv_events_enquire_admin_scripts()
{
    wp_enqueue_script('ssv_events_datetimepicker', SSV_Events::URL . '/js/jquery.datetimepicker.full.js', 'jquery-ui-datepicker');
    wp_enqueue_script('ssv_events_datetimepicker_admin_init', SSV_Events::URL . '/js/admin-init.js', 'ssv_events_datetimepicker');
    wp_enqueue_style('ssv_events_datetimepicker_admin_css', SSV_Events::URL . '/css/jquery.datetimepicker.css');
}

add_action('admin_enqueue_scripts', 'mp_ssv_events_enquire_admin_scripts', 12);

#region Update Settings Message.
function mp_ssv_events_update_settings_notification()
{
    if (empty(get_option(SSV_Events::OPTION_MAPS_API_KEY))) {
        ?>
        <div class="update-nag notice">
            <p>You still need to set the Google Maps API Key in order for the maps to work.</p>
            <p><a href="/wp-admin/admin.php?page=ssv-events-settings&tab=general">Set Now</a></p>
        </div>
        <?php
    }
}

add_action('admin_notices', 'mp_ssv_events_update_settings_notification');
#endregion
