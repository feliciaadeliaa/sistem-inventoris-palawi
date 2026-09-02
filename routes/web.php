<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\MutasiController;
use App\Http\Controllers\Admin\StockOutController as AdminStockOutController;
use App\Http\Controllers\User\StockOutController as UserStockOutController;
use App\Http\Controllers\Admin\RepairRequestController as AdminRepairRequestController;
use App\Http\Controllers\User\RepairRequestController as UserRepairRequestController;
use App\Http\Controllers\Gm\ApprovalController as GmApprovalController;
use App\Http\Controllers\BarangLookupController;
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

// Scan/Cari Barang - bisa diakses semua role yang login (admin & user)
Route::middleware('auth')->group(function () {
    Route::get('/barang/scan', [BarangLookupController::class, 'index'])->name('barang.scan');
    Route::get('/barang/cari', [BarangLookupController::class, 'search'])->name('barang.cari');
    Route::get('/barang/{item_id}/detail', [BarangLookupController::class, 'show'])->name('barang.detail');
});

// Sisi User - khusus role 'user'
Route::middleware(['auth', 'user'])->prefix('transaksi')->name('peminjaman.')->group(function () {
    Route::get('stock-out', [UserStockOutController::class, 'index'])->name('index');
    Route::get('stock-out/ajukan/{item_id}', [UserStockOutController::class, 'create'])->name('create');
    Route::post('stock-out', [UserStockOutController::class, 'store'])->name('store');
});

// Sisi User - Ajukan perbaikan
// NOTE: kerusakan (lapor rusak) sengaja belum didaftarkan di sini — alurnya beda, menyusul terpisah.
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/perbaikan/riwayat', [UserRepairRequestController::class, 'index'])->name('perbaikan.index');
    Route::get('/ajukan/perbaikan', [UserRepairRequestController::class, 'create'])->name('perbaikan.create');
    Route::post('/ajukan/perbaikan', [UserRepairRequestController::class, 'store'])->name('perbaikan.store');
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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('stock-in', [StockInController::class, 'index'])->name('stock-in.index');
    Route::patch('stock-in/{transaction}/confirm-return', [StockInController::class, 'confirmReturn'])->name('stock-in.confirm-return');
});

Route::middleware(['auth', 'admin'])->prefix('admin/transaksi')->name('admin.transaksi.')->group(function () {
    Route::get('stock-out', [AdminStockOutController::class, 'index'])->name('stock-out.index');
    Route::patch('stock-out/{transaction}/approve', [AdminStockOutController::class, 'approve'])->name('stock-out.approve');
    Route::patch('stock-out/{transaction}/reject', [AdminStockOutController::class, 'reject'])->name('stock-out.reject');
    Route::get('mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::post('mutasi', [MutasiController::class, 'store'])->name('mutasi.store');

    // Perbaikan (admin)
    Route::get('perbaikan', [AdminRepairRequestController::class, 'index'])->name('perbaikan.index');
    Route::patch('perbaikan/{transaction}/process', [AdminRepairRequestController::class, 'process'])->name('perbaikan.process');
    Route::patch('perbaikan/{transaction}/complete', [AdminRepairRequestController::class, 'complete'])->name('perbaikan.complete');
});

Route::middleware(['auth', 'gm'])->prefix('gm')->name('gm.')->group(function () {
    Route::get('approval', [GmApprovalController::class, 'index'])->name('approval.index');
    Route::patch('approval/stock-out/{transaction}/approve', [GmApprovalController::class, 'approveStockOut'])->name('approval.stock-out.approve');
    Route::patch('approval/stock-out/{transaction}/reject', [GmApprovalController::class, 'rejectStockOut'])->name('approval.stock-out.reject');
    Route::patch('approval/mutasi/{transaction}/acknowledge', [GmApprovalController::class, 'acknowledgeMutasi'])->name('approval.mutasi.acknowledge');
    Route::patch('approval/repair/{transaction}/approve', [\App\Http\Controllers\Gm\ApprovalController::class, 'approveRepair'])->name('approval.repair.approve');
    Route::patch('approval/repair/{transaction}/reject', [\App\Http\Controllers\Gm\ApprovalController::class, 'rejectRepair'])->name('approval.repair.reject');

});

require __DIR__.'/auth.php';