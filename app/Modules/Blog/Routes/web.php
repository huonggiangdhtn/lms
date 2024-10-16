<?php

use Illuminate\Support\Facades\Route;

// Define routes here
 
use App\Modules\Blog\Controllers\BlogController;

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');