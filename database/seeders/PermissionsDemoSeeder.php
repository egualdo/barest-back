<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create([ 'name' => 'edit articles' ]);
        Permission::create([ 'name' => 'delete articles' ]);
        Permission::create([ 'name' => 'publish articles' ]);
        Permission::create([ 'name' => 'unpublish articles' ]);        
        
        Permission::create([ 'name' => 'publishers management', 'guard_name' => 'admin_api' ]);           
        Permission::create([ 'name' => 'publication reports management', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'requests management', 'guard_name' => 'admin_api' ]);           
        Permission::create([ 'name' => 'users management', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'styles management', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'plans management', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'admins management', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'policies management', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'total access', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'review reports management', 'guard_name' => 'admin_api' ]);
        Permission::create([ 'name' => 'user reports management', 'guard_name' => 'admin_api' ]);
        
        // create roles and assign existing permissions
        $role1 = Role::create([ 'name' => 'user']);
        $role1->givePermissionTo('edit articles');
        $role1->givePermissionTo('delete articles');

        $role2 = Role::create([ 'name' => 'company' ]);
        $role2->givePermissionTo('publish articles');
        $role2->givePermissionTo('unpublish articles');

        $role3 = Role::create([ 'name' => 'admin', 'guard_name' => 'admin_api' ]);
        // gets all permissions via Gate::before rule; see AuthServiceProvider
        
    }
}