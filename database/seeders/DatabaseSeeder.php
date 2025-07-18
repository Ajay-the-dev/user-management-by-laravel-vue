<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'admin',
            'name' => 'admin',
            'email' => 'test@test.com',
            'mobile' => '9999999999',
            'password' => bcrypt('admin'),
            'address' => '{}',
            'dob' => '1998-05-16',
            'gender' => 'Male'
        ]);

        // $this->call(UserSeeder::class);

    }
}
