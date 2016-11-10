<?php

namespace Olorin\Mgmt;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

/**
 * Class MgmtModel
 * ===============
 * Inherit from this base-class to use MGMT's administration features on your model.  You will also
 * need to define any related models using a protected $mgmt_relations property.  If you wish to
 * customize any fields, you can do so by overriding the getMgmtFieldsAttribute() method.
 * Don't forget to read the docs! http://www.olorin.io/laravel/mgmt/docs
 *
 * @package Olorin\Mgmt
 */
class MgmtModel extends Model
{
    protected $isFresh = false;     // Is this model's $mgmt_fields attribute currently being instantiated?
    public $create_permission = ''; // Olorin\Auth\Permission::$name required to create an instance of this model.

    /**
     * Returns this models $mgmt_fields property.  If the property
     * has not yet been populated, calls getFieldData() to do so.
     *
     * @return array
     */
    public function getMgmtFieldsAttribute()
    {
        if(empty($this->mgmt_fields)) {
            $this->isFresh = true;
            $this->mgmt_fields = array();
            $this->getFieldData();
        }

        return $this->mgmt_fields;
    }

    /**
     * Determines if this model has related fields or not.
     *
     * @return bool
     */
    public function hasSidebar()
    {
        foreach($this->mgmt_fields as $field){
            if($field->related){
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an array of validation strings for Laravel's
     * built in validator.
     *
     * @return array
     */
    public function getValidationRules()
    {
        $fields = $this->mgmt_fields;
        $rules = array();

        // define validation rules
        foreach($fields as $k => $field){
            $ruleString = '';

            if(!empty($field->validation) && is_string($field->validation)) {
                $rules[$field->name] = $field->validation;
                continue;
            }

            if(($field->list && $field->required !== false) || $field->required === true){
                $ruleString .= '|required';
            }

            if($field->limit > 0){
                $ruleString .= '|max:' . $field->limit;
            }

            switch($field->type){
                case 'text':
                case 'textarea':
                case 'textarea-html':
                    $ruleString .= "|string";

                    if(strpos($ruleString, "required")){
                        $ruleString .= "|min:3";
                    }
                    break;
                case 'datetime':
                    $ruleString .= "|date";
                    break;
                case 'email':
                    $ruleString .= "|email";
                    break;
                case 'password':
                    $ruleString .= "|same:" . $field->name . "_confirm";
                    break;
            }

            // TODO: Validate many-to-many attachments...
            // many-to-many validation depends on how many items were selected to be attached...thanks Laravel...
            if($field->related && $field->relationship != 'belongsToMany' && $field->relationship != 'hasMany'){
                $relationship = $field->relationship;
                $class = $field->$relationship;

                $ruleString .= "|in:";

                foreach($class::all() as $item){
                    $ruleString .= $item->id . ",";
                }

                $ruleString = rtrim($ruleString, ',');
            }

            $ruleString = ltrim($ruleString, '|');

            $rules[$field->name] = $ruleString;
        }

        return $rules;
    }

    /**
     * Takes an input array from a form submission, then translates the input
     * to the model using the MgmtField declarations.
     *
     * @param array $input
     */
    public function translateInput(array $input) {
        $fields = $this->mgmt_fields;
        
        // input translations by field
        foreach($fields as $mgmt_field){
            if($mgmt_field->editable){
                $fieldname = $mgmt_field->name;

                if($mgmt_field->required == false && empty($input[$fieldname])) {
                    if($mgmt_field->related == true) {
                        if($mgmt_field->relationship == 'belongsTo') {
                            $this->$fieldname()->dissociate();
                        }
                        elseif($mgmt_field->relationship == 'belongsToMany') {
                            $this->$fieldname()->detach();
                        }
                    }
                    continue;
                }

                switch($mgmt_field->type){
                    case 'date':
                    case 'datetime':
                        $this->$fieldname = new Carbon($input[$fieldname]);
                        break;
                    case 'related':
                        if($this->id == null){
                            $this->save();
                        }
                        switch($mgmt_field->relationship){
                            case 'belongsTo':
                                $this->$fieldname()->associate($input[$fieldname]);
                                break;
                            case 'belongsToMany':
                                $this->$fieldname()->detach();
                                $this->$fieldname()->attach($input[$fieldname]);
                                break;
                            default:
                                dd("You've come across a relationship that isn't handled yet", $mgmt_field, $input);
                                break;
                        }
                        break;
                    default:
                        $this->$fieldname = $input[$fieldname];
                        break;
                }

                unset($this->attributes['mgmt_fields']);
            }
        }
    }

    public function getUrlFriendlyName()
    {
        $reflect = new \ReflectionClass($this);
        $global_ref = $reflect->getName();
        $global_ref = str_replace("\\", "-", $global_ref);
        $global_ref = str_replace("App-", "", $global_ref);

        return $global_ref;
    }

    /**
     * Populates this models $mgmt_fields attributes with instances
     * of App\Mgmt\MgmtField.
     */
    private function getFieldData()
    {
        // Table column names
        $table = $this->getTable();

        foreach(DB::select("SHOW COLUMNS FROM " . $table) as $table_row) {
            $field_name = $table_row->Field;
            $type = preg_replace("/\(\d+\)/i", "", $table_row->Type);
            $limit = intval(preg_replace("/[a-z\(\)]+/i", "", $table_row->Type));
            $options = array();

            switch($type) {
                case "varchar":
                    $type = "text";
                    break;
                case "text":
                    $type = "textarea";
                    break;
                case "timestamp":
                    $type = "datetime";
                    break;
                case "int unsigned":
                    $type = "integer";
                    break;
            }

            if(!empty($limit)) {
                $options["limit"] = $limit;
            }

            if(in_array($field_name, $this->fillable) && !in_array($field_name, $this->hidden)) {
                $this->mgmt_fields[$field_name] = new MgmtField($field_name, $type, $options);
            }
            else if(in_array($field_name, $this->hidden)) {
                $options['hidden'] = true;

                // TODO: Handle hidden fields on model...
            }
        }

        // Eloquent relationships
        // NOTE: $mgmt_relations property required to resolve related classes, e.g.:
        //      protected $mgmt_relations = array(
        //          'property_name' => array(
        //              'belongsTo',
        //              'App\RelatedModel'
        //          )
        //      );
        if(!empty($this->mgmt_relations)){
            foreach($this->mgmt_relations as $key => $relation) {
                if(count($relation) !== 2 || !is_string($relation[0]) || !is_string($relation[1])){
                    throw new \Exception("MgmtModel->getFieldData(): Invalid relationship!");
                }

                $this->mgmt_fields[$key] = new MgmtField($key, 'related', [
                    $relation[0] => $relation[1]
                ]);
            }
        }
    }

    /**
     * Accepts multiple field_names, which it will set as fields to display
     * in Mgmt's list function, provided mgmt_field's exist for each given name.
     *
     * @param string $field_name (+)
     */
    protected function setListFields()
    {
        foreach(func_get_args() as $name) {
            if(is_string($name) && array_key_exists($name, $this->mgmt_fields)){
                $this->mgmt_fields[$name]->list = true;
            }
        }
    }

    /**
     * Accepts an array of $field_name => $label, then sets the label
     * attribute on the relevant field names.
     *
     * @param array $labels
     */
    protected function setFieldLabels(array $labels)
    {
        foreach($labels as $name => $label) {
            if(array_key_exists($name, $this->mgmt_fields)){
                $this->mgmt_fields[$name]->label = $label;
            }
        }
    }
}
