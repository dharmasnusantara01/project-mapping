<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nip'   => '198001012005011001',
                'name'  => 'Superadmin Demo',
                'email' => 'superadmin@funneling.test',
                'role'  => UserRole::Superadmin,
            ],
            [
                'nip'   => '198505152010012002',
                'name'  => 'Manajer Sales Demo',
                'email' => 'manajer@funneling.test',
                'role'  => UserRole::ManajerSales,
            ],
            [
                'nip'   => '199203202015031003',
                'name'  => 'Sales Rep Demo',
                'email' => 'sales@funneling.test',
                'role'  => UserRole::Sales,
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['nip' => $u['nip']],
                array_merge($u, [
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]),
            );
        }
    }
}
