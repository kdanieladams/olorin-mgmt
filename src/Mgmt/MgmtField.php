<?php
namespace Olorin\Mgmt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Olorin\Mgmt\MgmtException;
use View;

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
        'add-one',
        'boolean',
        'date',
        'datetime',
        'email',
        'image',
        'integer',
        'password',
        'related',
        'text',
        'textarea',
        'textarea-html'
    ];

    // properties
    private $editable       = true;
    private $image_options  = [
        'upload' => false,
        'dir' => '/img',
        'path' => null,
        'preview' => true,
        'options' => []
    ];
    private $label          = '';
    private $limit          = 0;
    private $list           = false;
    private $name           = '';
    private $permissions    = [];
    private $required       = null;
    private $sidebar        = null;
    private $type           = 'text';
    private $validation     = null;
    private $view           = null;
    private $view_options   = ["labels" => ["true" => "True", "false" => "False"]];

    // relationship properties
    private $belongsTo      = null;
    private $belongsToMany  = null;
    private $hasMany        = null;
    private $related        = false;
    private $relationship   = '';

    public function __construct($name, $type, $options = array()){
        if($this->setName($name) && $this->setType($type)){
            // special type defaults
            if($this->type == 'related') {
                $this->related = true;
                $this->sidebar = true;
            }
            if($this->type == 'image') {
                $this->view_options['image_options'] = $this->image_options;
            }
            if($this->type == 'boolean') {
                $this->view_options['labels'] = [
                    'true' => 'true',
                    'false' => 'false'
                ];
            }

            // set property values according to given options
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

            throw new MgmtException("Unable to " . $msg . ".");
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
            $this->relationship = 'hasMany';
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

    public function setPermissions($value)
    {
        $go = false;

        if(is_array($value)) {
            if(is_array($value)){
                foreach($value as $k => $perm) {
                    if(\Olorin\Auth\Permission::where('name', $perm)->first() == null) {
                        $go = false;
                    }
                    else {
                        $go = true;
                    }
                }
            }

            if($go) {
                $this->permissions = $value;
                return true;
            }
        }

        return $go;
    }

    public function setImageOptions($value)
    {
        if($this->type == 'image' && is_array($value)) {
            foreach($value as $opt => $val) {
                if(in_array($opt, $this->image_options) && gettype($val) == gettype($this->image_options[$opt])) {
                    if($opt == 'dir') {
                        $path = base_path() . '/public/' . ltrim($val, "/");

                        if(is_dir($path)) {
                            $this->image_options['path'] = $path;
                        }
                        else {
                            throw new MgmtException("Unable to resolve public directory given for image field.", 1);
                        }

                        $dir = scandir($path);
                        $options = array();

                        foreach($dir as $k => $filename) {
                            if($filename == "." || $filename == "..") {
                                continue;
                            }

                            $opt_val = rtrim($val, "/") . "/" . $filename;

                            if(is_dir(ltrim($opt_val, "/"))) {
                                // TODO: Include support for subdirectories.
                                continue;
                            }

                            $options[$opt_val] = $filename;
                        }

                        $this->image_options["options"] = $options;
                    }

                    $this->image_options[$opt] = $val;
                }
            }

            $this->image_options['path'] = base_path() . '/public/' . ltrim($this->image_options['dir'], "/");

            $this->view_options['image_options'] = $this->image_options;
        }
    }

    public function setView($value)
    {
        if(View::exists($value)) {
            $this->view = $value;
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
            return 0;
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

        if($value == null || (is_a($value, 'Illuminate\Database\Eloquent\Collection') && count($value) == 0)) {
            // throw new MgmtException("Unable to query " . $this->getClassName() . " relationship on " . $fieldname . "!");
            $relationship = $this->relationship;
            $class = $this->$relationship;
            $value = new $class();

        }

        if($value instanceof Collection){
            $value = $value->first();
        }

        $label_key = $value->label_key;

        if(!isset($value->$label_key)){
            if(isset($value->title)) {
                $label_key = 'title';
            }
            elseif(isset($value->name)) {
                $label_key = 'name';
            }
            else {
                //dd($instance->$fieldname, $fieldname, $value, $value->{$value->label_key}, $value->name);
                throw new MgmtException("resolveRelatedFields(): Unable to determine related field identifier.", 1);
            }
        }

        return $label_key;
    }

    public function getRelatedOptions(Model $instance)
    {
        if($this->related) {
            $relationship = $this->relationship;
            $class = $relationship ? $this->$relationship : '';
            $items = array();
            $label_key = $this->getLabelKey($instance);

            if(!class_exists($class)) {
                throw new MgmtException('Mgmt was unable to resolve a related classname!', 1);
            }

            foreach($class::all() as $item){
                if(empty($item->label)) {
                    $items[$item->id] = $item->$label_key;
                }
                else {
                    $items[$item->id] = $item->label;
                }
            }

            return $items;
        }

        throw new MgmtException('Mgmt was unable to resolve a relationship!', 1);
    }

    public function getRelatedItems(Model $instance)
    {
        if($this->related) {
            $relationship = $this->relationship;
            $class = $relationship ? $this->$relationship : '';
            $items = array();

            if(!class_exists($class)) {
                throw new MgmtException('Mgmt was unable to resolve a related classname!', 1);
            }

            foreach($instance->{$this->name} as $item){
                $items[$item->id] = $item;
            }

            return $items;
        }

        throw new MgmtException('Mgmt was unable to resolve a relationship!', 1);
    }
}