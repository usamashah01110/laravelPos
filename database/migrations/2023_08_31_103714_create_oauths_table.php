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
        Schema::create('oauths', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('refresh_token')->nullable();
            $table->text('access_token')->nullable();
            $table->string('token_type')->default('bearer');
            $table->integer('expires')->nullable();
            $table->timestamp('refresh_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauths');
    }
};
