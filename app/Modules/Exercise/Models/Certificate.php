<?php

namespace App\Modules\Exercise\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu
     *
     * @var string
     */
    protected $table = 'certificates';

    /**
     * Các trường có thể gán giá trị hàng loạt
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'certificate_number',
        'certificate_name',
        'hocphan_id',
        'issued_date',
    ];

    /**
     * Định nghĩa quan hệ với bảng `users`
     * Một giấy chứng nhận thuộc về một người dùng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Định nghĩa quan hệ với bảng `hocphans`
     * Một giấy chứng nhận thuộc về một học phần
     */
    public function hocphan()
    {
        return $this->belongsTo(Hocphan::class); // Giả sử bạn có model Hocphan
    }
}