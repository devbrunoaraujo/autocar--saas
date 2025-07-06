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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nome do cliente');
            $table->string('email')->unique()->comment('Email do cliente');
            $table->string('phone')->nullable()->comment('Telefone do cliente');
            $table->string('address')->nullable()->comment('Endereço do cliente');
            $table->string('city')->nullable()->comment('Cidade do cliente');
            $table->string('state')->nullable()->comment('Estado do cliente');
            $table->string('zip_code')->nullable()->comment('CEP do cliente');
            $table->string('country')->default('Brasil')->comment('País do cliente');
            $table->string('document')->nullable()->comment('Documento do cliente (CPF/CNPJ)');
            $table->string('document_type')->default('CPF')->comment('Tipo de documento do cliente (CPF/CNPJ)');
            $table->boolean('is_active')->default(true)->comment('Indica se o cliente está ativo');
            $table->boolean('is_verified')->default(false)->comment('Indica se o cliente foi verificado');
            $table->timestamp('verified_at')->nullable()->comment('Data de verificação do cliente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
