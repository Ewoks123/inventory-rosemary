<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::updateOrCreate(
            ['username' => 'Rosemary'],
            [
                'nama_admin' => 'Rosemary',
                'password' => Hash::make('admin'),
                'email' => 'rosemary@example.com',
                'role_admin' => 'admin',
            ]
        );
    }
}
