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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount', 8, 2); // Valor do desconto
            $table->enum('type', ['fixed', 'percentage']); // Tipo de desconto
            $table->dateTime('valid_from')->nullable(); // Data inicial de validade
            $table->dateTime('valid_to')->nullable(); // Data final de validade
            $table->integer('usage_limit')->nullable(); // Quantidade máxima de usos
            $table->integer('times_used')->default(0); // Quantas vezes já foi usado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
