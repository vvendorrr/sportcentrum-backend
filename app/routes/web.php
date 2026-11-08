<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonRegistrationController;
use App\Http\Controllers\MyLessonRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::post('/lessons/{lesson}/registrations', [LessonRegistrationController::class, 'store'])
        ->name('lessons.register');

    Route::get('/my/registrations', [MyLessonRegistrationController::class, 'index'])
        ->name('registrations.index');
    Route::delete('/my/registrations/{lessonRegistration}', [LessonRegistrationController::class, 'destroy'])
        ->name('registrations.cancel');
});
