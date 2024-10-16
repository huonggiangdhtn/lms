<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/admin', function () {
//     //xuly 
//     return view('backend.index');
// });
Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin');
Route::get('/admin/login', [App\Http\Controllers\AdminController::class, 'viewLogin'])->name('admin.login');