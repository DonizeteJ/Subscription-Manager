<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@example.com')->exists()) {

            User::create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => 'password123'
            ]);

            $this->command->info('Admin user created successfully.');
        }
    }
}
