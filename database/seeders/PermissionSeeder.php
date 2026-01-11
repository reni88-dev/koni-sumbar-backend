<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'Lihat Dashboard', 'group' => 'Dashboard'],
            
            // Data Atlet
            ['name' => 'athletes.view', 'display_name' => 'Lihat Data Atlet', 'group' => 'Data Atlet'],
            ['name' => 'athletes.create', 'display_name' => 'Tambah Atlet', 'group' => 'Data Atlet'],
            ['name' => 'athletes.edit', 'display_name' => 'Edit Atlet', 'group' => 'Data Atlet'],
            ['name' => 'athletes.delete', 'display_name' => 'Hapus Atlet', 'group' => 'Data Atlet'],
            
            // Cabang Olahraga
            ['name' => 'cabor.view', 'display_name' => 'Lihat Cabor & Prestasi', 'group' => 'Cabor & Prestasi'],
            ['name' => 'cabor.create', 'display_name' => 'Tambah Cabor', 'group' => 'Cabor & Prestasi'],
            ['name' => 'cabor.edit', 'display_name' => 'Edit Cabor', 'group' => 'Cabor & Prestasi'],
            ['name' => 'cabor.delete', 'display_name' => 'Hapus Cabor', 'group' => 'Cabor & Prestasi'],
            
            // Event Olahraga
            ['name' => 'events.view', 'display_name' => 'Lihat Event Olahraga', 'group' => 'Event Olahraga'],
            ['name' => 'events.create', 'display_name' => 'Tambah Event', 'group' => 'Event Olahraga'],
            ['name' => 'events.edit', 'display_name' => 'Edit Event', 'group' => 'Event Olahraga'],
            ['name' => 'events.delete', 'display_name' => 'Hapus Event', 'group' => 'Event Olahraga'],
            
            // Monitoring
            ['name' => 'monitoring.view', 'display_name' => 'Lihat Monitoring', 'group' => 'Monitoring'],
            
            // Form Builder
            ['name' => 'forms.view', 'display_name' => 'Lihat Form Builder', 'group' => 'Form Builder'],
            ['name' => 'forms.create', 'display_name' => 'Buat Form', 'group' => 'Form Builder'],
            ['name' => 'forms.edit', 'display_name' => 'Edit Form', 'group' => 'Form Builder'],
            ['name' => 'forms.delete', 'display_name' => 'Hapus Form', 'group' => 'Form Builder'],
            ['name' => 'forms.submit', 'display_name' => 'Isi Form', 'group' => 'Form Builder'],
            
            // Master Data - Users
            ['name' => 'users.view', 'display_name' => 'Lihat Data User', 'group' => 'Master Data'],
            ['name' => 'users.create', 'display_name' => 'Tambah User', 'group' => 'Master Data'],
            ['name' => 'users.edit', 'display_name' => 'Edit User', 'group' => 'Master Data'],
            ['name' => 'users.delete', 'display_name' => 'Hapus User', 'group' => 'Master Data'],
            
            // Master Data - Roles
            ['name' => 'roles.view', 'display_name' => 'Lihat Data Role', 'group' => 'Master Data'],
            ['name' => 'roles.create', 'display_name' => 'Tambah Role', 'group' => 'Master Data'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Role', 'group' => 'Master Data'],
            ['name' => 'roles.delete', 'display_name' => 'Hapus Role', 'group' => 'Master Data'],
            ['name' => 'roles.permissions', 'display_name' => 'Atur Permission Role', 'group' => 'Master Data'],
            
            // Master Data - Cabors
            ['name' => 'cabors.view', 'display_name' => 'Lihat Master Cabor', 'group' => 'Master Data'],
            ['name' => 'cabors.create', 'display_name' => 'Tambah Cabor', 'group' => 'Master Data'],
            ['name' => 'cabors.edit', 'display_name' => 'Edit Cabor', 'group' => 'Master Data'],
            ['name' => 'cabors.delete', 'display_name' => 'Hapus Cabor', 'group' => 'Master Data'],
            
            // Master Data - Education Levels
            ['name' => 'education_levels.view', 'display_name' => 'Lihat Jenjang Pendidikan', 'group' => 'Master Data'],
            ['name' => 'education_levels.create', 'display_name' => 'Tambah Jenjang', 'group' => 'Master Data'],
            ['name' => 'education_levels.edit', 'display_name' => 'Edit Jenjang', 'group' => 'Master Data'],
            ['name' => 'education_levels.delete', 'display_name' => 'Hapus Jenjang', 'group' => 'Master Data'],
            
            // Master Data - Competition Classes
            ['name' => 'competition_classes.view', 'display_name' => 'Lihat Kelas Pertandingan', 'group' => 'Master Data'],
            ['name' => 'competition_classes.create', 'display_name' => 'Tambah Kelas Pertandingan', 'group' => 'Master Data'],
            ['name' => 'competition_classes.edit', 'display_name' => 'Edit Kelas Pertandingan', 'group' => 'Master Data'],
            ['name' => 'competition_classes.delete', 'display_name' => 'Hapus Kelas Pertandingan', 'group' => 'Master Data'],
            
            // Settings
            ['name' => 'settings.view', 'display_name' => 'Lihat Pengaturan', 'group' => 'Pengaturan'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Pengaturan', 'group' => 'Pengaturan'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
