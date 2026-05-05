<?php

namespace Database\Seeders;

use App\Models\AccountManager;
use Illuminate\Database\Seeder;

class AccountManagerSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Budi Santoso',    'nik' => 'AM001', 'email' => 'budi@telkom.test',    'phone' => '08120000001'],
            ['name' => 'Sari Wijaya',     'nik' => 'AM002', 'email' => 'sari@telkom.test',    'phone' => '08120000002'],
            ['name' => 'Andi Pratama',    'nik' => 'AM003', 'email' => 'andi@telkom.test',    'phone' => '08120000003'],
            ['name' => 'Rina Kusuma',     'nik' => 'AM004', 'email' => 'rina@telkom.test',    'phone' => '08120000004'],
            ['name' => 'Hadi Setiawan',   'nik' => 'AM005', 'email' => 'hadi@telkom.test',    'phone' => '08120000005'],
            ['name' => 'Dewi Anggraini',  'nik' => 'AM006', 'email' => 'dewi@telkom.test',    'phone' => '08120000006'],
            ['name' => 'Faisal Rahman',   'nik' => 'AM007', 'email' => 'faisal@telkom.test',  'phone' => '08120000007'],
        ];

        foreach ($rows as $r) {
            AccountManager::updateOrCreate(['nik' => $r['nik']], $r);
        }
    }
}
