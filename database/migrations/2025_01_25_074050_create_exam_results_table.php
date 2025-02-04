<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamResultsTable extends Migration
{
    public function up()
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('selected_answer_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('trac_nghiem_cauhois')->onDelete('cascade');
            $table->foreign('selected_answer_id')->references('id')->on('trac_nghiem_dapans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_results');
    }
}
