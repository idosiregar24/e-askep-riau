<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 8. ENTITAS UTAMA SESI ASUHAN KASUS
        Schema::create('care_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('mentor_dosen_id')->constrained('users')->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('medical_record_no', 100)->nullable();
            $table->string('age', 50);
            $table->enum('gender', ['L', 'P']);
            $table->enum('triage_category', ['merah', 'kuning', 'hijau', 'hitam'])->nullable();
            $table->enum('status', ['draft', 'submitted', 'need_revision', 'approved_graded', 'archived'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['mentor_dosen_id', 'status']);
        });

        // 9. DATA PENGKAJIAN KLINIS ADAPTIF (JSON)
        Schema::create('care_session_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_session_id')->constrained('care_sessions')->cascadeOnDelete();
            $table->enum('stage_type', ['kgd', 'kdm', 'kmb']);
            $table->json('assessment_payload');
            $table->timestamps();
        });

        // 10. ANALISA DATA & PERENCANAAN 3S
        Schema::create('nursing_care_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_session_id')->constrained('care_sessions')->cascadeOnDelete();
            $table->foreignId('sdki_id')->nullable()->constrained('master_sdki')->nullOnDelete();
            $table->foreignId('slki_id')->nullable()->constrained('master_slki')->nullOnDelete();
            $table->foreignId('siki_id')->nullable()->constrained('master_siki')->nullOnDelete();
            $table->text('subjective_data');
            $table->text('objective_data');
            $table->text('etiology');
            $table->text('custom_outcome_targets')->nullable();
            $table->text('custom_interventions')->nullable();
            $table->integer('priority_order')->default(1);
            $table->timestamps();
        });

        // 11. LOGBOOK CHECKLIST TINDAKAN & E-PARAF
        Schema::create('care_procedure_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_session_id')->constrained('care_sessions')->cascadeOnDelete();
            $table->foreignId('procedure_id')->constrained('master_spo_procedures')->cascadeOnDelete();
            $table->boolean('is_performed')->default(false);
            $table->dateTime('performed_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['care_session_id', 'is_verified']);
        });

        // 12. PEMANTAUAN TTV BERKALA (REASSESSMENT)
        Schema::create('vital_sign_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_session_id')->constrained('care_sessions')->cascadeOnDelete();
            $table->time('recorded_at');
            $table->string('blood_pressure', 30)->nullable();
            $table->string('heart_rate', 30)->nullable();
            $table->string('respiratory_rate', 30)->nullable();
            $table->string('spo2', 20)->nullable();
            $table->string('temperature', 20)->nullable();
            $table->string('gcs_score', 20)->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->timestamps();
        });

        // 13. EVALUASI DAN SERAH TERIMA (SBAR & SOAP)
        Schema::create('evaluations_and_handovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_session_id')->constrained('care_sessions')->cascadeOnDelete();
            $table->enum('format_type', ['SBAR', 'SOAP']);
            $table->json('payload');
            $table->timestamps();
        });

        // 14. PENILAIAN RUBRIK & CATATAN REVIEW PEMBIMBING
        Schema::create('session_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_session_id')->constrained('care_sessions')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $table->text('revision_notes')->nullable();
            $table->json('rubric_scores')->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('signature_snapshot_url')->nullable();
            $table->dateTime('reviewed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_reviews');
        Schema::dropIfExists('evaluations_and_handovers');
        Schema::dropIfExists('vital_sign_monitorings');
        Schema::dropIfExists('care_procedure_logs');
        Schema::dropIfExists('nursing_care_plans');
        Schema::dropIfExists('care_session_assessments');
        Schema::dropIfExists('care_sessions');
    }
};
