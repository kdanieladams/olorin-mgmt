<?php namespace Olorin\Support;

use Form;

class FormGroup {
    /**
     * Create a new form-group with a text field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function text($name, $options = array())
    {
        $field = new FormGroupField($name, $options);
        $textStr = Form::text($field->name, $field->value, $field->attributes) . '</input>';

        $field->addInput($textStr);

        return $field->output();
    }

    /**
     * Create a new form-group with a textarea inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function textarea($name, $options = array())
    {
        $field = new FormGroupField($name, $options);
        $textareaStr = Form::textarea($field->name, $field->value, $field->attributes);

        $field->addInput($textareaStr);

        return $field->output();
    }

    /**
     * Create a new form-group with a multi-select field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function multiselect($name, $options = array())
    {
        $options['multiple'] = true;
        $selectStr = '';
        $field = new FormGroupField($name, $options);

        if(isset($options['checkboxes']) && $options['checkboxes']){
            foreach($field->value as $val => $lbl){
                $selectStr .= "<div class=\"checkbox\">";
                $selectStr .= "<label>";

                if(!is_null($field->selected) && in_array($val, $field->selected)) {
                    $selectStr .= Form::checkbox($name . '[]', $val,
                            true, array_merge($field->attributes, ['class' => ''])) . '</input>';
                }
                else {
                    $selectStr .= Form::checkbox($name . '[]', $val,
                            false, array_merge($field->attributes, ['class' => ''])) . '</input>';
                }

                $selectStr .= str_replace('_', ' ', $lbl);
                $selectStr .= "</label>";
                $selectStr .= "</div>";
            }
        }
        else {
            $selectStr = Form::select($field->name . '[]', $field->value, $field->selected, $field->attributes);
        }

        $field->addInput($selectStr);

        return $field->output();
    }

    /**
     * Create a new form-group with an HTML5 date field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function date($name, $options = array())
    {
        $field = new FormGroupField($name, $options);
        $inputStr = Form::input('date', $field->name, $field->value, $field->attributes) . '</input>';

        $field->addInput($inputStr);

        return $field->output();
    }

    /**
     * Create a new form-group with an HTML5 datetime-local field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function datetime($name, $options = array())
    {
        $field = new FormGroupField($name, $options);
        $inputStr = Form::input('datetime-local', $field->name, $field->value, $field->attributes) . '</input>';

        $field->addInput($inputStr);

        return $field->output();
    }

    /**
     * Create new form-group with an HTML5 email field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function email($name, $options = array())
    {
        $field = new FormGroupField($name, $options);
        $inputStr = Form::email($field->name, $field->value, $field->attributes) . '</input>';

        $field->addInput($inputStr);

        return $field->output();
    }

    /**
     * Create new form-group with a password field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function password($name, $options = array())
    {
        $field = new FormGroupField($name, $options);
        $inputStr = Form::password($field->name, $field->attributes) . '</input>';

        $field->addInput($inputStr);

        return $field->output();
    }

    /**
     * Create new form-group with a select field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function select($name, $options = array())
    {
        $field = new FormGroupField($name, $options);

        $field->attributes = array_merge($field->attributes, ['autocomplete' => 'off']);

        $inputStr = Form::select($field->name, $field->value, $field->selected, $field->attributes);

        $field->addInput($inputStr);

        return $field->output();
    }

    /**
     * Create new form-group with a file field inside.
     *
     * @param $name
     * @param array $options
     * @return string
     */
    public static function file($name, $options = array())
    {
        $field = new FormGroupField($name, $options);
        $inputStr = Form::file($field->name, $field->attributes) . '</input>';

        $field->addInput($inputStr);

        return $field->output();
    }

    /**
     * Create new form-group with a checkbox inside.
     *
     * @param $name
     * @param $options
     * @return string
     */
    public static function checkbox($name, $options)
    {
        $options['addLabel'] = false;
        $field = new FormGroupField($name, $options);
        $inputStr = "<div class='checkbox'><label for='{$field->name}'>".
            Form::checkbox($field->name, 1, $field->value) . '</input>'
            ."{$field->label}</label></div>";

        $field->addInput($inputStr);

        return $field->output();
    }
}
