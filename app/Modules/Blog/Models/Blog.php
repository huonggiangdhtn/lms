<?php

namespace App\Modules\Blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Đảm bảo import mô hình User

class Blog extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'photo', 'summary', 'content', 'cat_id', 'user_id', 'status'];

    // Định nghĩa mối quan hệ với mô hình User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
