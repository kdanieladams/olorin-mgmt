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
                            true, array_merge($field->attributes, ['class' => '', 'multiple' => ''])) . '</input>';
                }
                else {
                    $selectStr .= Form::checkbox($name . '[]', $val,
                            false, array_merge($field->attributes, ['class' => '', 'multiple' => ''])) . '</input>';
                }

                $selectStr .= str_replace('_', ' ', $lbl);
                $selectStr .= "</label>";
                $selectStr .= "</div>";
            }
        }
        else {
            $selectStr = Form::select($field->name . '[]', $field->value, $field->selected,
                array_merge($field->attributes, ['class' => '', 'multiple' => '']));
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
     * Create new form-group with an upload-file field inside.
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
     * Create new form-group with a set of checkboxes inside.
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

    /**
     * Create a new form-group with a number field inside.
     *
     * @param $name
     * @param $options
     * @return string
     */
    public static function number($name, $options)
    {
        $field = new FormGroupField($name, $options);
        $textStr = Form::number($field->name, $field->value, $field->attributes) . '</input>';

        $field->addInput($textStr);

        return $field->output();
    }

    /**
     * Create a new form-group with a pair of radio buttons for boolean assignment.
     *
     * @param $name
     * @param $options
     * @return string
     */
    public static function boolean($name, $options)
    {
        $options['labels']['true'] = $options['labels']['true'] ?: "True";
        $options['labels']['false'] = $options['labels']['false'] ?: "False";
        $options['cssClass'] = "";

        $field = new FormGroupField($name, $options);

        // insert a block element to abide strict XML-formatting standards of DOMDocument() while forcing a line-break.
        $inputStr = "<div></div>";

        $field->attributes["id"] = $field->name . "_true";

        $inputStr .= "<label class='radio-inline'>";
        $inputStr .= Form::radio($field->name, "1", ($field->value == 1), $field->attributes) . '</input>';
        $inputStr .= " " . $options['labels']['true'];
        $inputStr .= "</label>";

        $field->attributes["id"] = $field->name . "_false";

        $inputStr .= "<label class='radio-inline'>";
        $inputStr .= Form::radio($field->name, "0", ($field->value == 0), $field->attributes) . '</input>';
        $inputStr .= " " . $options['labels']['false'];
        $inputStr .= "</label>";

        $field->addInput($inputStr);

        return $field->output();
    }
}
