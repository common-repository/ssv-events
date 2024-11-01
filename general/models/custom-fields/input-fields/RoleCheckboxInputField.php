<?php

namespace mp_ssv_general\custom_fields\input_fields;

use Exception;
use mp_ssv_general\custom_fields\InputField;
use mp_ssv_general\Message;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 10-1-17
 * Time: 12:03
 */
class RoleCheckboxInputField extends InputField
{
    const INPUT_TYPE = 'role_checkbox';

    /**
     * CheckboxInputField constructor.
     *
     * @param int          $id
     * @param string       $title
     * @param string|array $name
     * @param string       $class
     * @param string       $style
     * @param string       $overrideRight
     */
    protected function __construct($id, $title, $name, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, self::INPUT_TYPE, $name, $class, $style, $overrideRight);
    }

    /**
     * @param string $json
     *
     * @return RoleCheckboxInputField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        if ($values->input_type != self::INPUT_TYPE) {
            throw new Exception('Incorrect input type');
        }
        return new RoleCheckboxInputField(
            $values->id,
            $values->title,
            $values->name,
            $values->class,
            $values->style,
            $values->override_right
        );
    }

    /**
     * @param bool $forDatabase
     *
     * @return string the class as JSON object.
     */
    public function toJSON($forDatabase = false)
    {
        $values = array(
            'id'             => $this->id,
            'title'          => $this->title,
            'field_type'     => $this->fieldType,
            'input_type'     => $this->inputType,
            'name'           => $this->name,
            'class'          => $this->class,
            'style'          => $this->style,
            'override_right' => $this->overrideRight,
        );
        if (!$forDatabase) {
            $values['title'] = $this->title;
            $values['name']  = $this->name;
        }
        $values = json_encode($values);
        return $values;
    }

    /**
     * @param string $overrideRight is the right needed to override disabled and required parameters of the field.
     *
     * @return string the field as HTML object.
     */
    public function getHTML($overrideRight)
    {
        $name     = 'name="' . esc_html($this->name) . '"';
        $class    = !empty($this->class) ? 'class="' . esc_html($this->class) . '"' : 'class="validate filled-in"';
        $style    = !empty($this->style) ? 'style="' . esc_html($this->style) . '"' : '';
        $disabled = disabled(!current_user_can('edit_roles'), true, false);
        $checked  = checked($this->value, true, false);

        ob_start();
        ?>
        <div <?= $style ?>>
            <input type="hidden" id="<?= esc_html($this->id) ?>_reset" <?= $name ?> value="false"/>
            <input type="checkbox" id="<?= esc_html($this->id) ?>" <?= $name ?> value="true" <?= $class ?> <?= $checked ?> <?= $disabled ?>/>
            <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?></label>
        </div>
        <?php
        return trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));
    }

    /**
     * @return string the filter for this field as HTML object.
     */
    public function getFilterRow()
    {
        ob_start();
        ?>
        <select id="<?= esc_html($this->id) ?>" name="<?= esc_html($this->name) ?>" title="<?= esc_html($this->title) ?>">
            <option value="false">Not Checked</option>
            <option value="true">Checked</option>
        </select>
        <?php
        return $this->getFilterRowBase(ob_get_clean());
    }

    /**
     * @return Message[]|bool array of errors or true if no errors.
     */
    public function isValid()
    {
        return true;
    }

    /**
     * @param string|array|User|mixed $value
     */
    public function setValue($value)
    {
        parent::setValue($value);
        $new_value = filter_var($this->value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($new_value !== null) {
            $this->value = $new_value;
        }
    }

    /**
     * @param User $user
     */
    public function saveValue($user)
    {
        if ($this->value) {
            $user->add_role($this->name);
        } else {
            $user->remove_role($this->name);
        }
    }
}
