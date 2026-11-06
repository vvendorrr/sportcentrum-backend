<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home']);

Route::get('/lessons/create', function () {
    return view('lessons.create');
})->name('lessons.create');

Route::get('/lessons/preview', [PageController::class, 'lesson'])->name('lessons.preview');

Route::get('/account', [PageController::class, 'account'])->name('account');

Route::get('/admin/trainers', [PageController::class, 'trainers'])->name('admin.trainers');
