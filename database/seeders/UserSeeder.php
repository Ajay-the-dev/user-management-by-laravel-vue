<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;




class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Faker::create();
        
        for ($i = 0; $i < 100; $i++) {

             $address = [
                [
                    'city' => $faker->city,
                    'state' => $faker->state,
                    'street' => $faker->streetName,
                    'country' => 'India',
                    'landmark' => $faker->streetSuffix,
                    'officeType' => $faker->randomElement(['home', 'office']),
                ]
            ];

            User::create([
                'username' => $faker->userName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'), 
                'mobile' => $faker->phoneNumber,
                'dob' => $faker->date('Y-m-d', '2005-12-31'),
                'gender' => $faker->randomElement(['male', 'female']),
                'address' => json_encode($address),
                'name' => $faker->name,
            ]);
        }
    }
}
