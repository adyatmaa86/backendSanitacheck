<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\InspeksiController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaskMonitoringController;
use App\Http\Controllers\LaporanController;


// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected Panel Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Fasilitas (CRUD)
    Route::get('/facilities', [FasilitasController::class, 'index'])->name('facilities.index');
    Route::post('/facilities', [FasilitasController::class, 'store'])->name('facilities.store');
    Route::put('/facilities/{id}', [FasilitasController::class, 'update'])->name('facilities.update');
    Route::delete('/facilities/{id}', [FasilitasController::class, 'destroy'])->name('facilities.destroy');

    // Jenis Fasilitas (CRUD)
    Route::post('/jenis-fasilitas', [\App\Http\Controllers\JenisFasilitasController::class, 'store'])->name('jenis-fasilitas.store');
    Route::put('/jenis-fasilitas/{id}', [\App\Http\Controllers\JenisFasilitasController::class, 'update'])->name('jenis-fasilitas.update');
    Route::delete('/jenis-fasilitas/{id}', [\App\Http\Controllers\JenisFasilitasController::class, 'destroy'])->name('jenis-fasilitas.destroy');

    // Inspeksi
    Route::get('/inspections', [InspeksiController::class, 'index'])->name('inspections.index');
    Route::get('/history', [InspeksiController::class, 'history'])->name('inspections.history');
    Route::post('/inspections', [InspeksiController::class, 'store'])->name('inspections.store');
    Route::get('/inspections/{id}/edit', [InspeksiController::class, 'edit'])->name('inspections.edit')->whereNumber('id');
    Route::put('/inspections/{id}', [InspeksiController::class, 'update'])->name('inspections.update')->whereNumber('id');
    Route::delete('/inspections/destroy-all', [InspeksiController::class, 'destroyAll'])->name('inspections.destroy-all');
    Route::delete('/inspections/{id}', [InspeksiController::class, 'destroy'])->name('inspections.destroy');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/bulk-delete', [NotificationController::class, 'bulkDestroy'])->name('notifications.bulk-delete');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Settings
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/add-petugas', [SettingsController::class, 'addPetugas'])->name('settings.add-petugas');
    Route::post('/settings/add-petugas', [SettingsController::class, 'storePetugas'])->name('settings.store-petugas');
    Route::get('/settings/add-admin', [SettingsController::class, 'addAdmin'])->name('settings.add-admin');
    Route::post('/settings/add-admin', [SettingsController::class, 'storeAdmin'])->name('settings.store-admin');

    // Petugas List
    Route::get('/petugas', [PetugasController::class, 'index'])->name('petugas.index');
    Route::delete('/petugas/{id}', [PetugasController::class, 'destroy'])->name('petugas.destroy');

    // Admin List
    Route::get('/admin-list', [AdminController::class, 'index'])->name('admin.index');
    Route::delete('/admin-list/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');

    // Monitoring Tugas & Petugas
    Route::get('/pantau-petugas', [TaskMonitoringController::class, 'pantauPetugas'])->name('admin.pantau-petugas');
    Route::get('/tugas-saya', [TaskMonitoringController::class, 'tugasSaya'])->name('petugas.tugas-saya');
    Route::post('/tugas-saya/complete/{id}', [TaskMonitoringController::class, 'completeTask'])->name('petugas.complete-task');

    // Laporan Masuk (Admin & Petugas)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show')->whereNumber('id');
    Route::post('/laporan/{id}/kirim', [LaporanController::class, 'kirimKePetugas'])->name('laporan.kirim')->whereNumber('id');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy')->whereNumber('id');

    // Petugas - Terima Laporan
    Route::get('/terima-laporan', [LaporanController::class, 'petugasLaporan'])->name('petugas.terima-laporan');
    Route::post('/terima-laporan/{id}', [LaporanController::class, 'terimaLaporan'])->name('petugas.terima-laporan.proses')->whereNumber('id');
});



