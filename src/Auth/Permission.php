<?php

namespace Olorin\Auth;

use Illuminate\Database\Eloquent\Model;
use Olorin\Auth\Role;
use Olorin\Mgmt\MgmtModel;

class Permission extends MgmtModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'label'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at'
    ];

    /**
     * Specify many-to-many relationship with App\Role::class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
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
        parent::getMgmtFieldsAttribute();

        if($this->isFresh) {
            $this->setListFields("name", 'label', 'created_at');
        }

        return $this->mgmt_fields;
    }
}
