<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create User Types
        \App\Models\UserType::updateOrCreate(
            ['slug' => 'company'],
            ['name' => 'Company User']
        );

        \App\Models\UserType::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator']
        );

        // 2. Create Default Cities
        $cities = ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina'];
        foreach ($cities as $city) {
            \App\Models\City::updateOrCreate(['name' => $city]);
        }

        // 3. Create Default Sale People
        $salesPeople = ['Default Sales', 'Omar Al-Fahd', 'Sara Ahmed', 'Ahmed Mansour'];
        foreach ($salesPeople as $person) {
            \App\Models\SalePerson::updateOrCreate(['name' => $person]);
        }
    }
}
