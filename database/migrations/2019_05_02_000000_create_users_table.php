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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->integer('google_id')->nullable();
            $table->integer('facebook_id')->nullable();
            $table->string('role', 100)->default('user')->nullable();
            $table->boolean('email_verified')->default(0)->nullable();
            $table->boolean('phone_verified')->default(0)->nullable();
            $table->boolean('qr_verified')->default(0)->nullable();
            $table->string('status', 50)->default('pending')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('verification_token')->nullable();
            $table->string('reset_token')->nullable();
            $table->date('reset_token_expires')->nullable();
            $table->date('password_reset')->nullable();
            $table->softDeletes()->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
