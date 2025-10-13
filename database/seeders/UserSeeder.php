<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@bloodbank.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Agent médical
        User::create([
            'first_name' => 'Marie',
            'last_name' => 'Dubois',
            'email' => 'marie.dubois@bloodbank.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'phone' => '01.23.45.67.89',
            'is_active' => true,
        ]);

        // Donneurs d'exemple
        $donors = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Martin',
                'email' => 'jean.martin@email.com',
                'blood_group' => 'A+',
                'birth_date' => '1985-05-15',
                'gender' => 'M'
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Laurent',
                'email' => 'sophie.laurent@email.com',
                'blood_group' => 'O-',
                'birth_date' => '1990-08-22',
                'gender' => 'F'
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Moreau',
                'email' => 'pierre.moreau@email.com',
                'blood_group' => 'B+',
                'birth_date' => '1988-03-10',
                'gender' => 'M'
            ]
        ];

        foreach ($donors as $donorData) {
            User::create([
                'first_name' => $donorData['first_name'],
                'last_name' => $donorData['last_name'],
                'email' => $donorData['email'],
                'password' => Hash::make('password'),
                'role' => 'donor',
                'blood_group' => $donorData['blood_group'],
                'birth_date' => $donorData['birth_date'],
                'gender' => $donorData['gender'],
                'phone' => '01.' . rand(10,99) . '.' . rand(10,99) . '.' . rand(10,99) . '.' . rand(10,99),
                'is_active' => true,
            ]);
        }
        //
    }
}
