<?php namespace Olorin\Support;

use DOMDocument;
use Session;
use Form;
use Exception;

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

                $selectStr .= ucwords(str_replace('_', ' ', $lbl));
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

class FormGroupField {
    public $name        = '';
    public $label       = '';
    public $cssClass    = 'form-control';
    public $value       = null;
    public $selected    = null;
    public $addLabel    = true;
    public $disabled    = false;
    public $multiple    = false;
    public $attributes  = array();

    private $html       = null;
    private $container  = null;
    private $errors     = null;
    private $validate   = true;

    /**
     * Create a new FormGroupField object.
     *
     * @param $name
     * @param $options
     * @throws Exception
     */
    public function __construct($name, $options = array())
    {
        if (!is_string($name) || strlen($name) < 2) {
            throw new Exception("FormGroup: First parameter \$name is a required string.\n\n");
        }

        if (!is_array($options)) {
            throw new Exception("FormGroup: Second parameter \$options must be an array or undefined.\n\n");
        }

        if (count($options)) {
            foreach ($options as $k => $opt) {
                if ($k === 'validate') {
                    if(is_bool($opt)) $this->validate = $opt;
                }
                else if (property_exists($this, $k)) {
                    $this->$k = $opt;
                }
                else {
                    switch ($k) {
                        case 'default':
                            $this->value = $opt;
                            break;
                        case 'class':
                            $this->cssClass = $opt;
                            break;
                    }
                }
            }
        }

        $this->name = $name;
        $this->html = new DOMDocument;

        $this->addContainer();

        if($this->addLabel) $this->addLabel();

        $this->addErrorHandling();

        $this->resolveAttributes();
    }

    public function getLabel(){
        return strlen($this->label) > 0 ? $this->label : str_replace('_', ' ', $this->name);
    }

    /**
     * Return the field's DOMDocument HTML.
     *
     * @return string
     */
    public function output()
    {
        return $this->html->saveHTML();
    }

    /**
     * Check if the Session has any errors for this field.
     *
     * @return bool
     */
    public function hasError()
    {
        if($this->validate && Session::has('errors')) {
            $errors = Session::get('errors');

            if(gettype($errors) == 'object' && get_class($errors) == 'Illuminate/Support/ViewErrorBag'){
                $errorBag = $errors->getBag('default');

                if ($errorBag->has($this->name)) {
                    $this->errors = $errorBag->get($this->name);
                    return true;
                }
            }
            else if(in_array($this->name, array_keys($errors))) {
                $this->errors = count($errors[$this->name]) > 1 ? $errors[$this->name] : array($errors[$this->name]);
                return true;
            }
        }

        return false;
    }

    /**
     * Add a field string to this field's DOMDocument.
     *
     * @param $fieldStr
     */
    public function addInput($fieldStr)
    {
        $fieldStr = html_entity_decode($fieldStr);
        $element = $this->html->createDocumentFragment();
        $element->appendXML($fieldStr);

        $this->container->appendChild($element);
    }

    /**
     * Add the form-group div to the field's DOMDocument.
     *
     */
    private function addContainer()
    {
        if($this->container === null) {
            $this->container = $this->html->createElement('div');
            $this->container->setAttribute('class', 'form-group');

            $this->html->appendChild($this->container);
        }
    }

    /**
     * Add the label element to the field's DOMDocument.
     *
     */
    private function addLabel()
    {
        $labelElm = $this->html->createElement('label');
        $labelElm->setAttribute('for', $this->name);

        $label = $this->getLabel();

        $label = ucwords($label) . '&#58;&nbsp;&nbsp;';

        $labelElm->nodeValue = $label;

        $this->container->appendChild($labelElm);
    }

    /**
     * Display any errors on the field.
     *
     */
    private function addErrorHandling()
    {
        if($this->hasError()) {
            $this->cssClass = $this->cssClass . ' error';

            foreach($this->errors as $error){
                $errorElm = $this->html->createElement('label');
                $errorElm->setAttribute('class', 'label label-danger');
                $errorElm->nodeValue = $error;

                $this->container->appendChild($errorElm);
            }
        }
    }

    /**
     * Resolve general HTML attributes for this form field.
     *
     */
    private function resolveAttributes()
    {
        $this->attributes = [
            'class' => $this->cssClass,
            'id' => $this->name
        ];

        if($this->multiple) {
            $this->attributes['multiple'] = true;
        }

        if($this->disabled){
            $this->attributes['disabled'] = true;
        }
    }
}