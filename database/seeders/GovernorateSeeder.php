<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['name' => 'دمشق'],
            ['name' => 'حلب'],
            ['name' => 'اللاذقية'],
            ['name' => 'حماة'],
            ['name' => 'حمص'],
        ];

        foreach ($governorates as $gov) {
            Governorate::firstOrCreate($gov);
        }
    }
}
