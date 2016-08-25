<?php
namespace Olorin\Mgmt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MgmtField
 * ===============
 * Field object that represents a set of field properties for MGMT models.
 *
 * @package App\Mgmt
 */
class MgmtField {
    /**
     * The typelist defines all available field types in Mgmt.
     *
     * @var $typelist array
     */
    private $typelist = [
        'text',
        'textarea',
        'textarea-html',
        'datetime',
        'integer',
        'related',
        'email'
    ];

    // properties
    private $editable       = true;
    private $required       = null;
    private $label          = '';
    private $limit          = 0;
    private $list           = false;
    private $name           = '';
    private $type           = 'text';
    private $sidebar        = null;
    private $view_options   = [];

    // relationship properties
    private $belongsTo      = null;
    private $belongsToMany  = null;
    private $hasMany        = null;
    private $related        = false;
    private $relationship   = '';

    public function __construct($name, $type, $options = array()){
        if($this->setName($name) && $this->setType($type)){
            if($this->type == 'related') {
                $this->related = true;
                $this->sidebar = true;
            }

            foreach($options as $opt => $value) {
                if(is_string($opt) && property_exists($this, $opt)) {
                    $setString = 'set ' . $opt;
                    $setString = camelCase($setString);

                    if(method_exists($this, $setString)) {
                        $this->$setString($value);
                    }
                }
            }
        }
        else {
            $msg = $this->setName($name) ? ($this->setType($type) ? '[unknown error]' :
                'setType(\'' . $type . '\')') : 'setName(\'' . $name . '\')';

            throw new \Exception("MgmtField: Unable to " . $msg . ".");
        }
    }

    public function __set($name, $value){
        $setString = 'set ' . $name;
        $setString = camelCase($setString);

        if(method_exists($this, $setString)){
            return $this->$setString($value);
        }

        return false;
    }

    public function __get($name){
        $getString = 'get ' . $name;
        $getString = camelCase($getString);

        if(method_exists($this, $getString)) {
            return $this->$getString();
        }
        elseif(property_exists($this, $name)) {
            return $this->$name;
        }

        return false;
    }

    public function setBelongsTo($value) {
        if($this->related
            && is_string($value)
            && (class_exists($value) || class_exists('\\' . $value)))
        {
            $this->belongsTo = $value;
            $this->relationship = 'belongsTo';
            return true;
        }
        return false;
    }

    public function setBelongsToMany($value) {
        if($this->related
            && is_string($value)
            && (class_exists($value) || class_exists('\\' . $value)))
        {
            $this->belongsToMany = $value;
            $this->relationship = 'belongsToMany';
            return true;
        }

        return false;
    }

    public function setHasMany($value){
        if($this->related
            && is_string($value)
            && (class_exists($value) || class_exists('\\' . $value)))
        {
            $this->hasMany = $value;
            // TEMP:
            $this->belongsToMany = $value;
            $this->relationship = 'belongsToMany';
        }
        return false;
    }

    public function setEditable($value) {
        if(is_bool($value)) {
            $this->editable = $value;
            return true;
        }

        return false;
    }

    public function setLabel($value) {
        if(is_string($value)) {
            $this->label = $value;
            return true;
        }

        return false;
    }

    public function setList($value){
        if(is_bool($value)){
            $this->list = $value;
            return true;
        }

        return false;
    }

    public function setLimit($value){
        if(is_int($value)) {
            $this->limit = $value;
            return true;
        }

        return false;
    }

    public function setName($value) {
        if(is_string($value)) {
            $this->name = strtolower($value);
            return true;
        }

        return false;
    }

    public function setType($value){
        if(is_string($value) && in_array($value, $this->typelist)){
            $this->type = strtolower($value);
            return true;
        }

        return false;
    }

    public function setSidebar($value){
        if(is_string($value)){
            $value = trim(strtolower($value)) === 'true' ? true : false;
        }

        if(is_bool($value)) {
            $this->sidebar = $value;
            return true;
        }

        return false;
    }

    public function setViewOptions($value){
        if(is_array($value)){
            $this->view_options = $value;
            return true;
        }

        return false;
    }

    public function getLabel(){
        if(!empty($this->label)) return $this->label;

        return ucwords(str_replace('_', ' ', $this->name));
    }

    public function getSidebar(){
        if($this->sidebar === null) {
            return false;
        }

        return $this->sidebar;
    }

    /**
     * Resolve related fields and model instances to a given item.
     *
     * @param Model $instance
     * @return false|int|array
     */
    public function getRelatedId(Model $instance)
    {
        if(!$this->related) return false;

        $prop_name = $this->name;
        $related_model = $instance->$prop_name;

        if($related_model instanceof Collection) {
            $ret = array();

            foreach($related_model as $item){
                $ret[] = $item->id;
            }

            return $ret;
        }
        elseif(empty($related_model->id)) {
            return 1;
        }

        return $related_model->id;
    }

    public function getClassName()
    {
        if(!$this->related) return false;

        $relationship = $this->relationship;

        return str_replace("App\\", "", $this->$relationship);
    }

    public function getLabelKey(Model $instance)
    {
        if(!$this->related) return false;

        $fieldname = $this->name;
        $value = $instance->$fieldname;
        $label_key = 'label';

        if($value instanceof Collection){
            if(count($value) > 0){
                $value = $value[0];
            }
            else {
                $relationship = $this->relationship;

                $class = $this->$relationship;
                $value = $class::find(1);
            }
        }

        if(empty($value->label)){
            if(!empty($value->title)) {
                $label_key = 'title';
            }
            elseif(!empty($value->name)) {
                $label_key = 'name';
            }
            else {
                //dd($instance->$fieldname, $fieldname, $value);
                //throw new \Exception("MgmtField->resolveRelatedFields: Unable to determine related field identifier.\n\n");
            }
        }

        return $label_key;
    }

    public function getRelatedItems(Model $instance)
    {
        $relationship = $this->relationship;
        $class = $this->$relationship;
        $items = array();

        if(!class_exists($class)) {
            dd('MgmtField->getRelatedItems: Unabled to resolve related classname!',
                $class, $relationship, $instance);
        }

        foreach($class::all() as $item){
            $label_key = $this->getLabelKey($instance);
            $items[$item->id] = ucwords($item->$label_key);
        }

        return $items;
    }
}