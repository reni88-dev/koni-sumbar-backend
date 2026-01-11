<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['code' => 'SD', 'name' => 'Sekolah Dasar', 'order' => 1],
            ['code' => 'SMP', 'name' => 'Sekolah Menengah Pertama', 'order' => 2],
            ['code' => 'SMA', 'name' => 'Sekolah Menengah Atas', 'order' => 3],
            ['code' => 'SMK', 'name' => 'Sekolah Menengah Kejuruan', 'order' => 4],
            ['code' => 'D1', 'name' => 'Diploma 1', 'order' => 5],
            ['code' => 'D2', 'name' => 'Diploma 2', 'order' => 6],
            ['code' => 'D3', 'name' => 'Diploma 3', 'order' => 7],
            ['code' => 'D4', 'name' => 'Diploma 4 / Sarjana Terapan', 'order' => 8],
            ['code' => 'S1', 'name' => 'Sarjana (S1)', 'order' => 9],
            ['code' => 'S2', 'name' => 'Magister (S2)', 'order' => 10],
            ['code' => 'S3', 'name' => 'Doktor (S3)', 'order' => 11],
        ];

        foreach ($levels as $level) {
            EducationLevel::updateOrCreate(
                ['code' => $level['code']],
                array_merge($level, ['is_active' => true])
            );
        }
    }
}
