<?php

namespace Database\Seeders;

use App\Models\Cabor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaborSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabors = [
            ['name' => 'Sepak Bola', 'federation' => 'PSSI Sumbar'],
            ['name' => 'Bulu Tangkis', 'federation' => 'PBSI Sumbar'],
            ['name' => 'Tenis Meja', 'federation' => 'PTMSI Sumbar'],
            ['name' => 'Renang', 'federation' => 'PRSI Sumbar'],
            ['name' => 'Atletik', 'federation' => 'PASI Sumbar'],
            ['name' => 'Basket', 'federation' => 'PERBASI Sumbar'],
            ['name' => 'Voli', 'federation' => 'PBVSI Sumbar'],
            ['name' => 'Tinju', 'federation' => 'PERTINA Sumbar'],
            ['name' => 'Taekwondo', 'federation' => 'TI Sumbar'],
            ['name' => 'Karate', 'federation' => 'FORKI Sumbar'],
            ['name' => 'Pencak Silat', 'federation' => 'IPSI Sumbar'],
            ['name' => 'Judo', 'federation' => 'PJSI Sumbar'],
            ['name' => 'Angkat Besi', 'federation' => 'PABBSI Sumbar'],
            ['name' => 'Panahan', 'federation' => 'PERPANI Sumbar'],
            ['name' => 'Menembak', 'federation' => 'PERBAKIN Sumbar'],
            ['name' => 'Sepak Takraw', 'federation' => 'PSTI Sumbar'],
            ['name' => 'Tenis Lapangan', 'federation' => 'PELTI Sumbar'],
            ['name' => 'Golf', 'federation' => 'PGI Sumbar'],
            ['name' => 'Biliar', 'federation' => 'POBSI Sumbar'],
            ['name' => 'Catur', 'federation' => 'PERCASI Sumbar'],
            ['name' => 'Bridge', 'federation' => 'GABSI Sumbar'],
            ['name' => 'Panjat Tebing', 'federation' => 'FPTI Sumbar'],
            ['name' => 'Layar', 'federation' => 'PORLASI Sumbar'],
            ['name' => 'Dayung', 'federation' => 'PODSI Sumbar'],
            ['name' => 'Kano', 'federation' => 'PPKI Sumbar'],
            ['name' => 'Senam', 'federation' => 'PERSANI Sumbar'],
            ['name' => 'Wushu', 'federation' => 'WI Sumbar'],
            ['name' => 'Anggar', 'federation' => 'IKASI Sumbar'],
            ['name' => 'Gulat', 'federation' => 'PGSI Sumbar'],
            ['name' => 'Hockey', 'federation' => 'FHI Sumbar'],
            ['name' => 'Softball', 'federation' => 'PERBASASI Sumbar'],
            ['name' => 'Baseball', 'federation' => 'PERBASASI Sumbar'],
            ['name' => 'Futsal', 'federation' => 'FFI Sumbar'],
            ['name' => 'E-Sports', 'federation' => 'PBESI Sumbar'],
            ['name' => 'Selam', 'federation' => 'POSSI Sumbar'],
            ['name' => 'Polo Air', 'federation' => 'PRSI Sumbar'],
            ['name' => 'Balap Sepeda', 'federation' => 'ISSI Sumbar'],
            ['name' => 'Motor Sport', 'federation' => 'IMI Sumbar'],
            ['name' => 'Petanque', 'federation' => 'FOPI Sumbar'],
            ['name' => 'Muaythai', 'federation' => 'Muaythai Indonesia Sumbar'],
            ['name' => 'Kick Boxing', 'federation' => 'PERKIKNAS Sumbar'],
            ['name' => 'Tarung Derajat', 'federation' => 'Kodrat Sumbar'],
            ['name' => 'Sambo', 'federation' => 'PERSASI Sumbar'],
        ];

        foreach ($cabors as $cabor) {
            Cabor::updateOrCreate(
                ['name' => $cabor['name']],
                array_merge($cabor, [
                    'slug' => Str::slug($cabor['name']),
                    'is_active' => true,
                ])
            );
        }
    }
}
