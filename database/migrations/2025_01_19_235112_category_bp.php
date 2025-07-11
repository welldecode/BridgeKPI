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
        //
      Schema::create('category_bp', function (Blueprint $table) {
        $table->id(); 
        $table->string('name');   
        $table->unsignedBigInteger('relation'); 
        $table->unsignedBigInteger('nivel'); 
        $table->timestamps();
    });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('category_bp');
    }
};
