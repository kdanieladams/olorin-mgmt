<?php

namespace Olorin\Auth;

use Olorin\Mgmt\MgmtModel;
use Olorin\Auth\Permission;

class Role extends MgmtModel
{
    protected $fillable = ['name', 'label'];
    protected $mgmt_relations = ['permissions' => ['hasMany', 'Olorin\Auth\Permission']];

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
