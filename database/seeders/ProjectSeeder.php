<?php

namespace Database\Seeders;

use App\Enums\Division;
use App\Enums\ProjectStage;
use App\Enums\UserRole;
use App\Models\Instansi;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $sales = User::where('role', UserRole::Sales->value)->first();
        $instansi = Instansi::take(8)->get();
        if ($instansi->isEmpty()) {
            return;
        }

        $samples = [
            // [stage, nama_project, division, est_go_live (Y-m), revenue, durasi]
            [ProjectStage::Qualified, 'Pengadaan Layanan Internet 1Gbps', Division::Government, '2026-09', 850_000_000, null, null],
            [ProjectStage::Qualified, 'Solusi Cloud Computing Migrasi',   Division::Enterprise, '2026-10', 1_200_000_000, null, null],
            [ProjectStage::Submit,    'Implementasi SD-WAN Multi-Site',   Division::Government, '2026-08', 2_400_000_000, 'Implementasi SD-WAN di 5 lokasi cabang dengan SLA 99.9%', 12],
            [ProjectStage::Submit,    'Pengadaan IP-PBX & Contact Center', Division::Sme,       '2026-11', 450_000_000,  'Sistem telepon korporat dengan 50 ekstensi.', 6],
            [ProjectStage::Win,       'Layanan Astinet Dedicated 500Mbps', Division::Government, '2026-07', 1_800_000_000, 'Internet dedicated untuk kantor pusat.', 24],
            [ProjectStage::Win,       'Solusi Smart City Monitoring',     Division::Government, '2026-06', 5_500_000_000, 'CCTV terintegrasi dan command center.', 36],
            [ProjectStage::Lost,      'Tender Proyek Digitalisasi RS',    Division::Government, '2026-05', 3_200_000_000, null, null],
        ];

        foreach ($samples as $idx => [$stage, $namaProject, $division, $estYm, $revenue, $description, $durasi]) {
            $target = $instansi[$idx % $instansi->count()];

            $payload = [
                'instansi_id'      => $target->id,
                'stage'            => $stage->value,
                'nama_project'     => $namaProject,
                'nama_pelanggan'   => $target->nama_instansi,
                'nomor_pic'        => '08'.fake()->numerify('##########'),
                'jabatan_pic'      => fake()->randomElement(['Kepala Bagian IT', 'Kepala Sub Bagian', 'Sekretaris', 'Manajer Operasional']),
                'division'         => $division->value,
                'estimasi_go_live' => Carbon::parse($estYm.'-01')->toDateString(),
                'revenue'          => $revenue,
                'description'      => $description,
                'durasi_bulan'     => $durasi,
                'created_by'       => $sales?->id,
            ];

            if ($stage === ProjectStage::Win) {
                $payload['tanggal_win']     = Carbon::parse($estYm.'-01')->subMonths(2)->toDateString();
                $payload['tanggal_go_live'] = Carbon::parse($estYm.'-01')->toDateString();
                $payload['kontrak_sampai']  = Carbon::parse($estYm.'-01')->addMonths($durasi ?? 12)->toDateString();
                $payload['skema_penagihan'] = fake()->randomElement(['Bulanan', 'Termin 3x', 'One-time']);
            }
            if ($stage === ProjectStage::Lost) {
                $payload['lost_reason'] = 'Kalah harga di tender';
            }

            Project::create($payload);
        }
    }
}
