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
        Schema::table('orders', function (Blueprint $table) {
            // Campos para integración con Wompi
            $table->string('wompi_enlace_id')->nullable()->after('notes');
            $table->string('wompi_transaccion_id')->nullable()->after('wompi_enlace_id');
            $table->string('wompi_codigo_autorizacion')->nullable()->after('wompi_transaccion_id');
            $table->string('wompi_url_pago')->nullable()->after('wompi_codigo_autorizacion');
            $table->timestamp('wompi_fecha_pago')->nullable()->after('wompi_url_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'wompi_enlace_id',
                'wompi_transaccion_id',
                'wompi_codigo_autorizacion',
                'wompi_url_pago',
                'wompi_fecha_pago'
            ]);
        });
    }
};
