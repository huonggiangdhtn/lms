<?php

// app/Models/Blog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = ['title','slug', 'photo','summary','content','cat_id','user_id','status'];

}


    protected $fillable = [
        'title',
        'slug',
        'cat_id',
        'photo',
        'summary',
        'content',
        'status',
        'user_id',
        'hit'
    ];

    public function category()
    {
        return $this->belongsTo(BlogCat::class, 'cat_id');
    }

    public function resources()
    {
        return $this->hasMany(BlogResource::class);
    }
}
