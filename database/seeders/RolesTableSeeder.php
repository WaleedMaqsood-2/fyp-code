<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::insert([
            ['role_name' => 'Admin'],
            ['role_name' => 'Police'],
            ['role_name' => 'Forensic Analyst'],
            ['role_name' => 'Public User']
        ]);
    }
}
