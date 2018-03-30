<?php namespace Olorin\Support;

use DOMDocument;
use Exception;
use Session;

class FormGroupField {
    public $name        = '';
    public $label       = '';
    public $cssClass    = 'form-control';
    public $value       = null;
    public $selected    = null;
    public $addLabel    = true;
    public $disabled    = false;
    public $multiple    = false;
    public $attributes  = [];

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

            if(gettype($errors) == 'object' && get_class($errors) == 'Illuminate\Support\ViewErrorBag'){
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
        $fieldStr = str_replace(["disabled", "\r\n", '&nbsp;'], ["disabled=\"\"", "", ' '], $fieldStr);
        $element = $this->html->createDocumentFragment();
        try {
            $element->appendXML($fieldStr);
        }
        catch(\Exception $ex) {
            dd($ex->getMessage(), $fieldStr, $element);
        }


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