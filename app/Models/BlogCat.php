<?php

// app/Models/BlogCat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCat extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'photo',
        'status'
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'cat_id');
    }
}
