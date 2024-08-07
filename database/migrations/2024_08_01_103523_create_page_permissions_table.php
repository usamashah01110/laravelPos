<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('page_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('page_slug'); // The slug of the page the user has access to
            $table->timestamps();

            // Unique constraint to prevent duplicate permissions
            $table->unique(['user_id', 'page_slug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_permissions');
    }
}
