<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/calendar', function () {
    return view('calendar.index');
})->middleware(['auth'])->name('calendar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::resource('barang', ItemController::class)
        ->parameters(['barang' => 'item'])
        ->except(['show']);

    Route::get('barang/{item}/qr', [ItemController::class, 'showQr'])->name('barang.qr');
    Route::get('barang/{item}/qr/download', [ItemController::class, 'downloadQr'])->name('barang.qr.download');
    Route::get('barang/print-labels', [ItemController::class, 'printLabels'])->name('barang.print-labels');

    Route::resource('kategori', CategoryController::class)
        ->parameters(['kategori' => 'category'])
        ->except(['show']);

    Route::resource('lokasi', LocationController::class)
        ->parameters(['lokasi' => 'location'])
        ->except(['show']);
});
require __DIR__.'/auth.php';