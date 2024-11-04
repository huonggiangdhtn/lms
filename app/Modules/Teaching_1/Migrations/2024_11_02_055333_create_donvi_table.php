<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donvi', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Name of the unit
            $table->string('slug')->unique(); // Unique slug for the unit
            $table->unsignedBigInteger('parent_id')->nullable(); // Foreign key to the parent unit
            $table->json('children_id')->nullable(); // Child units in JSON format
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status of the unit
            $table->timestamps();

            // Foreign key declaration for parent_id
            $table->foreign('parent_id')->references('id')->on('donvi')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('donvi');
    }
};