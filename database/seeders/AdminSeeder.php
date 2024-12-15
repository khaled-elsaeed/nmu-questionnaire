<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminEmail = 'superadmin@nmu.edu.eg';
        $superAdminRole = 'super_admin';

        Role::firstOrCreate(['name' => $superAdminRole]);

        $superAdmin = User::create([
            'email' => $superAdminEmail,
            'username_en' => 'superadmin',
            'password' => Hash::make('defaultpassword'),
            'is_active' => 1,
        ]);

        $superAdmin->assignRole($superAdminRole);

        $faculties = [
            ['abbreviation' => 'CSE', 'name' => 'Computer Science & Engineering', 'id' => 1],
            ['abbreviation' => 'PHARM', 'name' => 'Pharmacy', 'id' => 3],
        ];

        foreach ($faculties as $faculty) {
            $facultyAbbreviation = $faculty['abbreviation'];
            $facultyId = $faculty['id'];

            $adminEmail = strtolower($facultyAbbreviation) . $facultyId . '@nmu.edu.eg';
            $adminRole = 'admin';
            $facultyRole = strtolower($facultyAbbreviation) . '_fac_' . $facultyId;

            Role::firstOrCreate(['name' => $adminRole]);
            Role::firstOrCreate(['name' => $facultyRole]);

            $user = User::create([
                'email' => $adminEmail,
                'username_en' => $facultyAbbreviation . $facultyId,
                'password' => Hash::make('defaultpassword'),
                'is_active' => 1,
            ]);

            $user->assignRole($adminRole);
            $user->assignRole($facultyRole);
        }
    }
}
