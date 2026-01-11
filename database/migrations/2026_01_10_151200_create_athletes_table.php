<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabor_id')->nullable()->constrained()->nullOnDelete();
            
            // Basic Info
            $table->string('name');
            $table->string('nik', 16)->nullable();                    // NIK 16 digit
            $table->string('no_kk', 16)->nullable();                  // No Kartu Keluarga
            $table->string('competition_class')->nullable();          // Kelas Pertandingan
            
            // Personal Info
            $table->string('birth_place')->nullable();                // Tempat Lahir
            $table->date('birth_date')->nullable();                   // Tanggal Lahir
            $table->enum('gender', ['male', 'female'])->nullable();   // Jenis Kelamin
            $table->string('religion')->nullable();                   // Agama
            $table->text('address')->nullable();                      // Alamat Rumah
            $table->string('education')->nullable();                  // Pendidikan Terakhir
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable(); // Golongan Darah
            $table->string('occupation')->nullable();                 // Pekerjaan
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('hobby')->nullable();                      // Hobi
            
            // Physical Info
            $table->integer('height')->nullable();                    // Tinggi Badan (cm)
            $table->decimal('weight', 5, 2)->nullable();              // Berat Badan (kg)
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // Career Info
            $table->year('career_start_year')->nullable();            // Tahun Mulai Karir
            $table->text('injury_illness_history')->nullable();       // Riwayat Cedera & Penyakit
            
            // Achievements (JSON arrays)
            $table->json('top_achievements')->nullable();             // 3 Prestasi Tertinggi
            $table->json('provincial_achievements')->nullable();      // Prestasi Tingkat Provinsi
            $table->json('national_achievements')->nullable();        // Prestasi Tingkat Nasional
            $table->json('international_achievements')->nullable();   // Prestasi Internasional
            
            // Photo
            $table->string('photo')->nullable();                      // Path to photo
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
