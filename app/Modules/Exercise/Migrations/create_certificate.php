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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Người nhận giấy chứng nhận
            $table->string('certificate_number')->unique(); // Mã giấy chứng nhận
            $table->string('certificate_name'); // Tên giấy chứng nhận
            $table->unsignedBigInteger('hocphan_id'); // Tên giấy chứng nhận
            $table->date('issued_date'); // Ngày cấp
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

 