<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\batches;
use Faker\Factory as Faker;



class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        for ($i = 0; $i < 100; $i++) {

            batches::create([
                'name' => 'Batch ' . ($i + 1),
                'description' => $faker->sentence,
                'start_date' => $faker->date('Y-m-d'),
                'end_date' => $faker->date('Y-m-d'),
                'status' => $faker->randomElement(['active', 'inactive']),
                'is_passout' => $faker->boolean,
                'created_by' => $faker->name,
                'updated_by' => $faker->name,
                'deleted_by' => null,
                'image' => null,
                'semester' => $faker->numberBetween(1, 10),
            ]);
        }
    }
}
