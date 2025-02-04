<?php

namespace App\Modules\Exercise\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HocPhan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'photo',
        'code',
        'content',
        'summary',
        'tinchi',
        'hinhthucthi',
        'user_id',
        'time_point',
        'tracnghiem_point',
        
        'tuluan_point'
    ];
}
