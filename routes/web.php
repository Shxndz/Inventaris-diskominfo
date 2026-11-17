<?php

use App\Http\Controllers\Admin\KategoriBarangController;
use App\Http\Controllers\Admin\RuanganController;
use App\Http\Controllers\Admin\SekbidController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\Admin\PeminjamanController as AdminPeminjamanController;
use App\Http\Controllers\Admin\LaporanKerusakanController as AdminLaporanController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\LaporanKerusakanController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('laporan', LaporanKerusakanController::class)->only(['index', 'create', 'store']);
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::resource('peminjaman', PeminjamanController::class)->only(['index', 'create', 'store', 'destroy']);
    // Khusus admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('sekbid', SekbidController::class)->except('show');
        Route::resource('kategori', KategoriBarangController::class)->except('show');
        Route::resource('ruangan', RuanganController::class)->except('show');
        Route::resource('barang', BarangController::class)->except('show');
        Route::get('peminjaman', [AdminPeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('peminjaman/{peminjaman}', [AdminPeminjamanController::class, 'show'])->name('peminjaman.show');
        Route::patch('peminjaman/{peminjaman}/setujui', [AdminPeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::patch('peminjaman/{peminjaman}/tolak', [AdminPeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
        Route::patch('peminjaman/{peminjaman}/kembalikan', [AdminPeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
        Route::get('laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/{laporan}', [AdminLaporanController::class, 'show'])->name('laporan.show');
        Route::patch('laporan/{laporan}/status', [AdminLaporanController::class, 'updateStatus'])->name('laporan.status');
        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/open', [AdminNotificationController::class, 'open'])->name('notifications.open');
        Route::post('notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
});

require __DIR__ . '/auth.php';
