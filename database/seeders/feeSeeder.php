<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fee;

class feeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 0; $i < 2; $i++) {
            $batchId = $i+1;
            Fee::create([
                'batchId'  => $batchId,
                'type' => 'Tution',
                'amount' => '60000',
                'status' => 'active',
                'created_by' => 1
            ]);
            Fee::create([
                'batchId'=>$batchId,
                'type' => 'Hostel',
                'amount' => '100000',
                'status' => 'active',
                'created_by' => 1
            ]);
        }
    }
}
