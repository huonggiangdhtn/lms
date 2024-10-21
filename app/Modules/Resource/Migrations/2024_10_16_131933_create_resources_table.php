<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('file_name');
            $table->string('file_type');
            $table->string('file_size');
            $table->string('path');
            $table->string('url');
            $table->unsignedBigInteger('resource_type_id');
            $table->unsignedBigInteger('resource_link_type_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resources');
    }
}
