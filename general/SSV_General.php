<?php

namespace mp_ssv_general;

use DateTime;
use Exception;
use mp_ssv_users\SSV_Users;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 26-1-17
 * Time: 15:50
 */
class SSV_General
{
    #region Constants
    const PATH = SSV_GENERAL_PATH;
    const URL = SSV_GENERAL_URL;
    const CUSTOM_FIELDS_TABLE = SSV_GENERAL_CUSTOM_FIELDS_TABLE;

    const BASE_URL = SSV_GENERAL_BASE_URL;

    const HOOK_USER_PROFILE_URL = 'ssv_general__hook_profile_url';
    const HOOK_GENERAL_OPTIONS_PAGE_CONTENT = 'ssv_general__hook_general_options_page_content';
    const HOOK_RESET_OPTIONS = 'ssv_general__hook_reset_options';

    const HOOK_USERS_SAVE_MEMBER = 'ssv_users__hook_save_member';
    const HOOK_USERS_NEW_EVENT = 'ssv_events__hook_new_event';
    const HOOK_EVENTS_NEW_REGISTRATION = 'ssv_events__hook_new_registration';

    const USER_OPTION_CUSTOM_FIELD_FIELDS = 'ssv_general__custom_field_fields';
    const OPTIONS_ADMIN_REFERER = 'ssv_general__options_admin_referer';
    #endregion

    #region _init()
    private static $initialized = false;

    public static function _init()
    {
        if (!self::$initialized) {
            require_once 'functions.php';
            require_once 'options/options.php';
            require_once 'models/User.php';
            require_once 'models/Message.php';
            require_once 'models/Form.php';
            self::$initialized = true;
        }
    }
    #endregion

    #region resetOptions()
    /**
     * This function sets all the options for this plugin back to their default value
     */
    public static function resetOptions()
    {
        $defaultSelected = json_encode(array('display', 'default', 'placeholder'));
        User::getCurrent()->updateMeta(SSV_General::USER_OPTION_CUSTOM_FIELD_FIELDS, $defaultSelected, false);
    }
    #endregion

    #region Tools

    #region redirect($location)
    /**
     * This function can be called from anywhere and will redirect the page to the given location.
     *
     * @param string $location is the url where the page should be redirected to.
     */
    public static function redirect($location)
    {
        $redirect_script = '<script type="text/javascript">';
        $redirect_script .= 'window.location = "' . $location . '"';
        $redirect_script .= '</script>';
        echo $redirect_script;
    }
    #endregion

    #region isValidPOST($adminReferer)
    /**
     * @param $adminReferer
     *
     * @return bool true if the request is POST, it isn't a reset request and it has the correct admin referer.
     */
    public static function isValidPOST($adminReferer)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return false;
        }
        if (!isset($_POST['admin_referer']) || $_POST['admin_referer'] != $adminReferer) {
            return false;
        }
        if (!check_admin_referer($adminReferer)) {
            return false;
        }
        return true;
    }
    #endregion

    #region isValidIBAN($iban)
    public static function isValidIBAN($iban)
    {
        $iban      = strtolower(str_replace(' ', '', $iban));
        $Countries = array('al' => 28, 'ad' => 24, 'at' => 20, 'az' => 28, 'bh' => 22, 'be' => 16, 'ba' => 20, 'br' => 29, 'bg' => 22, 'cr' => 21, 'hr' => 21, 'cy' => 28, 'cz' => 24, 'dk' => 18, 'do' => 28, 'ee' => 20, 'fo' => 18, 'fi' => 18, 'fr' => 27, 'ge' => 22, 'de' => 22, 'gi' => 23, 'gr' => 27, 'gl' => 18, 'gt' => 28, 'hu' => 28, 'is' => 26, 'ie' => 22, 'il' => 23, 'it' => 27, 'jo' => 30, 'kz' => 20, 'kw' => 30, 'lv' => 21, 'lb' => 28, 'li' => 21, 'lt' => 20, 'lu' => 20, 'mk' => 19, 'mt' => 31, 'mr' => 27, 'mu' => 30, 'mc' => 27, 'md' => 24, 'me' => 22, 'nl' => 18, 'no' => 15, 'pk' => 24, 'ps' => 29, 'pl' => 28, 'pt' => 25, 'qa' => 29, 'ro' => 24, 'sm' => 27, 'sa' => 24, 'rs' => 22, 'sk' => 24, 'si' => 19, 'es' => 24, 'se' => 24, 'ch' => 21, 'tn' => 24, 'tr' => 26, 'ae' => 23, 'gb' => 22, 'vg' => 24);
        $Chars     = array('a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17, 'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23, 'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29, 'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35);

        if (empty($iban)) {
            return false;
        }

        try {
            if (strlen($iban) == $Countries[substr($iban, 0, 2)]) {

                $MovedChar      = substr($iban, 4) . substr($iban, 0, 4);
                $MovedCharArray = str_split($MovedChar);
                $NewString      = '';

                foreach ($MovedCharArray AS $key => $value) {
                    if (!is_numeric($MovedCharArray[$key])) {
                        $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
                    }
                    $NewString .= $MovedCharArray[$key];
                }

                if (self::bcmod($NewString, '97') == 1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    public static function bcmod($x, $y)
    {
        $take = 5;
        $mod  = '';

        do {
            $a   = (int)$mod . substr($x, 0, $take);
            $x   = substr($x, $take);
            $mod = $a % $y;
        } while (strlen($x));

        return (int)$mod;
    }
    #endregion

    #region getFormSecurityFields($adminReferer, $save, $reset)
    /**
     * @param string      $adminReferer should be defined by a constant from the class you want to use this form in.
     * @param bool|string $saveButton   set to false if you don't want the save button to be displayed or give string to set custom button text.
     * @param bool|string $resetButton  set to false if you don't want the reset button to be displayed or give string to set custom button text.
     *
     * @return string HTML
     */
    public static function getFormSecurityFields($adminReferer, $saveButton = true, $resetButton = true)
    {
        ob_start();
        ?><input type="hidden" name="admin_referer" value="<?= $adminReferer ?>"><?php
        wp_nonce_field($adminReferer);
        if (is_string($saveButton)) {
            submit_button($saveButton);
        } elseif ($saveButton === true) {
            submit_button();
        }
        if ($resetButton) {
            ?><input type="submit" name="reset" id="reset" class="button button-primary" value="<?= is_string($resetButton) ? $resetButton : 'Reset to Default' ?>"><?php
        }
        return ob_get_clean();
    }
    #endregion

    #region sanitize($value)
    /**
     * @param mixed  $value
     * @param string|array $sanitationType
     *
     * @return string
     */
    public static function sanitize($value, $sanitationType)
    {
        if (is_array($value)) {
            foreach ($value as &$item) {
                self::sanitize($item, $sanitationType);
            }
            return $value;
        }
        if (is_array($sanitationType)) {
            if (!in_array($value, $sanitationType)) {
                $value = sanitize_text_field(array_values($sanitationType)[0]);
            }
        } elseif (strpos($sanitationType, 'email') !== false) {
            $value = sanitize_email($value);
        } elseif (strpos($sanitationType, 'file') !== false) {
            $value = sanitize_file_name($value);
        } elseif (strpos($sanitationType, 'color') !== false) {
            $value = sanitize_hex_color($value);
        } elseif (strpos($sanitationType, 'class') !== false) {
            $value = sanitize_html_class($value);
        } elseif (strpos($sanitationType, 'option') !== false) {
            $value = sanitize_option($sanitationType, $value);
        } elseif (strpos($sanitationType, 'date') !== false && strpos($sanitationType, 'time') !== false) {
            $dateTime = DateTime::createFromFormat('Y-m-d H:i', sanitize_text_field($value));
            if ($dateTime) {
                $value = $dateTime->format('Y-m-d H:i');
            } else {
                $value = '';
            }
        } elseif (strpos($sanitationType, 'date') !== false) {
            $date = DateTime::createFromFormat('Y-m-d', sanitize_text_field($value));
            if ($date) {
                $value = $date->format('Y-m-d');
            } else {
                $value = '';
            }
        } elseif (strpos($sanitationType, 'time') !== false) {
            $time = DateTime::createFromFormat('H:i', sanitize_text_field($value));
            if ($time) {
                $value = $time->format('H:i');
            } else {
                $value = '';
            }
        } elseif ($sanitationType == 'boolean' || $sanitationType == 'bool') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } elseif ($sanitationType == 'int') {
            $value = intval($value);
        } else {
            $value = sanitize_text_field($value);
        }
        return $value;
    }
    #endregion

    #region var_export($variable, $die)
    /**
     * This function is for development purposes only and lets the developer print a variable in the PHP formatting to inspect what the variable is set to.
     *
     * @param mixed $variable any variable that you want to be printed.
     * @param bool  $die      set true if you want to call die() after the print. $die is ignored if $return is true.
     *
     * @return mixed|null|string returns the print in string if $return is true, returns null if $return is false, and doesn't return if $die is true.
     */
    public static function var_export($variable, $die = false)
    {
        if (is_string($variable) && strpos($variable, 'FROM') !== false && strpos($variable, 'WHERE') !== false) {
            ob_start();
            echo $variable . ';';
            $query = ob_get_clean();
            include_once('lib/SqlFormatter.php');
            $print = SqlFormatter::highlight($query);
            $print = trim(preg_replace('/\s+/', ' ', $print));
        } else {
            if (self::_hasCircularReference($variable)) {
                $print = highlight_string("<?php " . var_dump($variable, true), true);
            } else {
                $print = highlight_string("<?php " . var_export($variable, true), true);
            }
            $print = trim($print);
            /** @noinspection HtmlUnknownAttribute */
            $print = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", '', $print, 1);  // remove prefix
            $print = preg_replace("|\\</code\\>\$|", '', $print, 1);
            $print = trim($print);
            $print = preg_replace("|\\</span\\>\$|", '', $print, 1);
            $print = trim($print);
            /** @noinspection HtmlUnknownAttribute */
            $print = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $print);
            $print .= ';';
        }
        echo $print;
        echo '<br/>';

        if ($die) {
            die();
        }
        return null;
    }
    #endregion

    #region _hasCircularReference($variable)

    /**
     * This function checks if the given $variable is recursive.
     *
     * @param mixed $variable is the variable to be checked.
     *
     * @return bool true if the $variable contains circular reference.
     */
    private static function _hasCircularReference($variable)
    {
        $dump = print_r($variable, true);
        if (strpos($dump, '*RECURSION*') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function eventsPluginActive()
    {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        return is_plugin_active('ssv-events/ssv-events.php');
    }

    public static function usersPluginActive()
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        if (is_plugin_active('ssv-users/ssv-users.php')) {
            return true;
        } else {
            return false;
        }
    }

    public static function mailchimpPluginActive()
    {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        return is_plugin_active('ssv-mailchimp/ssv-mailchimp.php');
    }

    public static function getLoginURL()
    {
        if (self::usersPluginActive()) {
            $loginPages = SSV_Users::getPagesWithTag(SSV_Users::TAG_LOGIN_FIELDS);
            if (count($loginPages) > 0) {
                return add_query_arg('redirect_to', get_permalink(), get_permalink($loginPages[0]));
            }
        }
        return site_url() . '/wp-login.php?redirect_to=' . site_url();
    }
    #endregion

    #region getListSelect($name, $options, $selected)
    public static function getListSelect($name, $options, $selected)
    {
        if (is_array($selected)) {
            foreach ($selected as &$item) {
                $item = esc_html($item);
            }
        } elseif (strpos($selected, ',') !== false) {
            $selected = explode(',', $selected);
            foreach ($selected as &$item) {
                $item = esc_html($item);
            }
        } else {
            $selected = esc_html($selected);
        }
        $name = esc_html($name);
        ob_start();
        $optionCount = count($options);
        ?>
        <div style="float:left;margin-right:20px;">
            <label for="non_selected_fields">Available</label>
            <br/>
            <select id="non_selected_fields" size="<?= $optionCount > 25 ? 25 : $optionCount ?>" multiple title="Columns to Export" style="min-width: 200px;">
                <?php foreach ($options as $option): ?>
                    <?php $option = esc_html($option); ?>
                    <option id="<?= $name ?>_non_selected_result_<?= $option ?>" onClick='<?= $name ?>_add("<?= $option ?>")' value="<?= $option ?>" <?= in_array($option, $selected) ? 'disabled' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="float:left;margin-right:20px;">
            <label for="selected_fields">Selected</label>
            <br/>
            <select id="selected_fields" size="<?= $optionCount > 25 ? 25 : $optionCount ?>" multiple title="Columns to Export" style="min-width: 200px;">
                <?php foreach ($selected as $option): ?>
                    <?php $option = esc_html($option); ?>
                    <option id="<?= $name ?>_selected_result_<?= $option ?>" onClick='<?= $name ?>_remove("<?= $option ?>")' value="<?= $option ?>"><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" id="<?= $name ?>" name="<?= $name ?>" value=""/>
        <!--suppress JSUnusedAssignment -->
        <script>
            var options = <?= json_encode($selected) ?>;
            document.getElementById('<?= $name ?>').value = options;
            function <?= $name ?>_add(val) {
                options.push(val);
                document.getElementById('<?= $name ?>').value = options;
                var option = document.createElement("option");
                option.id = '<?= $name ?>_selected_result_' + val;
                option.text = val;
                option.addEventListener("click", function () {
                    <?= $name ?>_remove(val);
                }, false);
                document.getElementById('selected_fields').add(option);
                option = document.getElementById('<?= $name ?>_non_selected_result_' + val);
                option.setAttribute("disabled", "disabled");
            }

            function <?= $name ?>_remove(val) {
                var index = options.indexOf(val);
                if (index > -1) {
                    options.splice(index, 1);
                }
                document.getElementById('<?= $name ?>').value = options;
                var option = document.getElementById('<?= $name ?>_non_selected_result_' + val);
                option.removeAttribute("disabled");
                option = document.getElementById('<?= $name ?>_selected_result_' + val);
                option.parentNode.removeChild(option);
            }
        </script>
        <?php
        return ob_get_clean();
    }
    #endregion

    #region currentNavTab($object, $selected)
    public static function currentNavTab($current, $selected)
    {
        return $current == $selected ? 'nav-tab-active' : '';
    }
    #endregion
    #endregion
}
