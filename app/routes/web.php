<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonRegistrationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MyLessonRegistrationController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::patch('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::post('/lessons/{lesson}/registrations', [LessonRegistrationController::class, 'store'])
        ->name('lessons.register');

    Route::get('/my/registrations', [MyLessonRegistrationController::class, 'index'])
        ->name('registrations.index');
    Route::delete('/my/registrations/{lessonRegistration}', [LessonRegistrationController::class, 'destroy'])
        ->name('registrations.cancel');
});

Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
