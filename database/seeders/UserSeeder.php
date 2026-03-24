<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Employee User',
            'email'    => 'employee@gmail.com',
            'password' => Hash::make('qwerty'),
            'role'     => 'employee',
        ]);
    }
}