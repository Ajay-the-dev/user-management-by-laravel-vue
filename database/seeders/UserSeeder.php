<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\batches;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    // Students created per batch
    private const STUDENTS_PER_BATCH = 8;

    // Codes match the dropdowns in resources/js/utils/dataHelper.js
    private const UNIVERSITY_COURSES = [
        'KSMA' => ['BM', 'MS', 'CR'],
        'ISM'  => ['BM', 'MD', 'MB', 'PHARM'],
        'KNU'  => ['BM', 'PATH', 'SURG', 'PED'],
    ];
    private const DEPARTMENTS = ['ANAT', 'PHYS', 'BIOC', 'PATH', 'PHARM', 'MICRO'];
    private const LOCATIONS = ['HA', 'HB', 'HC'];

    private const STAFF = [
        ['username' => 'priya.menon',  'name' => 'Priya Menon',  'designation' => 'Principal'],
        ['username' => 'rahul.verma',  'name' => 'Rahul Verma',  'designation' => 'Accountant'],
        ['username' => 'anita.joseph', 'name' => 'Anita Joseph', 'designation' => 'Registrar'],
        ['username' => 'vikram.rao',   'name' => 'Vikram Rao',   'designation' => 'Lecturer'],
        ['username' => 'sneha.pillai', 'name' => 'Sneha Pillai', 'designation' => 'Hostel Warden'],
    ];

    /**
     * Seed staff users and students for every batch.
     * All seeded accounts use the password "password".
     */
    public function run(): void
    {
        $faker = Faker::create('en_IN');
        $faker->seed(2026);
        $password = Hash::make('password');

        foreach (self::STAFF as $i => $staff) {
            User::create([
                ...$staff,
                'email'       => $staff['username'] . '@feemo.test',
                'password'    => $password,
                'mobile'      => '90000000' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'dob'         => $faker->date('Y-m-d', '1990-12-31'),
                'gender'      => $faker->randomElement(['male', 'female']),
                'address'     => [$this->address($faker)],
                'role'        => 'STAFF',
                'university'  => null,
                'location'    => null,
            ]);
        }

        foreach (batches::orderBy('id')->get() as $batch) {
            for ($n = 1; $n <= self::STUDENTS_PER_BATCH; $n++) {
                $gender     = $faker->randomElement(['male', 'female']);
                $firstName  = $faker->firstName($gender);
                $lastName   = $faker->lastName();
                $username   = preg_replace('/[^a-z.]/', '', strtolower("$firstName.$lastName")) . $batch->id . $n;
                $university = $faker->randomElement(array_keys(self::UNIVERSITY_COURSES));

                User::create([
                    'name'                   => "$firstName $lastName",
                    'username'               => $username,
                    'email'                  => $username . '@student.feemo.test',
                    'password'               => $password,
                    'mobile'                 => '8' . $batch->id . str_pad($n, 8, '0', STR_PAD_LEFT),
                    'dob'                    => $faker->dateTimeBetween('-24 years', '-18 years')->format('Y-m-d'),
                    'gender'                 => $gender,
                    'address'                => [$this->address($faker)],
                    'role'                   => 'STUDENT',
                    'batchId'                => $batch->id,
                    'rollNo'                 => sprintf('B%d-%03d', $batch->id, $n),
                    'university'             => $university,
                    'course'                 => $faker->randomElement(self::UNIVERSITY_COURSES[$university]),
                    'department'             => $faker->randomElement(self::DEPARTMENTS),
                    'location'               => $faker->randomElement(self::LOCATIONS),
                    'visaType'               => 'EDUCATION',
                    'visaStatus'             => 'active',
                    'visaExpiryDate'         => $faker->dateTimeBetween('+3 months', '+3 years')->format('Y-m-d'),
                    'insuranceStatus'        => 'active',
                    'insuranceExpiryDate'    => $faker->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
                    'passportNumber'         => strtoupper($faker->bothify('?#######')),
                    'passportIssuingCountry' => 'IN',
                    'passportIssueDate'      => $faker->dateTimeBetween('-6 years', '-1 year')->format('Y-m-d'),
                    'passportExpiryDate'     => $faker->dateTimeBetween('+4 years', '+9 years')->format('Y-m-d'),
                    'parentDetails'          => [[
                        'name'     => $faker->firstName('male') . " $lastName",
                        'relation' => 'Father',
                        'email'    => $faker->unique()->safeEmail(),
                        'mobile'   => '7' . $batch->id . str_pad($n, 8, '0', STR_PAD_LEFT),
                    ]],
                ]);
            }
        }
    }

    private function address($faker): array
    {
        return [
            'street'  => $faker->streetName(),
            'city'    => $faker->city(),
            'country' => 'IN',
        ];
    }
}
