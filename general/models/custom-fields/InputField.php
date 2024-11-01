<?php

namespace mp_ssv_general\custom_fields;

use Exception;
use mp_ssv_general\custom_fields\input_fields\CheckboxInputField;
use mp_ssv_general\custom_fields\input_fields\CustomInputField;
use mp_ssv_general\custom_fields\input_fields\DateInputField;
use mp_ssv_general\custom_fields\input_fields\HiddenInputField;
use mp_ssv_general\custom_fields\input_fields\ImageInputField;
use mp_ssv_general\custom_fields\input_fields\RoleCheckboxInputField;
use mp_ssv_general\custom_fields\input_fields\RoleSelectInputField;
use mp_ssv_general\custom_fields\input_fields\SelectInputField;
use mp_ssv_general\custom_fields\input_fields\TextInputField;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

require_once 'input-fields/TextInputField.php';
require_once 'input-fields/CheckboxInputField.php';
require_once 'input-fields/SelectInputField.php';
require_once 'input-fields/ImageInputField.php';
require_once 'input-fields/HiddenInputField.php';
require_once 'input-fields/CustomInputField.php';
require_once 'input-fields/DateInputField.php';
require_once 'input-fields/RoleCheckboxInputField.php';
require_once 'input-fields/RoleSelectInputField.php';

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 6-1-17
 * Time: 6:38
 */
class InputField extends Field
{
    const FIELD_TYPE = 'input';

    /** @var string $inputType */
    public $inputType;
    /** @var string $name */
    public $name;

    /** @var string $value */
    public $value;

    /**
     * InputField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $inputType
     * @param string $name
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $inputType, $name, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, self::FIELD_TYPE, $class, $style, $overrideRight);
        $this->inputType = $inputType;
        $this->name      = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', strtolower($name)));
    }

    /**
     * @param string $json
     *
     * @return InputField
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        switch ($values->input_type) {
            case TextInputField::INPUT_TYPE:
                return TextInputField::fromJSON($json);
            case SelectInputField::INPUT_TYPE:
                return SelectInputField::fromJSON($json);
            case CheckboxInputField::INPUT_TYPE:
                return CheckboxInputField::fromJSON($json);
            case DateInputField::INPUT_TYPE:
                return DateInputField::fromJSON($json);
            case RoleCheckboxInputField::INPUT_TYPE:
                return RoleCheckboxInputField::fromJSON($json);
            case RoleSelectInputField::INPUT_TYPE:
                return RoleSelectInputField::fromJSON($json);
            case ImageInputField::INPUT_TYPE:
                return ImageInputField::fromJSON($json);
            case HiddenInputField::INPUT_TYPE:
                return HiddenInputField::fromJSON($json);
            default:
                return CustomInputField::fromJSON($json);
        }
    }

    /**
     * @param bool $forDatabase
     *
     * @return string the class as JSON object.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function toJSON($forDatabase = false)
    {
        throw new Exception('This should be implemented in a sub class.');
    }

    /**
     * @param string $overrideRight is the right needed to override disabled and required parameters of the field.
     *
     * @return string the field as HTML object.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function getHTML($overrideRight)
    {
        throw new Exception('This should be implemented in sub class: ' . get_class($this) . '.');
    }

    /**
     * @return string the field as HTML object.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function getFilterRow()
    {
        throw new Exception('This should be implemented in sub class: ' . get_class($this) . '.');
    }

    /**
     * @param string $filter HTML string with the filter rows.
     *
     * @return string the field as HTML object.
     */
    public function getFilterRowBase($filter)
    {
        ob_start();
        ?>
        <td>
            <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?></label>
        </td>
        <td>
            <label>
                Filter
                <input id="filter_<?= esc_html($this->id) ?>" type="checkbox" name="filter_<?= esc_html($this->name) ?>">
            </label>
        </td>
        <td>
            <?= $filter ?>
        </td>
        <?php
        return ob_get_clean();
    }

    /**
     * @return array|bool array of errors or true if no errors.
     * @throws Exception if the method is not implemented by a sub class.
     */
    public function isValid()
    {
        throw new Exception('This should be implemented in sub class: ' . get_class($this) . '.');
    }

    /**
     * @param string|array|User|mixed $value
     */
    public function setValue($value)
    {
        if (get_class($this) == HiddenInputField::class) {
            return; //Can't change the value of hidden fields.
        }
        if ($value instanceof User) { //User values can always be set (even if isDisabled())
            $this->value = $value->getMeta($this->name);
        } elseif (is_array($value)) {
            if (isset($value[$this->name])) {
                $this->value = SSV_General::sanitize($value[$this->name], $this->name);
            }
        } else {
            $this->value = SSV_General::sanitize($value, $this->name);
        }
    }

    /**
     * @return bool returns if the field is disabled or not.
     */
    public function isDisabled()
    {
        if ($this instanceof CheckboxInputField
            || $this instanceof CustomInputField
            || $this instanceof SelectInputField
            || $this instanceof TextInputField
        ) {
            return $this->disabled;
        } else {
            return false;
        }
    }

    /**
     * @param int $id     is the new ID for the field (it currently has the old ID to find the old row).
     * @param int $postID is the ID of the post.
     */
    public function updateName($id, $postID)
    {
        global $wpdb;
        $table = SSV_General::CUSTOM_FIELDS_TABLE;
        $sql   = "SELECT customField FROM $table WHERE ID = $id AND postID = $postID";
        $json  = $wpdb->get_var($sql);
        if (empty($json)) {
            return;
        }
        $field = Field::fromJSON($json);
        if (!$field instanceof InputField) {
            return;
        }
        $wpdb->update(
            $wpdb->usermeta,
            array(
                'meta_key' => $this->name,
            ),
            array(
                'meta_key' => $field->name,
            ),
            array(
                '%s',
            ),
            array(
                '%s',
            )
        );
    }

    function __toString()
    {
        return $this->name;
    }

}
