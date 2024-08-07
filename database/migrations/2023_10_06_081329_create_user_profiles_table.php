<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('gender')->nullable();
            $table->string('image')->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->text('about_me')->nullable();
            $table->text('hobbies')->nullable();
            $table->string('job_title')->nullable();
            $table->text('job_experience')->nullable();
            $table->text('education_history')->nullable();
            $table->text('professional_certification')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
            $table->string('profile_status')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
