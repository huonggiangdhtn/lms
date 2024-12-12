<?php

namespace App\Modules\Teaching_1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'name',
        'mssv',
        'khoa',
        'donvi_id',
        'nganh_id',
        'user_id',
        'status',
        'slug',
    ];

    // Liên kết với model Donvi
    public function donvi()
    {
        return $this->belongsTo(\App\Modules\Teaching_1\Models\Donvi::class, 'donvi_id');
    }

    // Liên kết với model Nganh
    public function nganh()
    {
        return $this->belongsTo(\App\Modules\Teaching_1\Models\Nganh::class, 'nganh_id');
    }

    // Liên kết với model User
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
