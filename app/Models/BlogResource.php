<?php

// app/Models/BlogResource.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'resource_id',
        'resource_code'
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
