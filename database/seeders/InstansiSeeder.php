<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AccountManager;
use App\Models\Instansi;
use App\Models\Sector;
use App\Models\User;
use App\Models\Witel;
use Illuminate\Database\Seeder;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        $imigrasi = Sector::where('slug', 'imigrasi')->firstOrFail();
        $polda    = Sector::where('slug', 'polda')->firstOrFail();
        $kemkes   = Sector::where('slug', 'kemkes')->firstOrFail();

        $manajer = User::where('role', UserRole::ManajerSales->value)->first();

        $witelByCode = Witel::pluck('id', 'code');
        $ams = AccountManager::orderBy('id')->get();
        if ($ams->isEmpty()) {
            return;
        }

        $samples = [
            // [sector, nama_instansi, alamat, telpon, lat, lng, witelCode, isPublic]
            [$imigrasi, 'Kanim Pontianak',    'Jl. Letjen Sutoyo No.122, Pontianak',    '0561-732294',  -0.0263,  109.3425, 'KBR', true],
            [$imigrasi, 'Kanim Samarinda',    'Jl. Pangeran Antasari No.1, Samarinda',  '0541-741301',  -0.5021,  117.1535, 'KTM', true],
            [$imigrasi, 'Kanim Tarakan',      'Jl. Mulawarman No.15, Tarakan',          '0551-21039',   3.3273,   117.5736, 'KTA', true],
            [$imigrasi, 'Kanim Balikpapan',   'Jl. Jenderal Sudirman, Balikpapan',      '0542-733301',  -1.2654,  116.8312, 'KTM', true],
            [$imigrasi, 'Kanim Surabaya',     'Jl. Jenderal S. Parman No.58A, Sby',     '031-8492222',  -7.2575,  112.7521, 'SBY', true],
            [$imigrasi, 'Kanim Makassar',     'Jl. Perintis Kemerdekaan, Makassar',     '0411-553031',  -5.1477,  119.4327, 'MKS', false],

            [$polda,    'Polda Kalbar',       'Jl. Jenderal Ahmad Yani, Pontianak',     '0561-734555',  -0.0532,  109.3477, 'KBR', true],
            [$polda,    'Polda Kalsel',       'Jl. Jenderal S. Parman, Banjarmasin',    '0511-3251341', -3.3194,  114.5907, 'KSL', true],
            [$polda,    'Polda Kalteng',      'Jl. Tjilik Riwut, Palangka Raya',        '0536-3221344', -2.2099,  113.9110, 'KTG', true],
            [$polda,    'Polda Kaltim',       'Jl. Syarifuddin Yoes, Balikpapan',       '0542-733110',  -1.2375,  116.8528, 'KTM', true],
            [$polda,    'Polda Metro Jaya',   'Jl. Jenderal Sudirman, Jakarta',         '021-5234001',  -6.2240,  106.8085, 'JKT', true],
            [$polda,    'Polda Sulsel',       'Jl. Perintis Kemerdekaan, Makassar',     '0411-585013',  -5.1543,  119.4544, 'MKS', false],

            [$kemkes,   'RSUD dr. Soedarso',  'Jl. Adisucipto KM 5, Pontianak',         '0561-737701',  -0.0918,  109.3548, 'KBR', true],
            [$kemkes,   'Dinkes Kalteng',     'Jl. Yos Sudarso, Palangka Raya',         '0536-3221001', -2.2031,  113.9201, 'KTG', true],
            [$kemkes,   'RSUD Ulin',          'Jl. A. Yani No.43, Banjarmasin',         '0511-3252180', -3.3275,  114.5935, 'KSL', true],
            [$kemkes,   'Dinkes Kaltim',      'Jl. Basuki Rahmat, Samarinda',           '0541-741231',  -0.4880,  117.1450, 'KTM', true],
            [$kemkes,   'Kemenkes RI',        'Jl. HR Rasuna Said, Jakarta',            '021-5201590',  -6.2300,  106.8330, 'JKT', true],
            [$kemkes,   'Dinkes Bandung',     'Jl. Supratman No.73, Bandung',           '022-7273744',  -6.9036,  107.6317, 'BDG', true],
        ];

        foreach ($samples as $idx => [$sector, $nama, $alamat, $telpon, $lat, $lng, $witelCode, $isPublic]) {
            $witelId = $witelByCode[$witelCode] ?? null;
            if (! $witelId) {
                continue;
            }
            $am = $ams[$idx % $ams->count()];

            Instansi::create([
                'nama_instansi'      => $nama,
                'alamat_instansi'    => $alamat,
                'telpon_instansi'    => $telpon,
                'latitude'           => $lat,
                'longitude'          => $lng,
                'witel_id'           => $witelId,
                'account_manager_id' => $am->id,
                'sector_id'          => $sector->id,
                'public_summary'     => "{$nama} adalah instansi yang dilayani Telkom dalam program transformasi digital.",
                'is_public'          => $isPublic,
                'published_by'       => $isPublic ? $manajer?->id : null,
                'published_at'       => $isPublic ? now() : null,
            ]);
        }
    }
}
