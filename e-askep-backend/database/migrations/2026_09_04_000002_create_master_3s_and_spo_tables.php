<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 4. MASTER SDKI
        Schema::create('master_sdki', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('title');
            $table->string('category', 100);
            $table->string('sub_category', 100);
            $table->json('major_signs')->nullable();
            $table->json('minor_signs')->nullable();
            $table->timestamps();

            $table->index(['code', 'category']);
        });

        // 5. MASTER SLKI
        Schema::create('master_slki', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('title');
            $table->json('indicators')->nullable();
            $table->timestamps();
        });

        // 6. MASTER SIKI
        Schema::create('master_siki', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('title');
            $table->json('actions')->nullable();
            $table->timestamps();
        });

        // 7. MASTER KATALOG PROSEDUR SPO PRAKTIKUM
        Schema::create('master_spo_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('sub_cpmk_reference', 50);
            $table->string('domain_category', 100);
            $table->string('procedure_name');
            $table->timestamps();

            $table->index(['course_id', 'sub_cpmk_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_spo_procedures');
        Schema::dropIfExists('master_siki');
        Schema::dropIfExists('master_slki');
        Schema::dropIfExists('master_sdki');
    }
};
