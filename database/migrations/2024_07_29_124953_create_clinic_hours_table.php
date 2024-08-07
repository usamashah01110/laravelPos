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
        Schema::create('clinic_hours', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clinics_id')->unsigned();
            $table->dateTime('start_time'); // Use dateTime instead of date
            $table->dateTime('end_time'); // Use dateTime instead of date
            $table->string('day_of_week');
            $table->boolean('is_open');
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_hours');
    }
};
