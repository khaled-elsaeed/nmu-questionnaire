<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User; 

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'email' => 'khaled@gmail.com', 
            'username_ar' => 'خالد زهران', 
            'username_en' => 'Khaled Zahran', 
            'password' => bcrypt('password'), 
            'is_active' => true, 
            'profile_picture' => null, 
            'last_login' => now(), 
            'created_at' => now(), 
            'updated_at' => now(), 
        ]);

        $user->assignRole('admin');
    }
}