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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id'); // ID của nhóm
            $table->unsignedBigInteger('user_id');  // ID của người dùng
            $table->string('role');                  // Vai trò của thành viên trong nhóm
            $table->dateTime('joined_at');          // Thời gian tham gia
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
