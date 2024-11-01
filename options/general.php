<?php
namespace mp_ssv_events\options;
use mp_ssv_events\models\Registration;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\SSV_General;

if (!defined('ABSPATH')) {
    exit;
}

if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_OPTIONS)) {
    if (isset($_POST['reset'])) {
        SSV_Events::resetGeneralOptions();
    } else {
        update_option(SSV_Events::OPTION_DEFAULT_REGISTRATION_STATUS, SSV_General::sanitize($_POST['default_registration_status'], array('pending', 'approved', 'denied')));
        update_option(SSV_Events::OPTION_REGISTRATION_MESSAGE, SSV_General::sanitize($_POST['registration_message'], 'text'));
        update_option(SSV_Events::OPTION_CANCELLATION_MESSAGE, SSV_General::sanitize($_POST['cancellation_message'], 'text'));
        update_option(SSV_Events::OPTION_MAPS_API_KEY, SSV_General::sanitize($_POST['maps_api_key'], 'text'));
    }
}
?>
<form method="post" action="#">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Default Registration Status</th>
            <td>
                <?php $defaultRegistrationStatus = get_option(SSV_Events::OPTION_DEFAULT_REGISTRATION_STATUS); ?>
                <select name="default_registration_status" title="Default Registration Status">
                    <option value="pending" <?= selected($defaultRegistrationStatus, Registration::STATUS_PENDING) ?>>Pending</option>
                    <option value="approved" <?= selected($defaultRegistrationStatus, Registration::STATUS_APPROVED) ?>>Approved</option>
                    <option value="denied" <?= selected($defaultRegistrationStatus, Registration::STATUS_DENIED) ?>>Denied</option>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Google Maps API Key</th>
            <td>
                <input type="text" class="regular-text" name="maps_api_key" value="<?= esc_html(get_option(SSV_Events::OPTION_MAPS_API_KEY)) ?>" title="Google Maps API Key"/>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Registration Message</th>
            <td><textarea name="registration_message" class="large-text" title="Registration Message"><?= esc_html(get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE)) ?></textarea></td>
        </tr>
        <tr valign="top">
            <th scope="row">Cancellation Message</th>
            <td><textarea name="cancellation_message" class="large-text" title="cancellation Message"><?= esc_html(get_option(SSV_Events::OPTION_CANCELLATION_MESSAGE)) ?></textarea></td>
        </tr>
    </table>
    <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_OPTIONS); ?>
</form>
