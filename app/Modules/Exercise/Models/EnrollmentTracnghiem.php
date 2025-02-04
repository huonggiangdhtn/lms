<?php

namespace App\Modules\Exercise\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Exercise\Models\Bodetracnghiem;

class EnrollmentTracnghiem extends Model
{
    use HasFactory;

    protected $table = 'enrollment_tracnghiems';

    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'user_id',
        'enroll_id',
        'bode_id',
        'point',
        'time',
        'questions', // JSON chứa danh sách câu hỏi và điểm tương ứng
    ];

    // Tự động chuyển đổi trường `questions` thành mảng khi lấy dữ liệu
    protected $casts = [
        'questions' => 'array',
    ];

    /**
     * Thêm câu hỏi vào danh sách `questions`.
     *
     * @param int $cauhoi_id
     * @param int $point
     * @return void
     */
    public function addQuestion(int $cauhoi_id, int $point)
    {
        $questions = $this->questions ?? []; // Lấy danh sách câu hỏi hiện tại
        $questions[] = [
            'cauhoi_id' => $cauhoi_id,
            'point' => $point,
        ];

        $this->questions = $questions; // Cập nhật trường `questions`
        $this->save(); // Lưu lại thay đổi
    }

    /**
     * Lấy danh sách câu hỏi.
     *
     * @return array
     */
    public function getQuestions()
    {
        return $this->questions ?? [];
    }

    /**
     * Lấy tổng điểm của tất cả câu hỏi trong `questions`.
     *
     * @return int
     */
    public function getTotalPoints()
    {
        return collect($this->questions)->sum('point');
    }

    /**
     * Quan hệ với bảng `users`.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quan hệ với bảng `enrollments`.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enroll_id');
    }

    /**
     * Quan hệ với bảng `bodes` (bộ đề).
     */
    public function bode()
    {
        return $this->belongsTo(\App\Modules\Exercise\Models\Bodetracnghiem::class, 'bode_id');
    }
}