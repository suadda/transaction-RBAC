<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Array roles sudah diperbaiki (tidak ada duplikat & menggunakan Direktur)
        $roles = [
            'Staff',
            'SPV',
            'Manager',
            'Direktur',
            'Finance',
        ];

        // 2. Menggunakan firstOrCreate
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 3. Array users disesuaikan dengan dokumen soal
        $users = [
            [
                'name' => 'Staff User',
                'email' => 'staff@test.com',
                'role_name' => 'Staff',
            ],
            [
                'name' => 'SPV User',
                'email' => 'spv@test.com',
                'role_name' => 'SPV',
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@test.com',
                'role_name' => 'Manager',
            ],
            [
                'name' => 'Direktur User', // Sudah diubah
                'email' => 'direktur@test.com', // Sudah diubah
                'role_name' => 'Direktur', // Sudah diubah
            ],
            [
                'name' => 'Finance User',
                'email' => 'finance@test.com',
                'role_name' => 'Finance',
            ],
        ];

        // 4. Menggunakan firstOrCreate untuk User
        foreach ($users as $userData) {
            $role = Role::where('name', $userData['role_name'])->first();

            User::firstOrCreate(
                ['email' => $userData['email']], // Mengecek apakah email sudah ada
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $role->id,
                ]
            );
        }
    }
}