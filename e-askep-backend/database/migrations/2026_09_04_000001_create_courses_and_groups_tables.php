<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 2. TABEL MATA KULIAH / STASE KURIKULUM
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('program_study', 100);
            $table->string('academic_year', 20);
            $table->timestamps();
        });

        // 3. RELASI KELOMPOK PRAKTIK & BIMBINGAN
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('mentor_dosen_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('group_name', 50);
            $table->timestamps();

            $table->index(['student_id', 'mentor_dosen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_groups');
        Schema::dropIfExists('courses');
    }
};
