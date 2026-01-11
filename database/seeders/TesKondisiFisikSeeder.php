<?php

namespace Database\Seeders;

use App\Models\FormTemplate;
use App\Models\FormSection;
use App\Models\FormField;
use App\Models\FormFieldOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TesKondisiFisikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates the "Tes Kondisi Fisik" form template with all sections, fields, and options.
     */
    public function run(): void
    {
        // Check if template already exists
        if (FormTemplate::where('slug', 'tes-kondisi-fisik')->exists()) {
            $this->command->info('Form template "Tes Kondisi Fisik" already exists. Skipping...');
            return;
        }

        // Create the form template
        $template = FormTemplate::create([
            'name' => 'Tes Kondisi Fisik',
            'slug' => 'tes-kondisi-fisik',
            'description' => 'Form untuk mencatat hasil tes kondisi fisik atlet',
            'reference_model' => 'athlete',
            'reference_display_field' => 'name',
            'is_active' => true,
            'created_by' => 1,
        ]);

        // ========================================
        // Section 1: Data Diri
        // ========================================
        $sectionDataDiri = $template->sections()->create([
            'title' => 'Data Diri',
            'type' => 'normal',
            'order' => 0,
        ]);

        // Field: Nama (auto-fill dari model athlete)
        $sectionDataDiri->fields()->create([
            'label' => 'Nama',
            'name' => 'name',
            'type' => 'model_reference',
            'order' => 0,
            'data_source_model' => null, // inherits from template.reference_model
            'reference_field' => null, // uses field.name as default
        ]);

        // Field: Tempat Lahir (auto-fill dari model athlete)
        $sectionDataDiri->fields()->create([
            'label' => 'Tempat Lahir',
            'name' => 'birth_place',
            'type' => 'model_reference',
            'order' => 1,
            'data_source_model' => null,
            'reference_field' => null,
        ]);

        // Field: Jenis Kelamin (radio button)
        $fieldJenkel = $sectionDataDiri->fields()->create([
            'label' => 'Jenis Kelamin',
            'name' => 'jenkel',
            'type' => 'radio',
            'order' => 2,
        ]);

        // Options for Jenis Kelamin
        $fieldJenkel->options()->createMany([
            ['label' => 'Laki-laki', 'value' => 'laki-laki', 'order' => 0],
            ['label' => 'Perempuan', 'value' => 'perempuan', 'order' => 1],
        ]);

        // Field: Cabang Olahraga (auto-fill dari model cabor)
        $sectionDataDiri->fields()->create([
            'label' => 'Cabang Olahraga',
            'name' => 'cabor',
            'type' => 'model_reference',
            'order' => 3,
            'data_source_model' => 'cabor', // different model
            'reference_field' => 'name',
        ]);

        // Field: Tinggi Badan (auto-fill dari model athlete)
        $sectionDataDiri->fields()->create([
            'label' => 'Tinggi Badan',
            'name' => 'tinggi',
            'type' => 'model_reference',
            'order' => 4,
            'data_source_model' => 'athlete',
            'reference_field' => 'height',
        ]);

        // Field: Berat Badan (auto-fill dari model athlete)
        $sectionDataDiri->fields()->create([
            'label' => 'Berat Badan',
            'name' => 'berat',
            'type' => 'model_reference',
            'order' => 5,
            'data_source_model' => 'athlete',
            'reference_field' => 'weight',
        ]);

        // Field: IMT
        $sectionDataDiri->fields()->create([
            'label' => 'IMT',
            'name' => 'imt',
            'type' => 'text',
            'order' => 6,
        ]);

        // ========================================
        // Section 2: Hasil Tes Kondisi Fisik
        // ========================================
        $sectionHasilTes = $template->sections()->create([
            'title' => 'Hasil Tes Kondisi Fisik',
            'type' => 'table',
            'order' => 1,
        ]);

        // Field: Sit Up
        $sectionHasilTes->fields()->create([
            'label' => 'Sit Up',
            'name' => 'sit up',
            'type' => 'text',
            'group_label' => 'KEKUATAN',
            'sub_label' => 'Otot Perut',
            'technique' => 'Sit Up',
            'unit' => 'Kali',
            'is_required' => true,
            'order' => 0,
        ]);

        // Field: Push Up
        $sectionHasilTes->fields()->create([
            'label' => 'Push Up',
            'name' => 'push up',
            'type' => 'text',
            'group_label' => 'KEKUATAN',
            'sub_label' => 'Otot Lengan dan Bahu',
            'technique' => 'Push Up',
            'unit' => 'Kali',
            'is_required' => true,
            'order' => 1,
        ]);

        // Field: Pull Up
        $sectionHasilTes->fields()->create([
            'label' => 'Pull Up',
            'name' => 'pull up',
            'type' => 'text',
            'group_label' => 'KEKUATAN',
            'sub_label' => 'Otot Seluruh Tubuh Atas',
            'technique' => 'Pull up',
            'unit' => 'Kali',
            'is_required' => true,
            'order' => 2,
        ]);

        // Field: Medicine Ball Put
        $sectionHasilTes->fields()->create([
            'label' => 'Medicine Ball Put',
            'name' => 'medicine ball put',
            'type' => 'text',
            'group_label' => 'POWER (Daya Ledak)',
            'sub_label' => 'Otot Lengan dan Bahu',
            'technique' => 'Medicine Ball Put',
            'unit' => 'cm',
            'is_required' => true,
            'order' => 3,
        ]);

        // Field: Vertical Jump
        $sectionHasilTes->fields()->create([
            'label' => 'Vertical Jumps',
            'name' => 'vertical jump',
            'type' => 'text',
            'group_label' => 'POWER (Daya Ledak)',
            'sub_label' => 'Otot Tungkai',
            'technique' => 'Vertical Jumps',
            'unit' => 'cm',
            'is_required' => true,
            'order' => 4,
        ]);

        // Field: Lari 60 meter
        $sectionHasilTes->fields()->create([
            'label' => 'Lari 60 meter',
            'name' => 'lari 60 meter',
            'type' => 'text',
            'group_label' => 'KECEPATAN',
            'sub_label' => 'Kecepatan Lari',
            'technique' => '60 meter',
            'unit' => 'detik',
            'is_required' => true,
            'order' => 5,
        ]);

        // Field: Illinois Test
        $sectionHasilTes->fields()->create([
            'label' => 'Illinois Test',
            'name' => 'illinois test',
            'type' => 'text',
            'group_label' => 'KELINCAHAN',
            'sub_label' => 'Kelincahan Seluruh Tubuh',
            'technique' => 'Illinois Test',
            'unit' => 'detik',
            'is_required' => true,
            'order' => 6,
        ]);

        // Field: Sit and Reach
        $sectionHasilTes->fields()->create([
            'label' => 'Sit and Reach',
            'name' => 'sit and reach',
            'type' => 'text',
            'group_label' => 'FLEKSIBILITAS',
            'sub_label' => 'Fleksibilitas',
            'technique' => 'Sit and Reach',
            'unit' => 'cm',
            'is_required' => true,
            'order' => 7,
        ]);

        // Field: Bleep Test
        $sectionHasilTes->fields()->create([
            'label' => 'Bleep Test',
            'name' => 'bleep test',
            'type' => 'text',
            'group_label' => 'DAYA TAHAN UMUM',
            'sub_label' => 'Daya Tahan Umum',
            'technique' => 'Bleep Test',
            'unit' => null,
            'is_required' => true,
            'order' => 8,
        ]);

        $this->command->info('Form template "Tes Kondisi Fisik" created successfully!');
        $this->command->info("- Template ID: {$template->id}");
        $this->command->info("- Sections: 2 (Data Diri, Hasil Tes Kondisi Fisik)");
        $this->command->info("- Total Fields: 16");
    }
}
