<?php

namespace Olorin\Auth;

use Olorin\Mgmt\MgmtModel;
use Olorin\Auth\Permission;

class Role extends MgmtModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'label'];

    /**
     * Array of relationship class definitions for Mgmt.
     *
     * @var array
     */
    protected $mgmt_relations = ['permissions' => ['belongsToMany', 'Olorin\Auth\Permission']];

    /**
     * Olorin\Auth\Permission::$name required to create a User.
     *
     * @var string
     */
    public $create_permission = "create_roles";

    /**
     * Specify many-to-many relationship with App\Permission::class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Attach a permission to this role.
     *
     * @param $permission
     * @return Model
     */
    public function grantPermission($permission)
    {
        if(is_object($permission) && is_a($permission, '\Olorin\Auth\Permission')) {
            return $this->permissions()->save($permission);
        }

        if(is_string($permission)){
            $prm = Permission::where('name', $permission)->firstOrFail();
            return $this->permissions()->save($prm);
        }
    }

    /**
     * Label attribute accessor.  Returns an empty string if not assigned.
     *
     * @return string
     */
    public function getLabelAttribute()
    {
        return isset($this->attributes['label']) ? $this->attributes['label'] : '';
    }

    /**
     * Define some properties for displaying this model's fields
     * in the MGMT editor.
     *
     * @return array|mixed
     */
    public function getMgmtFieldsAttribute()
    {
        // run the base-model's method first to populate the $mgmt_fields array
        parent::getMgmtFieldsAttribute();

        // we only need to modify if the model is fresh
        if($this->isFresh){
            $this->setListFields("name", "label");
            $this->mgmt_fields['permissions']->view_options = ['checkboxes' => true];
            $this->mgmt_fields['permissions']->permissions = ['edit_permissions'];
        }

        return $this->mgmt_fields;
    }
}
