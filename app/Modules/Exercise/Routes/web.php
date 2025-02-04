<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Exercise\Controllers\TracNghiemCauHoiController;
use App\Modules\Exercise\Controllers\TuLuanCauHoiController;
use App\Modules\Exercise\Controllers\BoDeTracNghiemController;
use App\Modules\Exercise\Controllers\BoDeTuLuanController;
use App\Modules\Exercise\Controllers\HocPhanController;
use App\Modules\Exercise\Controllers\TracnghiemFrontController;
// Định nghĩa route cho module câu hỏi
Route::prefix('admin/tuluancauhoi')->name('admin.tuluancauhoi.')->group(function () {
    Route::get('/', [TuluancauhoiController::class, 'index'])->name('index');
    Route::get('/create', [TuluancauhoiController::class, 'create'])->name('create');
    Route::post('/store', [TuluancauhoiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [TuluancauhoiController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [TuluancauhoiController::class, 'update'])->name('update');
    Route::delete('/{id}', [TuluancauhoiController::class, 'destroy'])->name('destroy');
    Route::get('/search', [TuluancauhoiController::class, 'search'])->name('search');
    Route::get('/{id}', [TuluancauhoiController::class, 'show'])->name('show');
});


Route::group(['prefix' => 'admin/', 'as' => 'admin.'], function () {

     // Route::resource('recommend', RecommendController::class);
    Route::resource('hocphan', HocPhanController::class);
    // Route::get('recommend', [RecommendController::class, 'index'])->name('recommend.index');
    Route::get('hocphan_search', [HocPhanController::class, 'moduleSearch'])->name('hocphan.search');
    Route::get('hocphan_sinhvien/{id}', [HocPhanController::class, 'xemsvdk'])->name('hocphan.sinhvien');
    // Hiển thị danh sách chương trình đào tạo
    Route::get('chuong_trinh_dao_tao', [ChuongTrinhDaoTaoController::class, 'index'])->name('chuong_trinh_dao_tao.index');


    Route::resource('tracnghiemcauhoi', TracNghiemCauHoiController::class);
    // Route::get('hocphan_search', [App\Modules\Exercise\Controllers\::class, 'moduleSearch'])->name('hocphan.search');
    Route::delete('/admin/tracnghiemcauhoi/{tracnghiemcauhoiId}/resource/{resourceId}', [TracNghiemCauHoiController::class, 'removeResource'])->name('tracnghiemcauhoi.removeResource');

    Route::resource('tuluancauhoi', TuLuanCauHoiController::class);
    Route::delete('/admin/tuluancauhoi/{tuluancauhoiId}/resource/{resourceId}', [TuLuanCauHoiController::class, 'removeResource'])->name('tuluancauhoi.removeResource');
    
    // Bộ đề trắc nghiệm
    Route::resource('bode_tracnghiem', BodetracnghiemController::class);
    // Bộ đề tu luan
    Route::resource('bode_tuluans', BoDeTuLuanController::class);
});


Route::group(['prefix' => '', 'as' => 'front.'], function () {
Route::get('/hocphan', [TracnghiemFrontController::class, 'index'])->name('hocphan.index');
Route::get('/dangky_hocphan/{hocphan_id}', [TracnghiemFrontController::class, 'Dangky'])->name('hocphan.dangky');
Route::get('/lophocphan/{hocphan_id}', [TracnghiemFrontController::class, 'XemEnroll'])->name('enroll.index');
Route::get('/chiase/{id}', [TracnghiemFrontController::class, 'share'])->name('tracnghiem.share');
Route::get('/ketquatracnghiem/{id}', [TracnghiemFrontController::class, 'viewketqua'])->name('tracnghiem.result');
Route::post('/share-certificate', [TracnghiemFrontController::class, 'sharecertificate'])->name('tracnghiem.sharecertificate');
Route::get('/tracnghiem/{id}/practice', [TracnghiemFrontController::class, 'Luyentaptracnghiem'])->name('tracnghiem.practice');
Route::post('/tracnghiem/{bodeId}/submit', [TracnghiemFrontController::class, 'submit'])->name('tracnghiem.submit');
 
Route::get('/chungnhan_hocphan/{id}', [TracnghiemFrontController::class, 'viewCertificate'])->name('hocphan.certificate');

});