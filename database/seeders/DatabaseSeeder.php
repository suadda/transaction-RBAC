<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Category; 
use App\Models\Budget;  
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Role
        $roles = ['Staff', 'SPV', 'Manager', 'Direktur', 'Finance'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Master Kategori & Budget
        $categories = [
            'PO Produk' => 100000000,
            'Operasional' => 50000000,
            'Marketing' => 75000000,
        ];
        foreach ($categories as $catName => $budgetAmount) {
            $category = Category::firstOrCreate(['name' => $catName]);
            Budget::firstOrCreate(
                ['category_id' => $category->id],
                ['amount' => $budgetAmount]
            );
        }

        // 3. Master User
        $users = [
            ['name' => 'Staff User', 'email' => 'staff@test.com', 'role_name' => 'Staff'],
            ['name' => 'SPV User', 'email' => 'spv@test.com', 'role_name' => 'SPV'],
            ['name' => 'Manager User', 'email' => 'manager@test.com', 'role_name' => 'Manager'],
            ['name' => 'Direktur User', 'email' => 'direktur@test.com', 'role_name' => 'Direktur'],
            ['name' => 'Finance User', 'email' => 'finance@test.com', 'role_name' => 'Finance'],
        ];

        foreach ($users as $userData) {
            $role = Role::where('name', $userData['role_name'])->first();
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $role->id,
                ]
            );
        }
    }
}