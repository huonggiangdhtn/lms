<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('resources', function (Blueprint $table) {
        $table->string('tags')->nullable(); // Add this line
    });
}

public function down()
{
    Schema::table('resources', function (Blueprint $table) {
        $table->dropColumn('tags'); // Add this line
    });
}

};
