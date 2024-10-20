<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Resource\Controllers\ResourceController;
use App\Modules\Resource\Controllers\ResourceLinkTypeController;
use App\Modules\Resource\Controllers\ResourceTypeController;

Route::group(['prefix' => 'admin/', 'as' => 'admin.'], function () {
    
    // Phần quản lý Tài nguyên
    Route::resource('resources', ResourceController::class)->except(['show']); 
    Route::get('resources/{slug}', [ResourceController::class, 'show'])->name('resources.show'); 
    Route::get('resources/search', [ResourceController::class, 'search'])->name('admin.resources.search');

    // Phần quản lý Loại Liên kết Tài nguyên
    Route::resource('resource-link-types', ResourceLinkTypeController::class)->except(['create', 'edit']); 

    // Phần quản lý Loại Tài nguyên
    Route::resource('resource-types', ResourceTypeController::class)->except(['create', 'edit']); 

});