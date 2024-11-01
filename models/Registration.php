<?php

namespace mp_ssv_events\models;

use mp_ssv_events\SSV_Events;
use mp_ssv_general\custom_fields\Field;
use mp_ssv_general\custom_fields\input_fields\CustomInputField;
use mp_ssv_general\custom_fields\input_fields\TextInputField;
use mp_ssv_general\custom_fields\InputField;
use mp_ssv_general\Message;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 25-7-16
 * Time: 0:08
 */
class Registration
{
    #region Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DENIED = 'denied';

    const MODE_DISABLED = 'disabled';
    const MODE_MEMBERS_ONLY = 'members_only';
    const MODE_EVERYONE = 'everyone';
    #endregion

    #region Variables
    /** @var int */
    public $registrationID;

    /** @var Event */
    public $event;

    /** @var string One of the STATUS_ constants. */
    public $status;

    /** @var User */
    public $user;
    #endregion

    #region Construct
    /**
     * Registration constructor.
     *
     * @param int       $registrationID
     * @param Event     $event
     * @param string    $status
     * @param User|null $user
     */
    private function __construct($registrationID, $event = null, $status = null, $user = null)
    {
        global $wpdb;
        $tableName = SSV_Events::TABLE_REGISTRATION;

        $this->registrationID = $registrationID;

        if ($event == null) {
            $this->event = Event::getByID($wpdb->get_var("SELECT eventID FROM $tableName WHERE ID = $registrationID"));
        } else {
            $this->event = $event;
        }

        if ($status == null) {
            $this->status = $wpdb->get_var("SELECT registration_status FROM $tableName WHERE ID = $registrationID");
        } else {
            $this->status = $status;
        }

        if ($status == null) {
            $this->user = User::getByID($wpdb->get_var("SELECT userID FROM $tableName WHERE ID = $registrationID"));
        } else {
            $this->user = $user;
        }
    }

    /**
     * @param Event $event
     * @param User  $user
     *
     * @return Registration
     */
    public static function getByEventAndUser($event, $user)
    {
        global $wpdb;
        $tableName      = SSV_Events::TABLE_REGISTRATION;
        $eventID        = $event->getID();
        $registrationID = $wpdb->get_var("SELECT ID FROM $tableName WHERE eventID = $eventID AND userID = $user->ID");
        return new Registration($registrationID);
    }

    /**
     * @param int $registrationID
     *
     * @return Registration
     */
    public static function getByID($registrationID)
    {
        return new Registration($registrationID);
    }
    #endregion

    #region createNew($event, $user, $args)
    /**
     * This function creates the database entries, sends an email to the event author and returns the newly created Registration object.
     *
     * @param Event        $event
     * @param User|null    $user
     * @param InputField[] $inputFields
     *
     * @return Message[]|Registration
     */
    public static function createNew($event, $user = null, $inputFields = array())
    {
        #region Validate
        global $wpdb;
        if ($user !== null) {
            $table   = SSV_Events::TABLE_REGISTRATION;
            $eventID = $event->getID();
            $sql     = "SELECT * FROM $table WHERE eventID = $eventID AND userID = '$user->ID'";
        } else {
            $table   = SSV_Events::TABLE_REGISTRATION_META;
            $email   = $inputFields['email']->value;
            $eventID = $event->getID();
            $sql     = "SELECT * FROM $table WHERE eventID = $eventID AND meta_key = 'email' AND meta_value = '$email'";
        }
        if ($wpdb->get_row($sql) !== null) {
            return array(new Message('Already registered.', Message::ERROR_MESSAGE));
        }
        #endregion

        #region Create Base
        $status = get_option(SSV_Events::OPTION_DEFAULT_REGISTRATION_STATUS);
        $wpdb->insert(
            SSV_Events::TABLE_REGISTRATION,
            array(
                'userID'              => $user ? $user->ID : null,
                'eventID'             => $event->getID(),
                'registration_status' => $status,
            ),
            array(
                '%d',
                '%d',
                '%s',
            )
        );
        #endregion

        #region Save Meta Data
        $registrationID = $wpdb->insert_id;
        foreach ($inputFields as $field) {
            if (is_bool($field->value)) {
                $field->value = $field->value ? 'true' : 'false';
            }
            $wpdb->insert(
                SSV_Events::TABLE_REGISTRATION_META,
                array(
                    'registrationID' => $registrationID,
                    'meta_key'       => $field->name,
                    'meta_value'     => $field->value,
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                )
            );
        }
        #endregion

        $registration = new Registration($registrationID, $event, $status, $user);

        #region Email
        if (get_option(SSV_Events::OPTION_EMAIL_AUTHOR)) {
            $eventTitle = Event::getByID($event->getID())->post->post_title;
            $to         = User::getByID(Event::getByID($event->getID())->post->post_author)->user_email;
            $subject    = "New Registration for " . $eventTitle;
            if ($user != null) {
                ob_start();
                ?>User <a href="<?= esc_url($user->getProfileURL()) ?>"><?= esc_html($user->display_name) ?></a> has registered for <a href="<?= esc_url(get_permalink($event->getID())) ?>"><?= esc_html($eventTitle) ?></a> with the following information:<?php
                $message = ob_get_clean();
            } else {
                $message = 'Someone has registered for ' . esc_html($eventTitle) . ' with the following information:<br/>';
            }
            foreach ($inputFields as $field) {
                $message .= $field->title . ': ' . $field->value . '<br/>';
            }
            wp_mail($to, $subject, $message);
        }

        if (get_option(SSV_Events::OPTION_EMAIL_REGISTRANT)) {
            $eventTitle = Event::getByID($event->getID())->post->post_title;
            $to         = $registration->getMeta('email');
            $subject    = "You have registered for " . $eventTitle;
            ob_start();
            ?>You are now registered for <a href="<?= esc_url(get_permalink($event->getID())) ?>"><?= esc_html($eventTitle) ?></a> with the following information:<?php
            $message = ob_get_clean();
            foreach ($inputFields as $field) {
                $message .= $field->title . ': ' . $field->value . '<br/>';
            }
            wp_mail($to, $subject, $message);
        }
        #endregion

        do_action(SSV_General::HOOK_EVENTS_NEW_REGISTRATION, $registration);

        return $registration;
    }

    /**
     * @return InputField[]
     */
    public static function getDefaultFields()
    {
        #region First Name
        /** @var TextInputField $firstNameField */
        $firstNameField = Field::fromJSON(
            json_encode(
                array(
                    'id'             => -1,
                    'title'          => 'First Name',
                    'field_type'     => 'input',
                    'input_type'     => 'text',
                    'name'           => 'first_name',
                    'disabled'       => false,
                    'required'       => true,
                    'default_value'  => '',
                    'placeholder'    => '',
                    'class'          => '',
                    'style'          => '',
                    'override_right' => SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS,
                )
            )
        );
        #endregion

        #region Last Name
        /** @var TextInputField $lastNameField */
        $lastNameField = Field::fromJSON(
            json_encode(
                array(
                    'id'             => -1,
                    'title'          => 'Last Name',
                    'field_type'     => 'input',
                    'input_type'     => 'text',
                    'name'           => 'last_name',
                    'disabled'       => false,
                    'required'       => true,
                    'default_value'  => '',
                    'placeholder'    => '',
                    'class'          => '',
                    'style'          => '',
                    'override_right' => SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS,
                )
            )
        );
        #endregion

        #region Email
        /** @var CustomInputField $emailField */
        $emailField = Field::fromJSON(
            json_encode(
                array(
                    'id'             => -1,
                    'title'          => 'Email',
                    'field_type'     => 'input',
                    'input_type'     => 'email',
                    'name'           => 'email',
                    'disabled'       => false,
                    'required'       => true,
                    'default_value'  => '',
                    'placeholder'    => '',
                    'class'          => '',
                    'style'          => '',
                    'override_right' => SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS,
                )
            )
        );
        #endregion

        return array(
            $firstNameField->name => $firstNameField,
            $lastNameField->name  => $lastNameField,
            $emailField->name     => $emailField,
        );
    }
    #endregion

    #region cancel()
    /**
     * This function removes the database entries and sends an email to the event author (if needed).
     */
    public function cancel()
    {
        if (!$this->user) {
            return;
        }
        global $wpdb;
        $userID  = $this->user->ID;
        $eventID = $this->event->getID();
        $wpdb->delete(SSV_Events::TABLE_REGISTRATION, array('userID' => $userID, 'eventID' => $eventID));
        $wpdb->delete(SSV_Events::TABLE_REGISTRATION_META, array('registrationID' => $this->registrationID));

        if (get_option(SSV_Events::OPTION_EMAIL_AUTHOR)) {
            $eventTitle = $this->event->post->post_title;
            $to         = User::getByID($this->event->post->post_author)->user_email;
            $subject    = "Cancellation for " . $eventTitle;
            $message    = $this->user->display_name . ' has just canceled his/her registration for ' . $eventTitle . '.';
            wp_mail($to, $subject, $message);
        }

        if (get_option(SSV_Events::OPTION_EMAIL_REGISTRANT)) {
            $eventTitle = Event::getByID($this->event->getID())->post->post_title;
            $to         = $this->getMeta('email');
            $subject    = "You have registered for " . $eventTitle;
            ob_start();
            ?>Your registered for <a href="<?= esc_url(get_permalink($this->event->getID())) ?>"><?= esc_html($eventTitle) ?></a> is now canceled.<?php
            $message = ob_get_clean();
            wp_mail($to, $subject, $message);
        }
    }
    #endregion

    #region getMeta($key, $userMeta)
    /**
     * @param      $key
     *
     * @return null|string with the value matched by the key.
     */
    public function getMeta($key)
    {
        global $wpdb;
        $tableName = SSV_Events::TABLE_REGISTRATION_META;
        $value     = $wpdb->get_var("SELECT meta_value FROM $tableName WHERE registrationID = $this->registrationID AND meta_key = '$key'");
        if (empty($value)) {
            $value = $this->user ? $this->user->getMeta($key) : '';
        }
        return $value;
    }
    #endregion

    #region makePending()
    public function makePending()
    {
        global $wpdb;
        $table = SSV_Events::TABLE_REGISTRATION;
        $wpdb->replace(
            $table,
            array(
                "ID"                  => $this->registrationID,
                "eventID"             => $this->event->getID(),
                "userID"              => $this->user->ID,
                "registration_status" => self::STATUS_PENDING,
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%s',
            )
        );
        if (get_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED)) {
            $eventTitle = $this->event->post->post_title;
            $to         = $this->getMeta('email');
            $subject    = "Registration Pending";
            $message    = 'Your registration for ' . $eventTitle . ' has been changed back to Pending.';
            wp_mail($to, $subject, $message);
        }
    }
    #endregion

    #region approve()
    public function approve()
    {
        global $wpdb;
        $table = SSV_Events::TABLE_REGISTRATION;
        $wpdb->replace(
            $table,
            array(
                "ID"                  => $this->registrationID,
                "eventID"             => $this->event->getID(),
                "userID"              => $this->user->ID,
                "registration_status" => self::STATUS_APPROVED,
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%s',
            )
        );
        if (get_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED)) {
            $eventTitle = $this->event->post->post_title;
            $to         = $this->getMeta('email');
            $subject    = "Registration Approved";
            $message    = 'Your registration for ' . $eventTitle . ' has been approved.';
            wp_mail($to, $subject, $message);
        }
    }
    #endregion

    #region deny()
    public function deny()
    {
        global $wpdb;
        $table = SSV_Events::TABLE_REGISTRATION;
        $wpdb->replace(
            $table,
            array(
                "ID"                  => $this->registrationID,
                "eventID"             => $this->event->getID(),
                "userID"              => $this->user->ID,
                "registration_status" => self::STATUS_DENIED,
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%s',
            )
        );
        if (get_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED)) {
            $eventTitle = $this->event->post->post_title;
            $to         = $this->getMeta('email');
            $subject    = "Registration Denied";
            $message    = 'Your registration for ' . $eventTitle . ' has been denied.';
            wp_mail($to, $subject, $message);
        }
    }
    #endregion
}
