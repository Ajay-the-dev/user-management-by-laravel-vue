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
        
        for ($i = 0; $i < 5; $i++) {


            batches::create([
                'name' => 'IMF ' . ($i + 1).' th Year',
                'description' => $faker->sentence,
                'start_date' => $faker->date('Y-m-d'),
                'end_date' => $faker->date('Y-m-d'),
                'status' => 'active',
                'is_passout' => false,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'image' => null,
                'semester' => ($i + 1) > 3 ? 6 : 2,
            ]);
        }
    }
}
