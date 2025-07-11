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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->float('sub_total');
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->float('coupon')->nullable();
            $table->float('total_amount');
            $table->integer('quantity');
            $table->enum('payment_method',['pix','master', 'amex', 'boleto'])->default('pix');
            $table->enum('payment_status',['paid','unpaid','pending', 'rejected'])->default('unpaid');
            $table->enum('status',['new','process','delivered','cancel'])->default('new');
            $table->string('payment_order'); 
            $table->string('first_name');
            $table->string('last_name'); 
            $table->string('email');
            $table->string('external_reference');
            $table->string('phone');
            $table->string('country');
            $table->string('post_code')->nullable(); 
            $table->text('address2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('orders'); 
    }
};
