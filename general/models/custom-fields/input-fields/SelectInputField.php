<?php

namespace mp_ssv_general\custom_fields\input_fields;

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
class SelectInputField extends InputField
{
    const INPUT_TYPE = 'select';

    /** @var bool $disabled */
    public $disabled;
    /** @var array $options */
    public $options;

    /**
     * SelectInputField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $name
     * @param bool   $disabled
     * @param string $options
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $name, $disabled, $options, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, self::INPUT_TYPE, $name, $class, $style, $overrideRight);
        $this->disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
        $this->options  = explode(',', $options);
    }

    /**
     * @param string $json
     *
     * @return SelectInputField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        return new SelectInputField(
            $values->id,
            $values->title,
            $values->name,
            $values->disabled,
            $values->options,
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
            'options'        => implode(',', $this->options),
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
        $class    = !empty($this->class) ? 'class="' . esc_html($this->class) . '"' : 'class="validate"';
        $style    = !empty($this->style) ? 'style="' . esc_html($this->style) . '"' : '';
        $disabled = disabled($this->disabled, true, false);

        if (isset($overrideRight) && current_user_can($overrideRight)) {
            $disabled = '';
        }

        ob_start();
        if (current_theme_supports('materialize')) {
            ?>
            <div class="input-field">
                <select id="<?= esc_html($this->id) ?>" <?= $name ?> <?= $class ?> <?= $style ?> <?= $disabled ?>>
                    <?php foreach ($this->options as $option): ?>
                        <option value="<?= $option ?>" <?= selected($option, $this->value) ?>><?= $option ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?></label>
            </div>
            <?php
        } else {
            ?>
            <div class="input-field">
                <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?></label><br/>
                <select id="<?= esc_html($this->id) ?>" <?= $name ?> <?= $class ?> <?= $style ?> <?= $disabled ?>>
                    <?php foreach ($this->options as $option): ?>
                        <option value="<?= $option ?>" <?= selected($option, $this->value) ?>><?= $option ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php
        }

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
            <?php foreach ($this->options as $option): ?>
                <option value="<?= esc_html($option) ?>"><?= esc_html($option) ?></option>
            <?php endforeach; ?>
        </select>
        <?php
        return $this->getFilterRowBase(ob_get_clean());
    }

    /**
     * @return Message[]|bool array of errors or true if no errors.
     */
    public function isValid()
    {
        $errors = array();
        if (!$this->disabled && (empty($this->value) || !in_array($this->value, $this->options))) {
            $errors[] = new Message('The value ' . $this->value . ' is not one of the options.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
        }
        return empty($errors) ? true : $errors;
    }
}
