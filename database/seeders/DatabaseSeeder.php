<?php

namespace Database\Seeders;

use Database\Seeders\CityReferenceSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SectorSeeder::class,
            CityReferenceSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
