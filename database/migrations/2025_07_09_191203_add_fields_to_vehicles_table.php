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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('color')->nullable();
            $table->string('transmission')->nullable(); // e.g., automatic, manual
            $table->string('mileage')->nullable(); // e.g., 15000 km
            $table->string('renavam')->nullable(); // Vehicle Identification Number
            $table->string('crv')->nullable(); // Certificate of Registration and Licensing
            $table->string('chassis_number')->nullable(); // e.g., 9BABC123456789012
            $table->string('license_plate')->nullable(); // e.g., ABC-1234
            $table->string('notes')->nullable(); // Additional notes about the vehicle
            $table->boolean('is_active')->default(true); // To mark if the vehicle is active or not
            $table->boolean('is_featured')->default(true);
            $table->string('thumbnail')->nullable(); // Path to the vehicle's thumbnail image
            $table->string('gallery')->nullable(); // Path to the vehicle's gallery images
            $table->softDeletes(); // For soft delete functionality
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            //
        });
    }
};
