<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\SchoolAdminController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route(auth()->check() ? 'dashboard' : 'login'));

Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.portal')->name('register');
    Route::post('/register', [AccountController::class, 'register']);
    Route::view('/login', 'auth.portal')->name('login');
    Route::post('/login', [AccountController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');

    Route::middleware('can:student')->controller(StudentPortalController::class)->group(function () {
        Route::get('/profile', 'profile')->name('profile');
        Route::put('/profile', 'updateProfile')->name('profile.update');
        Route::get('/subjects', 'subjects')->name('subjects');
        Route::get('/enrollments', 'enrollments')->name('enrollments');
        Route::post('/enrollments', 'enroll')->name('enrollments.store');
        Route::get('/grades', 'grades')->name('grades');
    });

    Route::middleware('can:teacher')->prefix('teacher')->name('teacher.')->controller(GradingController::class)->group(function () {
        Route::get('/subjects', 'subjects')->name('subjects');
        Route::get('/subjects/{subject}', 'subject')->name('subjects.show');
        Route::put('/grades/{enrollment}', 'update')->name('grades.update');
    });

    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::controller(SchoolAdminController::class)->group(function () {
            Route::get('/students', 'students')->name('students');
            Route::put('/students/{student}', 'updateStudent')->name('students.update');
            Route::get('/subjects', 'subjects')->name('subjects');
            Route::post('/subjects', 'storeSubject')->name('subjects.store');
            Route::put('/subjects/{subject}', 'updateSubject')->name('subjects.update');
            Route::get('/enrollments', 'enrollments')->name('enrollments');
            Route::delete('/enrollments/{enrollment}', 'destroyEnrollment')->name('enrollments.destroy');
            Route::get('/teachers', 'teachers')->name('teachers');
            Route::post('/teachers', 'storeTeacher')->name('teachers.store');
            Route::put('/teachers/{teacher}', 'updateTeacher')->name('teachers.update');
        });

        Route::post('/students', [AccountController::class, 'registerForAdmin'])->name('students.store');
        Route::put('/grades/{enrollment}', [GradingController::class, 'update'])->name('grades.update');
    });
});
