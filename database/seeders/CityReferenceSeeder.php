<?php

namespace Database\Seeders;

use App\Models\CityReference;
use Illuminate\Database\Seeder;

class CityReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->cities() as $row) {
            CityReference::updateOrCreate(
                ['province' => $row['province'], 'name' => $row['name']],
                $row,
            );
        }
    }

    private function cities(): array
    {
        return [
            // Kalimantan Barat
            ['name' => 'Pontianak',     'province' => 'Kalimantan Barat',  'latitude' => -0.0263,  'longitude' => 109.3425],
            ['name' => 'Singkawang',    'province' => 'Kalimantan Barat',  'latitude' =>  0.9077,  'longitude' => 108.9853],
            ['name' => 'Sintang',       'province' => 'Kalimantan Barat',  'latitude' =>  0.0716,  'longitude' => 111.4951],
            ['name' => 'Ketapang',      'province' => 'Kalimantan Barat',  'latitude' => -1.8295,  'longitude' => 109.9637],
            ['name' => 'Sambas',        'province' => 'Kalimantan Barat',  'latitude' =>  1.3617,  'longitude' => 109.3043],

            // Kalimantan Tengah
            ['name' => 'Palangka Raya', 'province' => 'Kalimantan Tengah', 'latitude' => -2.2096,  'longitude' => 113.9108],
            ['name' => 'Sampit',        'province' => 'Kalimantan Tengah', 'latitude' => -2.5352,  'longitude' => 112.9609],
            ['name' => 'Pangkalan Bun', 'province' => 'Kalimantan Tengah', 'latitude' => -2.6863,  'longitude' => 111.6235],
            ['name' => 'Muara Teweh',   'province' => 'Kalimantan Tengah', 'latitude' => -0.9583,  'longitude' => 114.8939],

            // Kalimantan Selatan
            ['name' => 'Banjarmasin',   'province' => 'Kalimantan Selatan','latitude' => -3.3194,  'longitude' => 114.5908],
            ['name' => 'Banjarbaru',    'province' => 'Kalimantan Selatan','latitude' => -3.4572,  'longitude' => 114.8311],
            ['name' => 'Martapura',     'province' => 'Kalimantan Selatan','latitude' => -3.4109,  'longitude' => 114.8388],
            ['name' => 'Kotabaru',      'province' => 'Kalimantan Selatan','latitude' => -3.2354,  'longitude' => 116.2191],

            // Kalimantan Timur
            ['name' => 'Samarinda',     'province' => 'Kalimantan Timur',  'latitude' => -0.5023,  'longitude' => 117.1536],
            ['name' => 'Balikpapan',    'province' => 'Kalimantan Timur',  'latitude' => -1.2379,  'longitude' => 116.8529],
            ['name' => 'Bontang',       'province' => 'Kalimantan Timur',  'latitude' =>  0.1252,  'longitude' => 117.4969],
            ['name' => 'Tenggarong',    'province' => 'Kalimantan Timur',  'latitude' => -0.4023,  'longitude' => 116.9883],
            ['name' => 'Sangatta',      'province' => 'Kalimantan Timur',  'latitude' =>  0.5085,  'longitude' => 117.5821],

            // Kalimantan Utara
            ['name' => 'Tarakan',       'province' => 'Kalimantan Utara',  'latitude' =>  3.3274,  'longitude' => 117.5746],
            ['name' => 'Tanjung Selor', 'province' => 'Kalimantan Utara',  'latitude' =>  2.8476,  'longitude' => 117.3661],
            ['name' => 'Nunukan',       'province' => 'Kalimantan Utara',  'latitude' =>  4.1395,  'longitude' => 117.6644],
            ['name' => 'Malinau',       'province' => 'Kalimantan Utara',  'latitude' =>  3.5825,  'longitude' => 116.6418],

            // Ibukota provinsi luar Kalimantan (sample)
            ['name' => 'Jakarta',       'province' => 'DKI Jakarta',       'latitude' => -6.2088,  'longitude' => 106.8456],
            ['name' => 'Bandung',       'province' => 'Jawa Barat',        'latitude' => -6.9175,  'longitude' => 107.6191],
            ['name' => 'Semarang',      'province' => 'Jawa Tengah',       'latitude' => -6.9667,  'longitude' => 110.4167],
            ['name' => 'Yogyakarta',    'province' => 'DI Yogyakarta',     'latitude' => -7.7956,  'longitude' => 110.3695],
            ['name' => 'Surabaya',      'province' => 'Jawa Timur',        'latitude' => -7.2575,  'longitude' => 112.7521],
            ['name' => 'Denpasar',      'province' => 'Bali',              'latitude' => -8.6500,  'longitude' => 115.2167],
            ['name' => 'Mataram',       'province' => 'Nusa Tenggara Barat','latitude'=> -8.5833,  'longitude' => 116.1167],
            ['name' => 'Kupang',        'province' => 'Nusa Tenggara Timur','latitude'=> -10.1718, 'longitude' => 123.6075],
            ['name' => 'Medan',         'province' => 'Sumatera Utara',    'latitude' =>  3.5952,  'longitude' => 98.6722],
            ['name' => 'Padang',        'province' => 'Sumatera Barat',    'latitude' => -0.9492,  'longitude' => 100.3543],
            ['name' => 'Pekanbaru',     'province' => 'Riau',              'latitude' =>  0.5071,  'longitude' => 101.4478],
            ['name' => 'Palembang',     'province' => 'Sumatera Selatan',  'latitude' => -2.9909,  'longitude' => 104.7565],
            ['name' => 'Bandar Lampung','province' => 'Lampung',           'latitude' => -5.3971,  'longitude' => 105.2668],
            ['name' => 'Banda Aceh',    'province' => 'Aceh',              'latitude' =>  5.5483,  'longitude' => 95.3238],
            ['name' => 'Makassar',      'province' => 'Sulawesi Selatan',  'latitude' => -5.1477,  'longitude' => 119.4327],
            ['name' => 'Manado',        'province' => 'Sulawesi Utara',    'latitude' =>  1.4748,  'longitude' => 124.8421],
            ['name' => 'Palu',          'province' => 'Sulawesi Tengah',   'latitude' => -0.8917,  'longitude' => 119.8707],
            ['name' => 'Kendari',       'province' => 'Sulawesi Tenggara', 'latitude' => -3.9985,  'longitude' => 122.5126],
            ['name' => 'Gorontalo',     'province' => 'Gorontalo',         'latitude' =>  0.5435,  'longitude' => 123.0568],
            ['name' => 'Mamuju',        'province' => 'Sulawesi Barat',    'latitude' => -2.6748,  'longitude' => 118.8889],
            ['name' => 'Ambon',         'province' => 'Maluku',            'latitude' => -3.6954,  'longitude' => 128.1814],
            ['name' => 'Ternate',       'province' => 'Maluku Utara',      'latitude' =>  0.7910,  'longitude' => 127.3654],
            ['name' => 'Jayapura',      'province' => 'Papua',             'latitude' => -2.5337,  'longitude' => 140.7181],
            ['name' => 'Manokwari',     'province' => 'Papua Barat',       'latitude' => -0.8615,  'longitude' => 134.0623],
        ];
    }
}
