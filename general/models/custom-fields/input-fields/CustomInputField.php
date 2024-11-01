<?php

namespace mp_ssv_general\custom_fields\input_fields;

use Exception;
use mp_ssv_general\custom_fields\InputField;
use mp_ssv_general\Message;
use mp_ssv_general\SSV_General;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 10-1-17
 * Time: 12:03
 */
class CustomInputField extends InputField
{
    /** @var bool $disabled */
    public $disabled;
    /** @var array $required */
    public $required;
    /** @var string $defaultValue */
    public $defaultValue;
    /** @var string $placeholder */
    public $placeholder;

    /**
     * CustomInputField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $inputType
     * @param string $name
     * @param bool   $disabled
     * @param string $required
     * @param string $defaultValue
     * @param string $placeholder
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $inputType, $name, $disabled, $required, $defaultValue, $placeholder, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, $inputType, $name, $class, $style, $overrideRight);
        $this->disabled     = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
        $this->required     = filter_var($required, FILTER_VALIDATE_BOOLEAN);
        $this->defaultValue = $defaultValue;
        $this->placeholder  = $placeholder;
    }

    /**
     * @param string $json
     *
     * @return CustomInputField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        return new CustomInputField(
            $values->id,
            $values->title,
            $values->input_type,
            $values->name,
            $values->disabled,
            $values->required,
            $values->default_value,
            $values->placeholder,
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
            'disabled'       => $this->disabled,
            'required'       => $this->required,
            'default_value'  => $this->defaultValue,
            'placeholder'    => $this->placeholder,
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
        $value       = !empty($this->value) ? $this->value : $this->defaultValue;
        $inputType   = 'type="' . esc_html($this->inputType) . '"';
        $name        = 'name="' . esc_html($this->name) . '"';
        $class       = !empty($this->class) ? 'class="' . esc_html($this->class) . '"' : '';
        $style       = !empty($this->style) ? 'style="' . esc_html($this->style) . '"' : '';
        $placeholder = !empty($this->placeholder) ? 'placeholder="' . esc_html($this->placeholder) . '"' : '';
        $value       = !empty($value) ? 'value="' . esc_html($value) . '"' : '';
        $disabled    = disabled($this->disabled, true, false);
        $required    = $this->required ? 'required="required"' : '';

        if (isset($overrideRight) && current_user_can($overrideRight)) {
            $disabled = '';
            $required = '';
        }

        ob_start();
        ?>
        <div>
            <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?><?= $this->required ? '*' : '' ?></label>
            <input <?= $inputType ?> id="<?= esc_html($this->id) ?>" <?= $name ?> <?= $class ?> <?= $style ?> <?= $value ?> <?= $disabled ?> <?= $placeholder ?> <?= $required ?>/>
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
        ?><input id="<?= esc_html($this->id) ?>" type="text" name="<?= esc_html($this->name) ?>" title="<?= esc_html($this->title) ?>"/><?php
        return $this->getFilterRowBase(ob_get_clean());
    }

    /**
     * @return Message[]|bool array of errors or true if no errors.
     */
    public function isValid()
    {
        $errors = array();
        if (($this->required && !$this->disabled) && empty($this->value)) {
            $errors[] = new Message($this->title . ' field is required but not set.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
        }
        switch (strtolower($this->inputType)) {
            case 'iban':
                $this->value = str_replace(' ', '', strtoupper($this->value));
                if (!empty($this->value) && !SSV_General::isValidIBAN($this->value)) {
                    $errors[] = new Message($this->title . ' field is not a valid IBAN.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
                }
                break;
            case 'email':
                if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = new Message($this->title . ' field is not a valid email.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
                }
                break;
            case 'url':
                if (!filter_var($this->value, FILTER_VALIDATE_URL)) {
                    $errors[] = new Message($this->title . ' field is not a valid url.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
                }
                break;
        }
        return empty($errors) ? true : $errors;
    }
}
