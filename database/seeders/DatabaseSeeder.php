<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder; 
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\UsersTableSeeder; 
use Database\Seeders\CoursesAndStudentsSeeder; 


 


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CoursesAndStudentsSeeder::class);

    }
}
