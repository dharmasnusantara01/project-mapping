<?php

namespace Database\Seeders;

use App\Enums\PublicStatus;
use App\Enums\UserRole;
use App\Models\CityReference;
use App\Models\Project;
use App\Models\ProjectLocation;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $imigrasi = Sector::where('slug', 'imigrasi')->firstOrFail();
        $polda    = Sector::where('slug', 'polda')->firstOrFail();
        $kemkes   = Sector::where('slug', 'kemkes')->firstOrFail();

        $manajer = User::where('role', UserRole::ManajerSales->value)->first();

        $samples = [
            // [sector, name, customer, year, status, isPublic, city]
            [$imigrasi, 'Modernisasi Layanan Paspor',     'Kanim Pontianak',     2024, PublicStatus::Selesai,  true,  'Pontianak'],
            [$imigrasi, 'Sistem Antrian Imigrasi',        'Kanim Samarinda',     2025, PublicStatus::Berjalan, true,  'Samarinda'],
            [$imigrasi, 'Kios Layanan Mandiri',           'Kanim Tarakan',       2025, PublicStatus::Berjalan, true,  'Tarakan'],
            [$imigrasi, 'Integrasi Border Control',       'Kanim Nunukan',       2023, PublicStatus::Selesai,  true,  'Nunukan'],
            [$imigrasi, 'Aplikasi Layanan Visa Online',   'Kanim Balikpapan',    2024, PublicStatus::Selesai,  true,  'Balikpapan'],

            [$polda,    'Sistem Tilang Elektronik',       'Polda Kalbar',        2024, PublicStatus::Selesai,  true,  'Pontianak'],
            [$polda,    'Command Center Kepolisian',      'Polda Kalsel',        2025, PublicStatus::Berjalan, true,  'Banjarmasin'],
            [$polda,    'Mobile Patrol Tracker',          'Polda Kalteng',       2024, PublicStatus::Selesai,  true,  'Palangka Raya'],
            [$polda,    'Digital Forensic Lab',           'Polda Kaltim',        2025, PublicStatus::Berjalan, true,  'Samarinda'],
            [$polda,    'Sistem Identifikasi Biometrik',  'Polda Kaltara',       2023, PublicStatus::Selesai,  true,  'Tanjung Selor'],

            [$kemkes,   'Sistem Rekam Medis Elektronik',  'RSUD dr. Soedarso',   2024, PublicStatus::Selesai,  true,  'Pontianak'],
            [$kemkes,   'Telemedicine Daerah Terpencil',  'Dinkes Kalteng',      2025, PublicStatus::Berjalan, true,  'Sampit'],
            [$kemkes,   'SIMRS Terintegrasi',             'RSUD Ulin',           2024, PublicStatus::Selesai,  true,  'Banjarmasin'],
            [$kemkes,   'Aplikasi Vaksinasi Daerah',      'Dinkes Kaltim',       2023, PublicStatus::Selesai,  true,  'Balikpapan'],
            [$kemkes,   'SIMPUS Puskesmas',               'Dinkes Kaltara',      2025, PublicStatus::Berjalan, true,  'Tarakan'],

            // Beberapa di luar Kalimantan untuk variasi
            [$imigrasi, 'Pengembangan Layanan Pelabuhan', 'Kanim Surabaya',      2024, PublicStatus::Selesai,  true,  'Surabaya'],
            [$polda,    'Integrasi CCTV Kota',            'Polda Metro Jaya',    2025, PublicStatus::Berjalan, true,  'Jakarta'],
            [$kemkes,   'Dashboard Surveilans Penyakit',  'Kemenkes RI',         2024, PublicStatus::Selesai,  true,  'Jakarta'],

            // Draft (belum publish)
            [$imigrasi, 'Pilot Program e-Visa',           'Kanim Makassar',      2025, PublicStatus::Berjalan, false, 'Makassar'],
            [$polda,    'Upgrade Network Polda',          'Polda Sulsel',        2025, PublicStatus::Berjalan, false, 'Makassar'],
        ];

        foreach ($samples as [$sector, $name, $customer, $year, $status, $isPublic, $cityName]) {
            $city = CityReference::where('name', $cityName)->first();
            if (! $city) {
                continue;
            }

            $project = Project::create([
                'name'           => $name,
                'customer_name'  => $customer,
                'sector_id'      => $sector->id,
                'year'           => $year,
                'public_summary' => $this->summary($name, $customer),
                'is_public'      => $isPublic,
                'public_status'  => $status,
                'published_by'   => $isPublic ? $manajer?->id : null,
                'published_at'   => $isPublic ? now() : null,
            ]);

            ProjectLocation::create([
                'project_id'         => $project->id,
                'city'               => $city->name,
                'province'           => $city->province,
                'latitude'           => $city->latitude,
                'longitude'          => $city->longitude,
                'is_manual_override' => false,
                'geocoded_at'        => now(),
                'is_primary'         => true,
            ]);
        }
    }

    private function summary(string $name, string $customer): string
    {
        return "{$name} dikerjakan untuk {$customer} sebagai bagian dari modernisasi layanan publik berbasis digital.";
    }
}
