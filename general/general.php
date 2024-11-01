<?php
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('mp_ssv_general\SSV_General')) {
    require_once 'models/custom-fields/Field.php';

    #region Register Scripts
    function mp_ssv_general_admin_scripts()
    {
        wp_enqueue_script('mp-ssv-input-field-selector', SSV_General::URL . '/js/mp-ssv-input-field-selector.js', array('jquery'));
        wp_localize_script(
            'mp-ssv-input-field-selector',
            'settings',
            array(
                'custom_field_fields' => User::getCurrent()->getMeta(SSV_General::USER_OPTION_CUSTOM_FIELD_FIELDS, json_encode(array('display', 'default', 'placeholder'))),
                'roles'               => json_encode(array_keys(get_editable_roles())),
            )
        );
        wp_enqueue_script('mp-ssv-sortable-tables', SSV_General::URL . '/js/mp-ssv-sortable-tables.js', array('jquery', 'jquery-ui-sortable'));
    }

    add_action('admin_enqueue_scripts', 'mp_ssv_general_admin_scripts');
    #endregion

    global $wpdb;
    define('SSV_GENERAL_PATH', plugin_dir_path(__FILE__));
    define('SSV_GENERAL_URL', plugins_url() . '/' . plugin_basename(__DIR__));
    define('SSV_GENERAL_BASE_URL', (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
    define('SSV_GENERAL_CUSTOM_FIELDS_TABLE', $wpdb->prefix . "ssv_general_custom_fields");
    require_once 'SSV_General.php';

    SSV_General::_init();

    #region Register
    function mp_ssv_general_register_plugin()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = SSV_General::CUSTOM_FIELDS_TABLE;
        $sql
                    = "
		CREATE TABLE IF NOT EXISTS $table_name (
			ID bigint(20) NOT NULL,
			postID bigint(20) NOT NULL,
			fieldName VARCHAR(50) NOT NULL,
			fieldTitle VARCHAR(50) NOT NULL,
			customField TEXT NOT NULL,
            UNIQUE (postID, fieldName),
			PRIMARY KEY (ID, postID)
		) $charset_collate;";
        $wpdb->query($sql);
    }
    #endregion
}
