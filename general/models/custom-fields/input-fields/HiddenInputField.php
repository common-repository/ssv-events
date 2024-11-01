<?php

namespace mp_ssv_general\custom_fields\input_fields;

use DateTime;
use Exception;
use mp_ssv_general\custom_fields\InputField;
use mp_ssv_general\Message;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 10-1-17
 * Time: 12:03
 */
class HiddenInputField extends InputField
{
    const INPUT_TYPE = 'hidden';

    /** @var string $defaultValue */
    public $defaultValue;

    /**
     * HiddenInputField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $inputType
     * @param string $name
     * @param string $defaultValue
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $inputType, $name, $defaultValue, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, $inputType, $name, $class, $style, $overrideRight);
        $this->defaultValue = $defaultValue;
        if ($this->defaultValue == 'NOW') {
            $this->value = (new DateTime('NOW'))->format('Y-m-d');
        } else {
            $this->value = $this->defaultValue;
        }
    }

    /**
     * @param string $json
     *
     * @return HiddenInputField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        return new HiddenInputField(
            $values->id,
            $values->title,
            $values->input_type,
            $values->name,
            $values->default_value,
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
            'default_value'  => $this->defaultValue,
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
        if (strtolower($this->defaultValue) == 'now') {
            $this->defaultValue = (new DateTime('NOW'))->format('Y-m-d');
        }
        $name  = 'name="' . esc_html($this->name) . '"';
        $value = 'value="' . esc_html($this->defaultValue) . '"';
        $class = !empty($this->class) ? 'class="' . esc_html($this->class) . '"' : '';
        $style = !empty($this->style) ? 'style="' . esc_html($this->style) . '"' : '';

        ob_start();
        ?><input type="hidden" <?= $name ?> <?= $value ?> <?= $class ?> <?= $style ?> /><?php
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
        return true;
    }
}
