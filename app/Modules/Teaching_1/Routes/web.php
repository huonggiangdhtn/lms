<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Teaching_1\Controllers\TeacherController;

Route::group(['prefix' => 'admin/', 'as' => 'admin.'], function () {

    // teacher section
    Route::resource('teacher', TeacherController::class);
    // Route::post('teacher_status', [teacherController::class, 'teacherStatus'])->name('teacher.status');
    // Route::get('teacher_search', [teacherController::class, 'teacherSearch'])->name('teacher.search');

});
