<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    public function index()
    {
        return view('Blog::index'); // Logic for displaying all blog posts
    }

    public function show($id)
    {
        return view('Blog.show', compact('id')); // Logic for displaying a single blog post
    }
}