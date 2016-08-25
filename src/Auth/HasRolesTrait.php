<?php

namespace Olorin\Auth;

use Olorin\Auth\Role, Olorin\Auth\Permission;

trait HasRolesTrait {

    /**
     * Resolve an instance of App\Role in case a name is given instead.
     *
     * @param $role
     * @return bool | Role
     */
    private function resolveRole($role) {
        if(is_string($role)) {
            return Role::whereName($role)->firstOrFail();
        }

        if(is_object($role) && is_a($role, 'Olorin\Auth\Role')) {
            return $role;
        }

        return false;
    }

    /**
     * Many-to-many relationship with App\Role.
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Assign a role to this model.
     *
     * @param string|Role $role
     * @return mixed
     */
    public function assignRole($role)
    {
        $roleObj = $this->resolveRole($role);

        return $this->roles()->save($roleObj);
    }

    /**
     * Unassign a role from this model.
     *
     * @param string|Role $role
     * @return mixed
     */
    public function unassignRole($role)
    {
        $roleObj = $this->resolveRole($role);

        return $this->roles()->detach($roleObj);
    }

    /**
     * Check if this model has a given role.
     *
     * @param string|Collection|Role $role
     * @return boolean
     */
    public function hasRole($role)
    {
        if(is_string($role)) {
            return !! $this->roles->contains('name', $role);
        }

        if(is_object($role)) {
            if(is_a($role, 'Illuminate\Support\Collection')) {
                return !! $role->intersect($this->roles)->count();
            }
            else if(is_a($role, 'Olorin\Auth\Role')) {
                return !! $this->roles->intersect([$role])->count();
            }
        }
    }

    /**
     * Get a collection of permissions granted to this model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionsAttribute()
    {
        $permissions = array();
        $roles = $this->roles()->with('permissions')->get();

        foreach($roles as $role) {
            foreach($role->permissions as $perm) {
                array_push($permissions, $perm);
            }
        }

        return collect($permissions);
    }

    /**
     * Check if this model has a given permission.
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $hasPerm = false;

        if(is_string($permission)) {
            $hasPerm = $this->checkPermString($permission);
        }
        else if (is_a($permission, '\Olorin\Auth\Permission')){
            $hasPerm = $this->checkPermObj($permission);
        }
        else if (is_array($permission)) {
            foreach($permission as $k => $perm) {
                if(is_a($perm, '\Olorin\Auth\Permission') && $this->checkPermObj($permission[$k])) {
                    $hasPerm = true;
                    break;
                }
                else if (is_string($perm) && $this->checkPermString($permission[$k])) {
                    $hasPerm = true;
                    break;
                }
            }
        }

        return $hasPerm;
    }

    /**
     * See if the given Permission is assigned to any of this model's roles.
     *
     * @param \Olorin\Auth\Permission $perm
     * @return bool
     */
    private function checkPermObj(Permission $perm)
    {
        foreach($this->permissions as $p2) {
            if($perm->name == $p2->name) {
                return true;
            }
        }

        return false;
    }

    /**
     * See if the given string matches any Permission->name in this model's
     * roles' permissions.
     *
     * @param $perm
     * @return bool
     */
    private function checkPermString($perm)
    {
        foreach($this->permissions as $p2) {
            if($perm == $p2->name) {
                return true;
            }
        }

        return false;
    }
}