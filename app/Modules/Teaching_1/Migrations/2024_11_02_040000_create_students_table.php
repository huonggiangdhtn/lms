<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.  
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id(); // Tự tăng ID
            $table->string('mssv')->unique(); // Mã số sinh viên (unique)
            $table->string('name'); // Tên sinh viên
            $table->unsignedBigInteger('donvi_id'); // Liên kết tới bảng đơn vị
            $table->unsignedBigInteger('nganh_id'); // Liên kết tới bảng ngành
            $table->string('khoa'); // Khóa
            $table->enum('status', ['đang học', 'thôi học', 'tốt nghiệp']); // Tình trạng
            $table->unsignedBigInteger('user_id'); // ID người dùng
            $table->string('slug')->unique(); // Slug (unique)
            $table->timestamps(); // Created_at và Updated_at

            // Ràng buộc khóa ngoại
            $table->foreign('donvi_id')
                ->references('id')
                ->on('donvi')
                ->onDelete('cascade'); 

            $table->foreign('nganh_id')
                ->references('id')
                ->on('nganh')
                ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
