<?php

use App\Http\Controllers\GuruController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\IndukBukuController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\SantriController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/buku', [IndukBukuController::class, 'index'])->name('buku.index');
Route::get('/buku/input', [IndukBukuController::class, 'create'])->name('buku.create');
Route::post('/buku', [IndukBukuController::class, 'store'])->name('buku.store');
Route::get('/buku/template', [IndukBukuController::class, 'downloadTemplate'])->name('buku.template');
Route::post('/buku/upload-preview', [IndukBukuController::class, 'uploadPreview'])->name('buku.upload.preview');

Route::get('/label', [LabelController::class, 'index'])->name('label.index');
Route::get('/label/export', [LabelController::class, 'export'])->name('label.export');

Route::get('/santri', [SantriController::class, 'index'])->name('santri.index');
Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');
Route::post('/santri/refresh', [SantriController::class, 'refresh'])->name('santri.refresh');

Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
Route::get('/guru/{guruNo}', [GuruController::class, 'show'])->name('guru.show');
Route::post('/guru/refresh', [GuruController::class, 'refresh'])->name('guru.refresh');



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
