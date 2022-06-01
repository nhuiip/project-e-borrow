<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permission::create(['name' => 'edit articles']);
        // Permission::create(['name' => 'delete articles']);
        // Permission::create(['name' => 'publish articles']);
        // Permission::create(['name' => 'unpublish articles']);

        // Role::create(['name' => 'Admin']);

        $user = User::create([
            'name' => 'Admin',
            'roleId' => 1,
            'email' => 'admin@started.com',
            'password' => 'admin'
        ]);
        $user->assignRole('Admin');

        $permissions = Permission::all();
        if (count($permissions) != 0) {
            foreach ($permissions as $key => $value) {
                $user->givePermissionTo($value->name);
            }
        }
    }
}
