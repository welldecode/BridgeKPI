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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name');
            $table->text('description');
            $table->integer('duration'); 
            $table->integer('trial_period'); 
            $table->double('monthly_prices');
            $table->double('year_prices');
            $table->json('permissions');  
            $table->boolean('stats'); 
            $table->timestamps();
        });  

        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_plan');
            $table->timestamp('start_date')->nullable(); 
            $table->timestamp('end_date')->nullable();  
            $table->timestamp('trial_end_date')->nullable(); 
            $table->enum('stats', ['active', 'expired']);
            $table->timestamps();
        });
        
        Schema::create('plan_payment', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('subscription_id');
            $table->double('amount');
            $table->timestamp('payment_date'); 
            $table->enum('stats', ['pending', 'active', 'canceled']);
            $table->timestamps();
        });

        
        Schema::create('plan_recurring_subscriptions', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('subscription_id'); 
            $table->timestamp('next_billing_date'); 
            $table->boolean('auto_renew');
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('plans');
    }
};
