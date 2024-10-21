<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Recommend\Controllers\RecommendController;

Route::group(['prefix' => 'admin/', 'as' => 'admin.'], function() {
    Route::get('recommend/{userId}', [RecommendController::class, 'index'])->name('recommend.index');
});