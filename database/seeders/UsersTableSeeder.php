<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'cnic' => '12345-1234567-1',
                'contact_number' => '03001234567',
                'role_id' => 1, // Admin
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Police Officer',
                'email' => 'police@example.com',
                'role_id' => 2, // Police
                'cnic' => '23456-2345678-2',
                'contact_number' => '03012345678',
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Forensic Analyst',
                'email' => 'forensic@example.com',
                'cnic' => '34567-3456789-3',
                'contact_number' => '03023456789',
                'role_id' => 3, // Forensic Analyst
                'password' => Hash::make('password')
            ],
            [
                'name' => 'Public User',
                'email' => 'public@example.com',
                'role_id' => 4, // Public
                'cnic' => '45678-4567890-4',
                'contact_number' => '03034567890',
                'password' => Hash::make('password')
            ]
        ]);
    }
}
