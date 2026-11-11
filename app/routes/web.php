<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonAttendanceController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonRegistrationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberAccountController;
use App\Http\Controllers\MyLessonRegistrationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PeopleDirectoryController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TrainerAccountController;
use App\Http\Middleware\EnsureActiveUser;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
});

Route::middleware(['auth', EnsureActiveUser::class])->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/my/account', [PageController::class, 'account'])->name('account.show');
    Route::patch('/my/account', [AccountController::class, 'update'])->name('account.update');
    Route::patch('/my/account/email', [AccountController::class, 'updateEmail'])->name('account.email.update');
    Route::patch('/my/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::delete('/my/account', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::patch('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::post('/lessons/{lesson}/registrations', [LessonRegistrationController::class, 'store'])
        ->name('lessons.register');
    Route::get('/lessons/{lesson}/participants', [LessonAttendanceController::class, 'show'])
        ->name('lessons.participants');
    Route::patch('/lessons/{lesson}/attendance', [LessonAttendanceController::class, 'update'])
        ->name('lessons.attendance.update');

    Route::patch('/members/{member}/deactivate', [MemberAccountController::class, 'deactivate'])
        ->name('members.deactivate');
    Route::get('/my/registrations', [MyLessonRegistrationController::class, 'index'])
        ->name('registrations.index');
    Route::get('/deelnemers', [PeopleDirectoryController::class, 'participants'])
        ->name('participants.index');
    Route::get('/trainers', [PeopleDirectoryController::class, 'trainers'])
        ->name('trainers.index');
    Route::get('/trainers/create', [TrainerAccountController::class, 'create'])
        ->name('trainers.create');
    Route::post('/trainers', [TrainerAccountController::class, 'store'])
        ->name('trainers.store');
    Route::patch('/trainers/{trainer}', [TrainerAccountController::class, 'update'])
        ->name('trainers.update');
    Route::post('/trainers/{trainer}/lessons', [TrainerAccountController::class, 'assignLessons'])
        ->name('trainers.lessons.assign');
    Route::delete('/trainers/{trainer}/lessons/{lesson}', [TrainerAccountController::class, 'detachLesson'])
        ->name('trainers.lessons.detach');
    Route::delete('/my/registrations/{lessonRegistration}', [LessonRegistrationController::class, 'destroy'])
        ->name('registrations.cancel');
});

Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
