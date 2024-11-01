<?php
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

#region Menu Items
function ssv_add_ssv_menu()
{
    add_menu_page('SSV Options', 'SSV Options', 'edit_posts', 'ssv_settings', 'ssv_settings_page');
    add_submenu_page('ssv_settings', 'General', 'General', 'edit_posts', 'ssv_settings');
}

add_action('admin_menu', 'ssv_add_ssv_menu', 9);
#endregion

#region Page Content
function ssv_settings_page()
{
    $fields   = array(
        'display',
        'default',
        'placeholder',
        'class',
        'style',
    );

    if (SSV_General::isValidPOST(SSV_General::OPTIONS_ADMIN_REFERER)) {
        if (isset($_POST['reset'])) {
            SSV_General::resetOptions();
        } else {
            $customFieldFields = isset($_POST['custom_field_fields']) ? SSV_General::sanitize($_POST['custom_field_fields'], $fields) : array();
            User::getCurrent()->updateMeta(SSV_General::USER_OPTION_CUSTOM_FIELD_FIELDS, json_encode($customFieldFields), false);
        }
    }
    ?>
    <div class="wrap">
        <h1>SSV Plugins</h1>
    </div>
    <?php do_action(SSV_General::HOOK_GENERAL_OPTIONS_PAGE_CONTENT); ?>
    <div class="wrap">
        <h1>General Options</h1>
    </div>
    <form method="post" action="#">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="custom_field_fields">Custom Field Fields</label>
                </th>
                <td>
                    <?php
                    $selected = json_decode(User::getCurrent()->getMeta(SSV_General::USER_OPTION_CUSTOM_FIELD_FIELDS, json_encode(array('display', 'default', 'placeholder'))));
                    $selected = $selected ?: array();
                    ?>
                    <select id="custom_field_fields" size="<?= count($fields) ?>" name="custom_field_fields[]" multiple>
                        <?php
                        foreach ($fields as $field) {
                            ?>
                            <option value="<?= $field ?>" <?= in_array($field, $selected) ? 'selected' : '' ?>>
                                <?= $field ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?= SSV_General::getFormSecurityFields(SSV_General::OPTIONS_ADMIN_REFERER, true, 'Reset Preference'); ?>
    </form>
    <?php
}
#endregion
