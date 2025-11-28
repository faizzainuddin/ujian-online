<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherQuestionController;
use App\Http\Controllers\TeacherStudentController;
use App\Http\Controllers\TeacherScheduleController;
use Illuminate\Support\Facades\Route;
use Whoops\Run;

Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::get('/captcha/refresh', function () {
    return captcha_src('flat');
})->name('captcha.refresh');

Route::middleware('admin.auth')->group(function () {
    Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');

    Route::controller(AdminUserController::class)->group(function () {
        Route::get('/admin/users', 'index')->name('admin.users.index');
        Route::get('/admin/users/data', 'data')->name('admin.users.data');
        Route::get('/admin/users/create', 'create')->name('admin.users.create');
        Route::post('/admin/users', 'store')->name('admin.users.store');
        Route::get('/admin/users/{role}/{id}/edit', 'edit')->name('admin.users.edit');
        Route::put('/admin/users/{role}/{id}', 'update')->name('admin.users.update');
        Route::delete('/admin/users/{role}/{id}', 'destroy')->name('admin.users.destroy');
    });
});

Route::middleware('teacher.auth')->group(function () {
    Route::view('/guru/dashboard', 'guru.dashboard')->name('teacher.dashboard');

    Route::controller(TeacherQuestionController::class)
        ->prefix('guru/questions')
        ->name('teacher.questions.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/builder', 'builder')->name('builder');
            Route::post('/', 'store')->name('store');
            Route::put('/{questionSet}', 'update')->name('update');
            Route::get('/{questionSet}/edit', 'builder')->name('edit');
            Route::delete('/{questionSet}', 'destroy')->name('destroy');
        });
    
    Route::get('/guru/siswa', [TeacherStudentController::class, 'index'])
        ->name('teacher.students.index');

    Route::prefix('teacher')->group(function () {
        Route::get('/jadwal', [TeacherScheduleController::class, 'index'])->name('teacher.schedules.index');
        Route::get('/jadwal/create', [TeacherScheduleController::class, 'create'])->name('teacher.schedules.create');
        Route::post('/jadwal/store', [TeacherScheduleController::class, 'store'])->name('teacher.schedules.store');
        Route::delete('/jadwal/{id}', [TeacherScheduleController::class, 'destroy'])->name('teacher.schedules.destroy'); 
    });

});

Route::middleware('student.auth')->group(function () {
    Route::get('/siswa/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/ujian-siswa', [StudentDashboardController::class, 'exams'])->name('student.exams');
    Route::get('/siswa/nilai/{semester?}', [StudentDashboardController::class, 'nilai'])->name('student.nilai');
});

