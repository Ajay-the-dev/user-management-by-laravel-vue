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
        
        for ($i = 0; $i < 2; $i++) {


            batches::create([
                'id' => $i+1,
                'name' => 'Batch' . ($i + 1),
                'description' => $faker->sentence,
                'start_date' => $faker->date('Y-m-d'),
                'end_date' => $faker->date('Y-m-d'),
                'status' => 'active',
                'is_passout' => false,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'image' => null,
                'semester' => 1,
            ]);
        }
    }
}
