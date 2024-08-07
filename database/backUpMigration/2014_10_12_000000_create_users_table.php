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
//            $table->enum(['role')->default(2); // Admin=1, Subscriber=2
            $table->enum('role', ['Admin', 'Subscriber'])->default('Subscriber');
            $table->boolean('email_verified')->default(0);
            $table->boolean('phone_verified')->default(0);
//            $table->tinyInteger('status')->default(1); // Pending=1, Active=2, Waiting=3
            $table->enum('status', ['Pending', 'Waiting', 'Active'])->default('Pending');
            $table->text('verification_token')->nullable();
            $table->string('reset_token')->nullable();
            $table->date('reset_token_expires')->nullable();
            $table->date('password_reset')->nullable();

//            $table->rememberToken();
            $table->text('remember_token')->nullable();
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
