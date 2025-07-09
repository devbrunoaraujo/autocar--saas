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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type'); // cars, motorcycles, trucks
            $table->integer('brand_id');
            $table->string('brand_name');
            $table->integer('model_id');
            $table->string('model_name');
            $table->string('year_id');
            $table->string('year_name');
            $table->string('fuel')->nullable();
            $table->string('fuel_acronym')->nullable();
            $table->string('fipe_price')->nullable();
            $table->string('month_reference')->nullable();
            $table->timestamps();

            // Índices para melhor performance
            $table->index(['vehicle_type', 'brand_id', 'model_id']);
            $table->index(['vehicle_type', 'brand_id']);
            $table->index('vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
