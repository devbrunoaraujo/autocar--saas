<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->text('gallery')->nullable()->change();
            $table->text('thumbnail')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('gallery', 255)->change(); // Ajuste esse valor conforme o original
            $table->string('thumbnail', 255)->change(); // Mesmo aqui
        });
    }
};
