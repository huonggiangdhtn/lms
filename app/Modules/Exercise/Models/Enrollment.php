<?php

namespace App\Modules\Exercise\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    // Tên bảng nếu khác với mặc định
    protected $table = 'enrollments';

    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'user_id',
        'phancong_id',
        'timespending',       // Thời gian đã học
        'tracnghiem_point',   // Điểm trắc nghiệm
        'tuluan_point',       // Điểm tự luận
        'time_point',         // Điểm thời gian
        'process',            // % hoàn thành khóa học
        'status',             // Trạng thái khóa học
    ];

    // Các trạng thái hợp lệ cho cột `status`
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FINISHED = 'finished';
    const STATUS_REJECTED = 'rejected';

    /**
     * Quan hệ với bảng `users`
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quan hệ với bảng `phancong`
     */
    public function phancong()
    {
        return $this->belongsTo(PhanCong::class, 'phancong_id');
    }

    /**
     * Kiểm tra trạng thái khóa học
     */
    public function isFinished()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
}