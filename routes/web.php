<?php


use App\Http\Controllers\dashboardController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelangganAuthController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Payment\TripayController;
use App\Http\Controllers\TripayCallbackController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LaporanController;
use App\Exports\TagihanExport;
use App\Http\Controllers\FonnteController;
use App\Http\Controllers\FonnteNotificationController;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth/pelanggan-login');
});

Auth::routes();
Route::middleware(['auth'])->group(function(){
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/table', [TableController::class, 'index'])->name('table');
    Route::get('/home', [dashboardController::class, 'index'])->name('home');
    Route::get('/update-data', [dashboardController::class, 'updateData']);
    Route::get('/get-data-chart', [dashboardController::class, 'getDataChart']);

 });

Route::middleware('auth')->group(function () {

    Route::prefix('tagihan')->group(function () {
        Route::get('', [TagihanController::class, 'index'])->name('tagihan');
        Route::post('/store-tagihan', [TagihanController::class, 'storeTagihan'])->name('store.tagihan');
        Route::get('/buka-tagihan', [TagihanController::class, 'bukaTagihan'])->name('buka-tagihan');
        Route::get('/data-tagihan', [TagihanController::class, 'dataTagihan'])->name('data-tagihan');
        Route::get('/lunas-tagihan', [TagihanController::class, 'lunasTagihan'])->name('lunas-tagihan');
        Route::post('/bayar-tagihan/{kode}', [TagihanController::class, 'bayarTagihan'])->name('bayar-tagihan');
        Route::get('/cetak-struk/{id}', [TagihanController::class, 'cetakStruk'])->name('cetak-struk');
        Route::delete('/delete-tagihan/{id}', [TagihanController::class, 'deleteTagihan'])->name('delete-tagihan');
    });

    Route::controller(PaketController::class)->prefix('paket')->group(function () {
        Route::get('', 'index')->name('paket');
        Route::get('tambah', 'tambah')->name('paket.tambah');
        Route::post('tambah', 'simpan')->name('paket.tambah.simpan');
        Route::get('edit/{id_paket}', 'edit')->name('paket.edit');
        Route::post('edit/{id_paket}', 'update')->name('paket.update');
        Route::delete('hapus/{id_paket}', 'hapus')->name('paket.hapus');
    });

    Route::controller(PelangganController::class)->prefix('pelanggan')->group(function () {
        Route::get('', 'index')->name('pelanggan');
        Route::get('tambah', 'tambah')->name('pelanggan.tambah');
        Route::post('tambah', 'simpan')->name('pelanggan.tambah.simpan');
        Route::get('edit/{id_pelanggan}', 'edit')->name('pelanggan.edit');
        Route::put('edit/{id_pelanggan}', 'update')->name('pelanggan.update');
        Route::delete('hapus/{id_pelanggan}', 'hapus')->name('pelanggan.hapus');
        Route::get('pelanggan/{id_pelanggan}', 'show')->name('pelanggan.show');
    });

    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('banks', BankController::class);
    Route::resource('pengeluaran', PengeluaranController::class)->except(['show']);
    Route::post('/callback', [TripayCallbackController::class, 'handle']);
});

Route::get('/pelanggan-login', [PelangganAuthController::class, 'showLoginForm'])->name('pelanggan.login');
Route::post('/pelanggan-login', [PelangganAuthController::class, 'login']);
Route::middleware('auth:pelanggan')->group(function () {
    Route::get('/dashboard-pelanggan', [PelangganAuthController::class, 'dashboard'])->name('dashboard-pelanggan');
    Route::get('/belum-lunas', [PelangganAuthController::class, 'belumLunas'])->name('tagihan.belum_lunas');
    Route::get('/sudah-lunas', [PelangganAuthController::class, 'sudahLunas'])->name('tagihan.sudah_lunas');
    Route::get('/riwayat-pembayaran', [PelangganAuthController::class, 'riwayatPembayaran'])->name('tagihan.riwayat_pembayaran');
    Route::get('/profile', [PelangganAuthController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [PelangganAuthController::class, 'editProfile'])->name('edit_profile');
    Route::post('/profile/update', [PelangganAuthController::class,'updateProfile'])->name('update_profile');
    Route::get('/profile/show', [PelangganAuthController::class, 'showProfile'])->name('show_profile');
    Route::post('/profile/upload-picture', [PelangganAuthController::class, 'uploadProfilePicture'])->name('profile.picture.upload');
    Route::get('/tagihan/invoice-pembayaran/{id}', [PelangganAuthController::class, 'invoicePembayaran'])->name('tagihan.invoice_pembayaran');
    Route::get('/tagihan/{id}/payment', [PelangganAuthController::class, 'showPaymentPage'])->name('payment');
    Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('/transaction/{reference}', [TransactionController::class, 'show'])->name('transaction.show');
});

Route::middleware(['guest'])->group(function(){

    Route::get('/login2', [dashboardController::class, 'login2'])->name('login2');
    Route::get('/register2', [dashboardController::class, 'register2'])->name('register2');
 });

Route::get('export-tagihan/{bulan}/{tahun}', function ($bulan, $tahun) {
    $fileName = "tagihan_{$bulan}_{$tahun}.xlsx"; // Buat nama file dinamis
    return Excel::download(new TagihanExport($bulan, $tahun), $fileName, \Maatwebsite\Excel\Excel::XLSX);
})->name('export-tagihan');

Route::get('/tripay/config', [TripayController::class, 'showConfigForm'])->name('tripay.config.form');
Route::put('/tripay/config', [TripayController::class, 'updateConfig'])->name('tripay.config.update');

Route::post('/logout-pelanggan', [PelangganAuthController::class, 'logout'])->name('pelanggan.logout');
Route::post('/cetak-struk/{id}', [TagihanController::class, 'cetakStruk'])->name('cetak.struk');
Route::get('/generate-pdf/{id}', [TagihanController::class, 'generatePdf'])->name('generate-pdf');
Route::get('/pelanggan-lunas', [TagihanController::class, 'lunas'])->name('pelanggan.lunas');
Route::get('/pelanggan-belum-lunas', [TagihanController::class, 'belumLunas'])->name('pelanggan.belumLunas');
Route::get('/pelanggan/aktif', [PelangganController::class, 'aktif'])->name('pelanggan.aktif');
Route::get('/pelanggan/nonaktif', [PelangganController::class, 'nonaktif'])->name('pelanggan.nonaktif');
Route::get('/paket/view', [PaketController::class, 'viewPaket'])->name('paket.view');
Route::resource('pengeluaran', PengeluaranController::class)->except(['show']);

Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::post('/laporan/export', [LaporanController::class, 'export']);

Route::get('/fonnte', [FonnteController::class, 'index'])->name('fonnte.index');
Route::post('/fonnte/store-token', [FonnteController::class, 'storeToken'])->name('fonnte.storeToken');
Route::delete('/fonnte/delete', [FonnteController::class, 'deleteToken'])->name('fonnte.deleteToken');
Route::post('/fonnte/send-message', [FonnteController::class, 'sendMessage'])->name('fonnte.sendMessage');

Route::get('/fonnte/notification', [FonnteNotificationController::class, 'index'])->name('fonnte.notification.index');
Route::post('/fonnte/notification/save-settings', [FonnteNotificationController::class, 'saveSettings'])->name('fonnte.notification.saveSettings');
Route::post('/fonnte/notification/send', [FonnteNotificationController::class, 'sendNotifications'])->name('fonnte.notification.send');












