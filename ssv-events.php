<?php
/**
 * Plugin Name: SSV Events
 * Plugin URI: https://bosso.nl/ssv-events/
 * Description: SSV Events is a plugin that allows you to create events for the Students Sports Club and allows all members from that club to join the event.
 * Version: 3.2.7
 * Author: moridrin
 * Author URI: http://nl.linkedin.com/in/jberkvens/
 * License: WTFPL
 * License URI: http://www.wtfpl.net/txt/copying/
 */

namespace mp_ssv_events;
global $wpdb;
define('SSV_EVENTS_PATH', plugin_dir_path(__FILE__));
define('SSV_EVENTS_URL', plugins_url() . '/ssv-events/');
define('SSV_EVENTS_REGISTRATION_TABLE', $wpdb->prefix . "ssv_event_registration");
define('SSV_EVENTS_REGISTRATION_META_TABLE', $wpdb->prefix . "ssv_event_registration_meta");

if (!defined('ABSPATH')) {
    exit;
}

#region Require Once
require_once 'general/general.php';
require_once 'functions.php';

require_once "options/options.php";

require_once "models/Event.php";
require_once "models/Registration.php";

require_once "custom-post-type/post-type.php";
require_once "custom-post-type/event-views/page-full.php";

require_once "widgets/category-widget.php";
require_once "widgets/upcoming-events-widget.php";
#endregion

#region SSV_Events class
class SSV_Events
{
    #region Constants
    const PATH = SSV_EVENTS_PATH;
    const URL = SSV_EVENTS_URL;

    const TABLE_REGISTRATION = SSV_EVENTS_REGISTRATION_TABLE;
    const TABLE_REGISTRATION_META = SSV_EVENTS_REGISTRATION_META_TABLE;

    const OPTION_DEFAULT_REGISTRATION_STATUS = 'ssv_events__default_registration_status';
    const OPTION_REGISTRATION_MESSAGE = 'ssv_events__registration_message';
    const OPTION_CANCELLATION_MESSAGE = 'ssv_events__cancellation_message';
    const OPTION_EMAIL_AUTHOR = 'ssv_events__email_author';
    const OPTION_EMAIL_REGISTRANT = 'ssv_events__email_registrant';
    const OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED = 'ssv_events__email_on_registration_status_changed';
    const OPTION_PUBLISH_ERROR = 'ssv_events__publish_error';
    const OPTION_MAPS_API_KEY = 'ssv_events__google_maps_api_key';

    const ADMIN_REFERER_OPTIONS = 'ssv_events__admin_referer_options';
    const ADMIN_REFERER_REGISTRATION = 'ssv_events__admin_referer_registration';

    const CAPABILITY_MANAGE_EVENTS = 'manage_events';
    const CAPABILITY_MANAGE_EVENT_REGISTRATIONS = 'manage_event_registrations';
    #endregion

    #region resetOptions()
    /**
     * This function sets all the options for this plugin back to their default value
     */
    public static function resetOptions()
    {
        self::resetGeneralOptions();
        self::resetEmailOptions();
        update_option(self::OPTION_PUBLISH_ERROR, false);
    }

    #region resetGeneralOptions()

    /**
     * This function sets all the options on the General Tab back to their default value
     */
    public static function resetGeneralOptions()
    {
        update_option(self::OPTION_DEFAULT_REGISTRATION_STATUS, 'pending');
        update_option(self::OPTION_REGISTRATION_MESSAGE, 'Your registration is pending.');
        update_option(self::OPTION_CANCELLATION_MESSAGE, 'Your registration is canceled.');
    }
    #endregion

    #region resetEmailOptions()
    /**
     * This function sets all the options on the Email Tab back to their default value
     */
    public static function resetEmailOptions()
    {
        update_option(self::OPTION_EMAIL_AUTHOR, true);
        update_option(self::OPTION_EMAIL_REGISTRANT, true);
        update_option(self::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED, false);
    }
    #endregion

    #endregion

    public static function CLEAN_INSTALL()
    {
        mp_ssv_events_uninstall();
        mp_ssv_events_register_plugin();
    }
}

#endregion
