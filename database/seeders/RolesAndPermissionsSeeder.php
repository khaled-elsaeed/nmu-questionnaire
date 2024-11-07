<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create permissions
        //  Permission::create(['name' => 'edit articles']);
        //  Permission::create(['name' => 'delete articles']);

 
         // Create roles and assign existing permissions
         $adminRole = Role::create(['name' => 'admin']);
         $residentRole = Role::create(['name' => 'student']);
 
        //  // Assign permissions to roles
        //  $adminRole->givePermissionTo('edit articles');
        //  $adminRole->givePermissionTo('delete articles');
        //  $adminRole->givePermissionTo('publish articles');
    }
}
