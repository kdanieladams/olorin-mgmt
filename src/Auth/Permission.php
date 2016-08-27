<?php

namespace Olorin\Auth;

use Illuminate\Database\Eloquent\Model;
use Olorin\Auth\Role;
use Olorin\Mgmt\MgmtModel;

class Permission extends MgmtModel
{
    protected $fillable = [
        'name', 'label'
    ];

    protected $hidden = [
        'created_at'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getMgmtFieldsAttribute()
    {
        parent::getMgmtFieldsAttribute();

        if($this->isFresh) {
            $this->setListFields("name", 'label', 'created_at');
        }

        return $this->mgmt_fields;
    }
}
