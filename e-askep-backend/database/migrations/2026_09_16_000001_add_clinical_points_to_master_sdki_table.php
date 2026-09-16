<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perluasan aditif master SDKI agar dapat menampung poin-poin klinis lengkap
 * tiap diagnosis: definisi baku, penyebab (etiologi), dan faktor risiko.
 *
 * Kolom `faktor_risiko` diperlukan karena diagnosis bertipe "Risiko ..." pada
 * SDKI tidak memiliki tanda/gejala mayor-minor, melainkan daftar faktor risiko.
 * Seluruh kolom nullable sehingga tidak merusak data maupun kode yang ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_sdki', function (Blueprint $table) {
            $table->text('definition')->nullable()->after('sub_category');
            $table->json('risk_factors')->nullable()->after('minor_signs');
            $table->json('causes')->nullable()->after('risk_factors');
            $table->string('source_url')->nullable()->after('causes');
        });
    }

    public function down(): void
    {
        Schema::table('master_sdki', function (Blueprint $table) {
            $table->dropColumn(['definition', 'risk_factors', 'causes', 'source_url']);
        });
    }
};
