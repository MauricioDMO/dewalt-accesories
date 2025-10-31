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
            // Eliminar columnas de estado/provincia y código postal
            $table->dropColumn(['customer_state', 'customer_zip']);
            
            // Agregar columnas de departamento y municipio de El Salvador
            $table->string('customer_departamento')->after('customer_city');
            $table->string('customer_municipio')->after('customer_departamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Restaurar columnas originales
            $table->string('customer_state')->nullable()->after('customer_city');
            $table->string('customer_zip')->after('customer_state');
            
            // Eliminar columnas de El Salvador
            $table->dropColumn(['customer_departamento', 'customer_municipio']);
        });
    }
};
