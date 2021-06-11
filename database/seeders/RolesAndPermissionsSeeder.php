<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Сброс кэшированных ролей и разрешений
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        // create read update delete
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'read user']);
        Permission::create(['name' => 'update user']);

        Permission::create(['name' => 'create document template']);
        Permission::create(['name' => 'read document template']);
        Permission::create(['name' => 'update document template']);
        Permission::create(['name' => 'delete document template']);
        
        Permission::create(['name' => 'create document']);
        Permission::create(['name' => 'read document']);
        Permission::create(['name' => 'update document']);
        Permission::create(['name' => 'delete document']);

        Permission::create(['name' => 'read your document']);
        Permission::create(['name' => 'delete your document']);

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo([
            'create document',
            'read document',
            'read your document',
            'delete your document',
        ]);
    }
}