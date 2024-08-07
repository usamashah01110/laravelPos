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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            
            $table->string('twillio')->nullable();
            $table->string('twillio_client_id')->nullable();
            $table->string('twillio_secret_id')->nullable();
            $table->string('twoFactor')->nullable();
            $table->string('twoFactor_type')->nullable();
            // $table->integer('paymeny_method')->nullable();
            $table->string('payment_method_type')->nullable();
            $table->string('payment_method_client_id')->nullable();
            $table->string('payment_method_secret_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
