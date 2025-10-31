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
            // Eliminar columnas de estado/provincia y código postal solo si existen
            if (Schema::hasColumn('orders', 'customer_state')) {
                $table->dropColumn('customer_state');
            }
            if (Schema::hasColumn('orders', 'customer_zip')) {
                $table->dropColumn('customer_zip');
            }
            
            // Agregar columnas de departamento y municipio de El Salvador solo si no existen
            if (!Schema::hasColumn('orders', 'customer_departamento')) {
                $table->string('customer_departamento')->after('customer_city');
            }
            if (!Schema::hasColumn('orders', 'customer_municipio')) {
                $table->string('customer_municipio')->after('customer_departamento');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Eliminar columnas de El Salvador solo si existen
            if (Schema::hasColumn('orders', 'customer_departamento')) {
                $table->dropColumn('customer_departamento');
            }
            if (Schema::hasColumn('orders', 'customer_municipio')) {
                $table->dropColumn('customer_municipio');
            }
            
            // Restaurar columnas originales solo si no existen
            if (!Schema::hasColumn('orders', 'customer_state')) {
                $table->string('customer_state')->nullable()->after('customer_city');
            }
            if (!Schema::hasColumn('orders', 'customer_zip')) {
                $table->string('customer_zip')->after('customer_state');
            }
        });
    }
};
