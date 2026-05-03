<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            ['name' => 'Imigrasi', 'slug' => 'imigrasi', 'color' => '#1d4ed8', 'order' => 1],
            ['name' => 'Kepolisian',    'slug' => 'polda',    'color' => '#dc2626', 'order' => 2],
            ['name' => 'Kemkes',   'slug' => 'kemkes',   'color' => '#16a34a', 'order' => 3],
        ];

        foreach ($sectors as $row) {
            Sector::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
