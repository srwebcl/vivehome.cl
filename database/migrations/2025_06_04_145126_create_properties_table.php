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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            // Definición de claves foráneas al final para mejor legibilidad
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('operation_type'); // 'venta', 'arriendo'
            $table->decimal('price', 15, 2); // Ajusta la precisión según necesidad (ej. 15 dígitos totales, 2 decimales)
            $table->string('currency')->default('CLP'); // 'CLP', 'UF'

            $table->string('address')->nullable();
            $table->string('commune')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->decimal('total_area_m2', 8, 2)->nullable();
            $table->decimal('built_area_m2', 8, 2)->nullable();
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->unsignedInteger('parking_lots')->default(0)->nullable();
            $table->unsignedInteger('storage_units')->default(0)->nullable();

            $table->string('status')->default('disponible'); // 'disponible', 'arrendada', 'vendida', 'borrador'
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            // Definición de claves foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // onDelete('cascade') borra propiedades si se borra el usuario
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict'); // onDelete('restrict') previene borrar categoría si tiene propiedades
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
