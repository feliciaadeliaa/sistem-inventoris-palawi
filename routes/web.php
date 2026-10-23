<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GolonganController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\MutasiController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\StockOutController as AdminStockOutController;
use App\Http\Controllers\User\StockOutController as UserStockOutController;
use App\Http\Controllers\User\RiwayatController;
use App\Http\Controllers\Admin\RepairRequestController as AdminRepairRequestController;
use App\Http\Controllers\User\RepairRequestController as UserRepairRequestController;
use App\Http\Controllers\Gm\ApprovalController as GmApprovalController;
use App\Http\Controllers\BarangLookupController;
use App\Http\Controllers\Admin\LaporanPenyusutanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Semua route di bawah ini WAJIB login + email terverifikasi
|--------------------------------------------------------------------------
| Route verifikasi email, kirim ulang, dan logout ada di routes/auth.php
| (di luar grup ini) supaya user yang belum verifikasi tetap bisa
| mengaksesnya.
*/
// Profil sengaja TIDAK memakai 'verified': kalau user salah ketik saat mengganti
// email, dia harus tetap bisa membuka profil untuk memperbaikinya.
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'active'])->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Scan/Cari Barang - bisa diakses semua role yang login (admin & user)
    Route::get('/barang/scan', [BarangLookupController::class, 'index'])->name('barang.scan');
    Route::get('/barang/cari', [BarangLookupController::class, 'search'])->name('barang.cari');
    Route::get('/barang/{item_id}/detail', [BarangLookupController::class, 'show'])->name('barang.detail');

    // Riwayat pengajuan saya - semua role yang login (user, GM, dll.) bisa melihat pengajuannya sendiri
    Route::get('/riwayat-pengajuan', [RiwayatController::class, 'index'])->name('riwayat.index');

    // Sisi User - khusus role 'user'
    Route::middleware('user')->prefix('transaksi')->name('peminjaman.')->group(function () {
        Route::get('stock-out', [UserStockOutController::class, 'index'])->name('index');
        Route::get('stock-out/ajukan/{item_id?}', [UserStockOutController::class, 'create'])->name('create');
        Route::post('stock-out', [UserStockOutController::class, 'store'])->name('store');
        Route::get('stock-out/export', [UserStockOutController::class, 'export'])->name('export');
    });

    // ==== SEMENTARA NONAKTIF - menunggu kejelasan alur dari kantor ====
    // Kode asli (AdminRepairRequestController, UserRepairRequestController) TIDAK dihapus,
    // cuma nggak dirouting dulu. Tinggal balikin kalau sudah jelas.
    Route::middleware('user')->group(function () {
        Route::get('/perbaikan/riwayat', fn () => view('coming-soon', ['feature' => 'Perbaikan']))->name('perbaikan.index');
        Route::get('/ajukan/perbaikan', fn () => view('coming-soon', ['feature' => 'Perbaikan']))->name('perbaikan.create');

        Route::get('/ajukan/kerusakan', fn () => view('coming-soon', ['feature' => 'Lapor Kerusakan']))->name('kerusakan.create');
    });

    // Sisi Admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

    Route::middleware('admin')->prefix('admin')->group(function () {
        // Download Excel data aset (tanpa kolom foto) - taruh di atas resource
        Route::get('barang/export', [ItemController::class, 'export'])->name('barang.export');

        // Trash (aset yang di-soft delete) + pulihkan + hapus permanen
        Route::get('barang/trash', [ItemController::class, 'trash'])->name('barang.trash');
        Route::patch('barang/{id}/restore', [ItemController::class, 'restore'])->name('barang.restore');
        Route::delete('barang/{id}/force-delete', [ItemController::class, 'forceDelete'])->name('barang.force-delete');

        Route::resource('barang', ItemController::class)
            ->parameters(['barang' => 'item'])
            ->except(['show']);

        Route::get('barang/{item}/qr', [ItemController::class, 'showQr'])->name('barang.qr');
        Route::get('barang/{item}/qr/download', [ItemController::class, 'downloadQr'])->name('barang.qr.download');
        Route::get('barang/print-labels', [ItemController::class, 'printLabels'])->name('barang.print-labels');

        // Cetak label sebagai PNG (ZIP)
        Route::get('barang/print-labels-png', [ItemController::class, 'printLabelsPng'])->name('barang.print-labels-png');

        // ===== Import asset via Excel (pola sama dengan lokasi-import) =====
        Route::get('barang-import', [ItemController::class, 'importForm'])->name('barang.import.form');
        Route::post('barang-import', [ItemController::class, 'import'])->name('barang.import');
        Route::get('barang-import/template', [ItemController::class, 'template'])->name('barang.import.template');

        // Kategori: tanpa hapus, ada aktif/nonaktif (edit tetap ada)
        Route::resource('kategori', CategoryController::class)
            ->parameters(['kategori' => 'category'])
            ->except(['show', 'destroy']);
        Route::patch('kategori/{category}/toggle-active', [CategoryController::class, 'toggleActive'])
            ->name('kategori.toggle-active');

        // Master Golongan AT (menentukan masa manfaat aset berdasarkan sub jenis)
        // Tanpa edit & hapus, ada aktif/nonaktif
        Route::resource('golongan', GolonganController::class)
            ->parameters(['golongan' => 'golongan'])
            ->except(['show', 'edit', 'update', 'destroy']);
        Route::patch('golongan/{golongan}/toggle-active', [GolonganController::class, 'toggleActive'])
            ->name('golongan.toggle-active');

        // Lokasi: tanpa edit & hapus, ada aktif/nonaktif
        Route::resource('lokasi', LocationController::class)
            ->parameters(['lokasi' => 'location'])
            ->except(['show', 'edit', 'update', 'destroy']);
        Route::patch('lokasi/{location}/toggle-active', [LocationController::class, 'toggleActive'])
            ->name('lokasi.toggle-active');

        Route::get('lokasi-import', [LocationController::class, 'importForm'])->name('lokasi.import.form');
        Route::post('lokasi-import', [LocationController::class, 'import'])->name('lokasi.import');

        // Pengadaan - belum pernah dibangun, sementara coming soon
        Route::get('pengadaan', fn () => view('coming-soon', ['feature' => 'Pengadaan']))->name('pengadaan.index');
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('stock-in', [StockInController::class, 'index'])->name('stock-in.index');
        Route::patch('stock-in/{transaction}/confirm-return', [StockInController::class, 'confirmReturn'])->name('stock-in.confirm-return');
        Route::get('stock-in/export', [StockInController::class, 'export'])->name('stock-in.export');
    });

    Route::middleware('admin')->prefix('admin/transaksi')->name('admin.transaksi.')->group(function () {
        Route::get('stock-out', [AdminStockOutController::class, 'index'])->name('stock-out.index');
        Route::patch('stock-out/{transaction}/approve', [AdminStockOutController::class, 'approve'])->name('stock-out.approve');
        Route::patch('stock-out/{transaction}/reject', [AdminStockOutController::class, 'reject'])->name('stock-out.reject');
        Route::get('stock-out/export', [AdminStockOutController::class, 'export'])->name('stock-out.export');

        Route::get('mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
        Route::post('mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
        Route::get('mutasi/export', [MutasiController::class, 'export'])->name('mutasi.export');

        // Perbaikan (admin) - sementara coming soon
        Route::get('perbaikan', fn () => view('coming-soon', ['feature' => 'Perbaikan']))->name('perbaikan.index');

        // Kerusakan - belum pernah dibangun, sementara coming soon
        Route::get('kerusakan', fn () => view('coming-soon', ['feature' => 'Laporan Kerusakan']))->name('kerusakan.index');
    });

    // Laporan & Ekspor
    Route::middleware('admin')->prefix('admin/laporan')->name('admin.laporan.')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.laporan.riwayat-barang.index'))->name('index');

        Route::get('riwayat-barang/export-pdf', [LaporanController::class, 'exportRiwayatBarangPdf'])->name('riwayat-barang.export-pdf');
        Route::get('riwayat-barang/export-excel', [LaporanController::class, 'exportRiwayatBarangExcel'])->name('riwayat-barang.export-excel');
        Route::get('riwayat-barang', [LaporanController::class, 'riwayatBarangIndex'])->name('riwayat-barang.index');
        Route::get('peminjaman-aktif/export-pdf', [LaporanController::class, 'exportPeminjamanAktifPdf'])->name('peminjaman-aktif.export-pdf');
        Route::get('peminjaman-aktif/export-excel', [LaporanController::class, 'exportPeminjamanAktifExcel'])->name('peminjaman-aktif.export-excel');
        Route::get('peminjaman-aktif', [LaporanController::class, 'peminjamanAktifIndex'])->name('peminjaman-aktif.index');

        Route::get('permintaan-perbaikan', fn () => view('coming-soon', [
            'feature' => 'Laporan Permintaan Perbaikan',
            'activeTab' => 'perbaikan',
        ]))->name('permintaan-perbaikan.index');

        Route::get('pengurangan-fasilitas', fn () => view('coming-soon', [
            'feature' => 'Laporan Pengurangan Fasilitas',
            'activeTab' => 'fasilitas',
        ]))->name('pengurangan-fasilitas.index');

        Route::get('riwayat-barang/{item}', [LaporanController::class, 'riwayatBarangShow'])->name('riwayat-barang.show');
    });

    // Sisi GM
    Route::middleware('gm')->prefix('gm')->name('gm.')->group(function () {
        Route::get('approval', [GmApprovalController::class, 'index'])->name('approval.index');
        Route::patch('approval/stock-out/{transaction}/approve', [GmApprovalController::class, 'approveStockOut'])->name('approval.stock-out.approve');
        Route::patch('approval/stock-out/{transaction}/reject', [GmApprovalController::class, 'rejectStockOut'])->name('approval.stock-out.reject');
        Route::patch('approval/mutasi/{transaction}/approve', [GmApprovalController::class, 'approveMutasi'])->name('approval.mutasi.approve');
        Route::patch('approval/mutasi/{transaction}/reject', [GmApprovalController::class, 'rejectMutasi'])->name('approval.mutasi.reject');
    });

    Route::middleware('role:admin,finance,gm,senior_analis')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('penyusutan', [LaporanPenyusutanController::class, 'index'])->name('penyusutan');
        Route::get('penyusutan/export', [LaporanPenyusutanController::class, 'export'])->name('penyusutan.export');
    });
});

// Di luar grup 'verified' (berisi login, register, verifikasi email, logout)
require __DIR__.'/auth.php';