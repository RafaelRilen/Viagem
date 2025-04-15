<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Rafa Company',
            'email' => 'rafa@teste.com',
            'password' => bcrypt('senha123'),
            'is_admin' => false,
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('senhaSegura'),
            'is_admin' => true,
        ]);
    }
}
