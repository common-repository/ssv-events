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
class ImageInputField extends InputField
{
    const INPUT_TYPE = 'image';

    /** @var string $preview */
    public $preview;
    /** @var array $required */
    public $required;

    /**
     * ImageInputField constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $name
     * @param bool   $preview
     * @param string $required
     * @param string $class
     * @param string $style
     * @param string $overrideRight
     */
    protected function __construct($id, $title, $name, $preview, $required, $class, $style, $overrideRight)
    {
        parent::__construct($id, $title, self::INPUT_TYPE, $name, $class, $style, $overrideRight);
        $this->required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
        $this->preview  = $preview;
    }

    /**
     * @param string $json
     *
     * @return ImageInputField
     * @throws Exception
     */
    public static function fromJSON($json)
    {
        $values = json_decode($json);
        if ($values->input_type != self::INPUT_TYPE) {
            throw new Exception('Incorrect input type');
        }
        return new ImageInputField(
            $values->id,
            $values->title,
            $values->name,
            $values->preview,
            $values->required,
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
            'preview'        => $this->preview,
            'required'       => $this->required,
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
        $required = $this->required && !empty($this->value) ? 'required="required"' : '';

        if (isset($overrideRight) && current_user_can($overrideRight)) {
            $required = '';
        }

        ob_start();
        if (current_theme_supports('materialize')) {
            ?>
            <div style="padding-top: 10px;">
                <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?><?= $this->required ? '*' : '' ?></label><br/>
                <?php if ($this->preview): ?>
                    <img src="<?= esc_url($this->value) ?>" <?= $class ?> <?= $style ?>/>
                <?php endif; ?>
                <div class="file-field input-field">
                    <div class="btn">
                        <span>Image</span>
                        <input type="file" id="<?= esc_html($this->id) ?>" <?= $name ?> <?= $class ?> <?= $style ?> <?= $required ?>>
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" title="<?= esc_html($this->title) ?>">
                    </div>
                </div>
            </div>
            <?php
        } else {
            ?>
            <label for="<?= esc_html($this->id) ?>"><?= esc_html($this->title) ?><?= $this->required ? '*' : '' ?></label><br/>
            <?php if ($this->preview): ?>
                <img src="<?= esc_url($this->value) ?>" <?= $class ?> <?= $style ?>/>
            <?php endif; ?>
            <input type="file" id="<?= esc_html($this->id) ?>" <?= $name ?> <?= $class ?> <?= $style ?> <?= $required ?>><br/>
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
            <option value="0">No Image</option>
            <option value="1">Has Image</option>
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
        if ($this->required && empty($this->value)) {
            $errors[] = new Message($this->title . ' is required but not set.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
        } elseif (!empty($this->value) && !mp_ssv_starts_with($this->value, SSV_General::BASE_URL)) {
            $errors[] = new Message($this->title . ' has an incorrect url.', current_user_can($this->overrideRight) ? Message::SOFT_ERROR_MESSAGE : Message::ERROR_MESSAGE);
        }
        return empty($errors) ? true : $errors;
    }
}
