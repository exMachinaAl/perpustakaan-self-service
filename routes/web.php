<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KioskCirculationController;
use App\Http\Controllers\KioskDeviceController;
use App\Http\Controllers\KioskOmnichannelController;
use App\Http\Controllers\KioskPilotController;
use App\Http\Controllers\KioskPilotReturnController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RfidCardController;
use App\Http\Controllers\RfidDeskController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/lupa-password', [ForgotPasswordController::class, 'showForgotForm'])
    ->name('password.forgot');

Route::post('/lupa-password', [ForgotPasswordController::class, 'checkEmail'])->middleware('throttle:3,1')
    ->name('password.check');

Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->middleware('throttle:5,1')
    ->name('password.update');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')
    ->middleware(['auth', 'active', 'role:admin'])
    ->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/data-buku', [BukuController::class, 'index'])
            ->name('admin.databuku.index');

        Route::get('/data-buku/create', [BukuController::class, 'create'])
            ->name('admin.databuku.create');

        Route::post('/data-buku/store', [BukuController::class, 'store'])
            ->name('admin.databuku.store');

        Route::get('/data-buku/{id_buku}/edit', [BukuController::class, 'edit'])
            ->name('admin.databuku.edit');

        Route::put('/data-buku/{id_buku}', [BukuController::class, 'update'])
            ->name('admin.databuku.update');

        Route::delete('/data-buku/{id_buku}', [BukuController::class, 'destroy'])
            ->name('admin.databuku.destroy');

        Route::get('/data-buku/{book}/eksemplar', [BookCopyController::class, 'index'])
            ->name('admin.databuku.inventory.index');
        Route::post('/data-buku/{book}/eksemplar', [BookCopyController::class, 'store'])
            ->middleware('throttle:30,1')->name('admin.databuku.inventory.store');
        Route::patch('/data-buku/{book}/eksemplar/{copy}', [BookCopyController::class, 'update'])
            ->name('admin.databuku.inventory.update');
        Route::post('/data-buku/{book}/eksemplar/{copy}/reissue', [BookCopyController::class, 'reissue'])
            ->middleware('throttle:10,1')->name('admin.databuku.inventory.reissue');
        Route::patch('/data-buku/{book}/eksemplar/{copy}/status', [BookCopyController::class, 'toggle'])
            ->name('admin.databuku.inventory.toggle');
        Route::get('/data-buku/{book}/eksemplar-cetak', [BookCopyController::class, 'print'])
            ->middleware('throttle:20,1')->name('admin.databuku.inventory.print');

        Route::get('/data-buku/{id_buku}', [BukuController::class, 'show'])
            ->name('admin.databuku.show');

        Route::get('/peminjaman', [PeminjamanController::class, 'index'])
            ->name('admin.peminjaman.index');

        Route::put('/peminjaman/{peminjaman_id}/setujui', [PeminjamanController::class, 'setujui'])
            ->name('admin.peminjaman.setujui');

        Route::put('/peminjaman/{peminjaman_id}/tolak', [PeminjamanController::class, 'tolak'])
            ->name('admin.peminjaman.tolak');

        Route::put('/peminjaman/{peminjaman_id}/kembalikan', [PeminjamanController::class, 'adminKembalikan'])
            ->name('admin.peminjaman.kembalikan');

        Route::patch('/peminjaman/{peminjaman_id}/status', [PeminjamanController::class, 'updateStatus'])
            ->name('admin.peminjaman.updateStatus');

        Route::delete('/peminjaman/{peminjaman_id}', [PeminjamanController::class, 'destroy'])
            ->name('admin.peminjaman.destroy');

        Route::get('/anggota', [AnggotaController::class, 'index'])->name('admin.anggota.index');
        Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('admin.anggota.create');
        Route::post('/anggota', [AnggotaController::class, 'store'])->name('admin.anggota.store');

        Route::get('/anggota/{id}', [AnggotaController::class, 'show'])->name('admin.anggota.show');
        Route::get('/anggota/{id}/edit', [AnggotaController::class, 'edit'])->name('admin.anggota.edit');
        Route::put('/anggota/{id}', [AnggotaController::class, 'update'])->name('admin.anggota.update');
        Route::get('/anggota/{id}/kartu', [AnggotaController::class, 'card'])
            ->middleware('throttle:20,1')->name('admin.anggota.card');
        Route::post('/anggota/{id}/kartu/reissue', [AnggotaController::class, 'reissueCard'])
            ->middleware('throttle:5,1')->name('admin.anggota.card.reissue');

        Route::patch('/anggota/{id}/status', [AnggotaController::class, 'updateStatus'])->name('admin.anggota.updateStatus');
        Route::delete('/anggota/{id}', [AnggotaController::class, 'destroy'])->name('admin.anggota.destroy');

        Route::get('/anggota-export', [AnggotaController::class, 'export'])->name('admin.anggota.export');

        Route::get('/program-studi', [ProdiController::class, 'index'])
            ->name('admin.program-studi.index');

        Route::get('/program-studi/create', [ProdiController::class, 'create'])
            ->name('admin.program-studi.create');

        Route::post('/program-studi', [ProdiController::class, 'store'])
            ->name('admin.program-studi.store');

        Route::get('/program-studi/{prodi}', [ProdiController::class, 'show'])
            ->name('admin.program-studi.show');

        Route::get('/program-studi/{prodi}/edit', [ProdiController::class, 'edit'])
            ->name('admin.program-studi.edit');

        Route::put('/program-studi/{prodi}', [ProdiController::class, 'update'])
            ->name('admin.program-studi.update');

        Route::delete('/program-studi/{prodi}', [ProdiController::class, 'destroy'])
            ->name('admin.program-studi.destroy');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->middleware('throttle:10,1')->name('laporan.export');

        Route::get('/verifikasi-peminjaman', [PeminjamanController::class, 'verifikasi'])
            ->name('admin.verifikasi.index');

        Route::put('/verifikasi-peminjaman/{peminjaman_id}/status', [PeminjamanController::class, 'updateStatus'])
            ->name('admin.verifikasi.update-status');

        Route::get('/scan', [ScanController::class, 'index'])
            ->name('admin.scan.index');

        Route::get('/scan/peminjaman/{id}', [ScanController::class, 'validasi'])
            ->name('admin.scan.validasi');
        Route::post('/scan/verify', [ScanController::class, 'verify'])->middleware('throttle:30,1')->name('admin.scan.verify');
        Route::get('/rfid', [RfidCardController::class, 'index'])->name('admin.rfid.index');
        Route::post('/rfid', [RfidCardController::class, 'store'])->middleware('throttle:30,1')->name('admin.rfid.store');
        Route::patch('/rfid/{card}/revoke', [RfidCardController::class, 'revoke'])->name('admin.rfid.revoke');
        Route::get('/rfid/desk', [RfidDeskController::class, 'index'])->name('admin.rfid.desk');
        Route::get('/rfid/events', [RfidDeskController::class, 'events'])->middleware('throttle:120,1')->name('admin.rfid.events');
        Route::post('/rfid/enroll', [RfidCardController::class, 'enroll'])->middleware('throttle:15,1')->name('admin.rfid.enroll');

        Route::put('/peminjaman/{peminjaman_id}/bayar-denda', [PeminjamanController::class, 'bayarDenda'])
            ->name('admin.peminjaman.bayar-denda');

        Route::get('/peminjaman/{peminjaman_id}', [PeminjamanController::class, 'adminShow'])
            ->name('admin.peminjaman.show');

    });

Route::prefix('user')
    ->middleware(['auth', 'active', 'role:user'])
    ->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'index'])
            ->name('user.dashboard');

        Route::get('/katalog', [BukuController::class, 'userIndex'])
            ->name('user.katalog');

        Route::get('/katalog/{id_buku}', [BukuController::class, 'userShow'])
            ->name('user.katalog.show');

        Route::post('/pinjam/{id_buku}', [PeminjamanController::class, 'store'])
            ->name('user.peminjaman.store');

        Route::get('/status-peminjaman', [PeminjamanController::class, 'cek'])
            ->name('user.peminjaman.cek');

        Route::put('/status-peminjaman/{peminjaman_id}/tidak-jadi', [PeminjamanController::class, 'tidakJadi'])
            ->name('user.peminjaman.tidakJadi');

        Route::get('/peminjaman/{id}/bukti', [PeminjamanController::class, 'bukti'])
            ->name('user.peminjaman.bukti');

        Route::get('/riwayat-peminjaman', [PeminjamanController::class, 'riwayat'])
            ->name('user.riwayat');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('user.profile.edit');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('user.profile.update');

        Route::delete('/profile/foto', [ProfileController::class, 'destroyPhoto'])
            ->name('user.profile.foto.destroy');

    });

// PATCH 004: Omnichannel Kiosk LAB (dry-run only; admin-authenticated debug station).
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin/kiosk')->group(function () {
    Route::get('/hub', [KioskOmnichannelController::class, 'index'])->name('admin.kiosk.hub');
    Route::post('/pair/new', [KioskOmnichannelController::class, 'newPairAjax'])->middleware('throttle:6,1')->name('admin.kiosk.pair.new');
    Route::post('/inspect', [KioskOmnichannelController::class, 'inspect'])->middleware('throttle:60,1')->name('admin.kiosk.inspect');
    Route::post('/simulate', [KioskOmnichannelController::class, 'simulate'])->middleware('throttle:30,1')->name('admin.kiosk.simulate');
    // PATCH 005B: diagnostik LAB dan QR anggota contoh dari siswa aktif, tidak membuat pinjaman.
    Route::get('/health', [KioskOmnichannelController::class, 'labHealth'])->middleware('throttle:40,1')->name('admin.kiosk.health');
    Route::post('/debug/member-pass', [KioskOmnichannelController::class, 'debugMemberPass'])->middleware('throttle:15,1')->name('admin.kiosk.debug.member-pass');
    Route::get('/events', [KioskOmnichannelController::class, 'events'])->middleware('throttle:120,1')->name('admin.kiosk.events');
    Route::post('/books/copies', [KioskOmnichannelController::class, 'createCopyFromForm'])->middleware('throttle:20,1')->name('admin.kiosk.copies.create-form');
    Route::post('/books/{bookId}/copies', [KioskOmnichannelController::class, 'createCopy'])->middleware('throttle:20,1')->name('admin.kiosk.copies.create');
});
Route::middleware(['auth', 'active', 'role:user'])->prefix('user/kiosk')->group(function () {
    Route::get('/pass', [KioskOmnichannelController::class, 'memberPass'])->middleware('throttle:20,1')->name('user.kiosk.pass');
    Route::post('/pin', [KioskOmnichannelController::class, 'updatePin'])->middleware('throttle:5,1')->name('user.kiosk.pin');
    Route::get('/connect/{token}', [KioskOmnichannelController::class, 'connect'])->middleware('throttle:10,1')->name('user.kiosk.connect');
    Route::post('/connect/{token}', [KioskOmnichannelController::class, 'connectSubmit'])->middleware('throttle:10,1')->name('user.kiosk.connect.submit');
    Route::get('/phone', [KioskOmnichannelController::class, 'phone'])->name('user.kiosk.phone');
    Route::post('/phone/scan', [KioskOmnichannelController::class, 'phoneScan'])->middleware('throttle:20,1')->name('user.kiosk.phone.scan');
});

// PATCH 006 — real QR scans, server-owned session, opt-in supervised checkout.
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin/kiosk/circulation')->group(function () {
    Route::post('/scan', [KioskCirculationController::class, 'scan'])->middleware('throttle:60,1')->name('admin.kiosk.circulation.scan');
    Route::post('/reset', [KioskCirculationController::class, 'reset'])->middleware('throttle:30,1')->name('admin.kiosk.circulation.reset');
    Route::get('/status', [KioskCirculationController::class, 'status'])->middleware('throttle:120,1')->name('admin.kiosk.circulation.status');
});

// PATCH 007 — Public kiosk device enrollment, not an admin/user login session.
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin/kiosk/devices')->group(function () {
    Route::get('/', [KioskDeviceController::class, 'devices'])->name('admin.kiosk.devices');
    Route::post('/', [KioskDeviceController::class, 'create'])->middleware('throttle:10,1')->name('admin.kiosk.devices.create');
    Route::post('/{id}/reissue', [KioskDeviceController::class, 'reissue'])->middleware('throttle:10,1')->name('admin.kiosk.devices.reissue');
    Route::patch('/{id}/revoke', [KioskDeviceController::class, 'revoke'])->name('admin.kiosk.devices.revoke');
});
// These routes MUST NOT have 'auth' or 'role:admin': device credential is scoped in controller.
Route::prefix('kiosk')->group(function () {
    Route::get('/setup', [KioskDeviceController::class, 'setup'])->name('kiosk.setup');
    Route::post('/enroll', [KioskDeviceController::class, 'enroll'])->middleware('throttle:5,1')->name('kiosk.enroll');
    Route::get('/', [KioskDeviceController::class, 'stationPage'])->name('kiosk.station');
    Route::post('/scan', [KioskDeviceController::class, 'scan'])->middleware('throttle:60,1')->name('kiosk.scan');
    Route::post('/authenticate', [KioskDeviceController::class, 'authenticate'])->middleware('throttle:10,1')->name('kiosk.authenticate');
    Route::post('/checkout', [KioskDeviceController::class, 'checkout'])->middleware('throttle:20,1')->name('kiosk.checkout');
    Route::post('/reset', [KioskDeviceController::class, 'reset'])->middleware('throttle:30,1')->name('kiosk.reset');
    Route::get('/status', [KioskDeviceController::class, 'status'])->middleware('throttle:120,1')->name('kiosk.status');
});

// PATCH 009: Only authenticated admin may approve kiosk checkout pilot. Never on public /kiosk.
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin/kiosk/pilot')->group(function () {
    Route::get('/', [KioskPilotController::class, 'index'])->name('admin.kiosk.pilot');
    Route::post('/return/{loanId}', [KioskPilotReturnController::class, 'receive'])
        ->whereNumber('loanId')->middleware('throttle:10,1')->name('admin.kiosk.pilot.return');
    Route::post('/{deviceId}/approve', [KioskPilotController::class, 'approve'])
        ->whereNumber('deviceId')->middleware('throttle:10,1')->name('admin.kiosk.pilot.approve');
});
