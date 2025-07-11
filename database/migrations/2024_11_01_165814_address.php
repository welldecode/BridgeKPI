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
          
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();  
            $table->unsignedBigInteger('user_id');
            $table->string('address_line1'); 
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->integer('zip_code');
            $table->string('country'); 
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();   
            $table->string('name'); 
            $table->double('price')->nullable(); 
            $table->string('estimated_delivery')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('shipping_methods');
    }
};
