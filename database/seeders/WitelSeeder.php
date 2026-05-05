<?php

namespace Database\Seeders;

use App\Models\Witel;
use Illuminate\Database\Seeder;

class WitelSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'WITEL JAKARTA',     'code' => 'JKT'],
            ['name' => 'WITEL BANDUNG',     'code' => 'BDG'],
            ['name' => 'WITEL SURABAYA',    'code' => 'SBY'],
            ['name' => 'WITEL MAKASSAR',    'code' => 'MKS'],
            ['name' => 'WITEL KALBAR',      'code' => 'KBR'],
            ['name' => 'WITEL KALSEL',      'code' => 'KSL'],
            ['name' => 'WITEL KALTIM',      'code' => 'KTM'],
            ['name' => 'WITEL KALTENG',     'code' => 'KTG'],
            ['name' => 'WITEL KALTARA',     'code' => 'KTA'],
        ];

        foreach ($rows as $r) {
            Witel::updateOrCreate(['name' => $r['name']], $r);
        }
    }
}
