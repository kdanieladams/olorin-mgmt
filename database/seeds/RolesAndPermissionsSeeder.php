<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();
        // Seed some roles
        DB::table('roles')->insert([
            [
                'name' => 'moderator',
                'label' => 'Moderator',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'administrator',
                'label' => 'Administrator',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Seed some permissions
        DB::table('permissions')->insert([
            [
                'name' => 'view_mgmt',
                'label' => 'Can View MGMT',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create_roles',
                'label' => 'Can create Roles',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit_permissions',
                'label' => 'Can edit Permissions',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create_users',
                'label' => 'Can create Users',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit_password',
                'label' => 'Can edit a User\'s Password',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);

        // Seed some relationships
        DB::table('permission_role')->insert([
            ['permission_id' => 1, 'role_id' => 1], // view_mgmt        -> mod
            ['permission_id' => 1, 'role_id' => 2], // view_mgmt        -> admin
            ['permission_id' => 2, 'role_id' => 2], // create_roles     -> admin
            ['permission_id' => 3, 'role_id' => 2], // edit_permissions -> admin
            ['permission_id' => 4, 'role_id' => 2], // create_users     -> admin
            ['permission_id' => 5, 'role_id' => 2]  // edit_password    -> admin
        ]);
    }
}
