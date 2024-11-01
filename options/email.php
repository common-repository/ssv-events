<?php
namespace mp_ssv_events\options;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\SSV_General;

if (!defined('ABSPATH')) {
    exit;
}

if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_OPTIONS)) {
    if (isset($_POST['reset'])) {
        SSV_Events::resetEmailOptions();
    } else {
        update_option(SSV_Events::OPTION_EMAIL_AUTHOR, SSV_General::sanitize($_POST['email_author_on_registration'], 'boolean'));
        update_option(SSV_Events::OPTION_EMAIL_REGISTRANT, SSV_General::sanitize($_POST['email_registrant_on_registration'], 'boolean'));
        update_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED, SSV_General::sanitize($_POST['email_on_registration_status_changed'], 'boolean'));
    }
}
?>
<form method="post" action="#">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Email Author</th>
            <td>
                <label>
                    <input type="hidden" name="email_author_on_registration" value="false"/>
                    <input type="checkbox" name="email_author_on_registration" value="true" <?= get_option(SSV_Events::OPTION_EMAIL_AUTHOR) ? 'checked' : '' ?> />
                    When someone registers or cancels the event author will receive an email.
                </label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Email Registrant</th>
            <td>
                <label>
                    <input type="hidden" name="email_registrant_on_registration" value="false"/>
                    <input type="checkbox" name="email_registrant_on_registration" value="true" <?= get_option(SSV_Events::OPTION_EMAIL_REGISTRANT) ? 'checked' : '' ?> />
                    When someone registers or cancels he/she will receive a confirmation email.
                </label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Registration Status Changed</th>
            <td>
                <label>
                    <input type="hidden" name="email_on_registration_status_changed" value="false"/>
                    <input type="checkbox" name="email_on_registration_status_changed" value="true" <?= get_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED) ? 'checked' : '' ?>/>
                    When an event admin changes someones registration, the registrant will receive and email on the status change.
                </label>
            </td>
        </tr>
    </table>
    <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_OPTIONS); ?>
</form>
