<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SectorSeeder::class,
            WitelSeeder::class,
            AccountManagerSeeder::class,
            InstansiSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
