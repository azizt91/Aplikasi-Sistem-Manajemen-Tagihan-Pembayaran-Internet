<?php


use App\Http\Controllers\DashboardController;
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
use App\Http\Controllers\Auth\ManualResetController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\GenieAcsController;
use App\Http\Controllers\WifiSettingController;
use App\Http\Controllers\NotificationSettingController;
use App\Http\Controllers\Auth\UnifiedLoginController;
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
    return redirect('/login');
});

Route::get('/password/manual/reset', [ManualResetController::class, 'showForm'])->name('password.manual.form');
Route::post('/password/manual/reset', [ManualResetController::class, 'reset'])->name('password.manual.reset');

// Override login routes dengan UnifiedLoginController (sebelum Auth::routes)
Route::get('/login', [UnifiedLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UnifiedLoginController::class, 'login']);
Route::post('/logout', [UnifiedLoginController::class, 'logout'])->name('logout');

// Auth::routes tanpa login (sudah di-override di atas)
Auth::routes(['login' => false, 'logout' => false]);

Route::middleware(['auth'])->group(function(){
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/table', [TableController::class, 'index'])->name('table');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/update-data', [DashboardController::class, 'updateData']);
    Route::get('/get-data-chart', [DashboardController::class, 'getDataChart']);
    Route::get('/get-dashboard-charts', [DashboardController::class, 'getChartData']);

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
        Route::post('/rollback-tagihan/{id}', [TagihanController::class, 'rollbackTagihan'])->name('rollback-tagihan');
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
        Route::get('aktif', 'aktif')->name('pelanggan.aktif');
        Route::get('nonaktif', 'nonaktif')->name('pelanggan.nonaktif');

        // IP Address Management Routes
        Route::post('{id_pelanggan}/update-ip', 'updateIP')->name('pelanggan.update-ip');
        Route::post('{id_pelanggan}/ping', 'pingIP')->name('pelanggan.ping');
        Route::post('sync-network-status', 'syncNetworkStatus')->name('pelanggan.sync-network-status');
        Route::get('network-status-data', 'getNetworkStatusData')->name('pelanggan.network-status-data');
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

    Route::get('/maps', [\App\Http\Controllers\MapController::class,'index'])->name('maps.index');
    Route::get('/maps/markers', [\App\Http\Controllers\MapController::class,'markers'])->name('maps.markers');
    Route::post('/maps/refresh-network-status', [\App\Http\Controllers\MapController::class,'refreshNetworkStatus'])->name('maps.refresh-network-status');
    Route::get('/maps/rx-power/{id_pelanggan}', [\App\Http\Controllers\MapController::class,'getRxPower'])->name('maps.rx-power');
    
    // Admin Broadcast Notifications
    Route::get('/broadcast', [\App\Http\Controllers\NotificationBroadcastController::class, 'create'])->name('broadcast.create');
    Route::post('/broadcast', [\App\Http\Controllers\NotificationBroadcastController::class, 'send'])->name('broadcast.send');
});

// Redirect pelanggan-login ke unified login
Route::get('/pelanggan-login', function () {
    return redirect('/login');
})->name('pelanggan.login');
Route::post('/pelanggan-login', [UnifiedLoginController::class, 'login']);
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
    
    // New Pelanggan Pages
    Route::get('/pelanggan/tagihan', [PelangganAuthController::class, 'tagihan'])->name('pelanggan.tagihan');
    Route::get('/pelanggan/pemakaian', [PelangganAuthController::class, 'pemakaian'])->name('pelanggan.pemakaian');
    Route::get('/pelanggan/traffic-data', [PelangganAuthController::class, 'getTrafficData'])->name('pelanggan.traffic_data');
    Route::get('/pelanggan/pengumuman', [PelangganAuthController::class, 'pengumuman'])->name('pelanggan.pengumuman');
    Route::get('/pelanggan/bantuan', [PelangganAuthController::class, 'bantuan'])->name('pelanggan.bantuan');

    // WiFi Settings (GenieACS)
    Route::get('/wifi-settings', [WifiSettingController::class, 'index'])->name('wifi-settings.index');
    Route::post('/wifi-settings', [WifiSettingController::class, 'update'])->name('wifi-settings.update');
    
    // Pelanggan Notifications
    Route::get('/pelanggan/notifikasi', [\App\Http\Controllers\PelangganNotificationController::class, 'index'])->name('pelanggan.notifikasi');
    Route::get('/pelanggan/notifikasi/count', [\App\Http\Controllers\PelangganNotificationController::class, 'getUnreadCount'])->name('pelanggan.notifikasi.count');
    Route::get('/pelanggan/notifikasi/latest', [\App\Http\Controllers\PelangganNotificationController::class, 'getLatest'])->name('pelanggan.notifikasi.latest');
    Route::post('/pelanggan/notifikasi/{id}/read', [\App\Http\Controllers\PelangganNotificationController::class, 'markAsRead'])->name('pelanggan.notifikasi.read');
    Route::post('/pelanggan/notifikasi/read-all', [\App\Http\Controllers\PelangganNotificationController::class, 'markAllAsRead'])->name('pelanggan.notifikasi.read-all');
    Route::delete('/pelanggan/notifikasi/delete-all', [\App\Http\Controllers\PelangganNotificationController::class, 'deleteAll'])->name('pelanggan.notifikasi.delete-all');
});

Route::middleware(['guest'])->group(function(){

    Route::get('/login2', [DashboardController::class, 'login2'])->name('login2');
    Route::get('/register2', [DashboardController::class, 'register2'])->name('register2');
 });

// Logout pelanggan - tidak perlu auth admin karena ini untuk pelanggan
Route::post('/logout-pelanggan', [PelangganAuthController::class, 'logout'])->name('pelanggan.logout');

// ============================================================================
// PROTECTED ROUTES - Semua route berikut membutuhkan autentikasi admin
// ============================================================================
Route::middleware('auth')->group(function () {
    // Export Tagihan
    Route::get('export-tagihan/{bulan}/{tahun}', function ($bulan, $tahun) {
        $fileName = "tagihan_{$bulan}_{$tahun}.xlsx";
        return Excel::download(new TagihanExport($bulan, $tahun), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    })->name('export-tagihan');

    // Tripay Configuration
    Route::get('/tripay/config', [TripayController::class, 'showConfigForm'])->name('tripay.config.form');
    Route::put('/tripay/config', [TripayController::class, 'updateConfig'])->name('tripay.config.update');

    // GenieACS Configuration
    Route::get('/genieacs', [GenieAcsController::class, 'index'])->name('genieacs.index');
    Route::put('/genieacs', [GenieAcsController::class, 'update'])->name('genieacs.update');
    Route::post('/genieacs/test', [GenieAcsController::class, 'testConnection'])->name('genieacs.test');

    // Admin WiFi Management
    Route::get('/pelanggan/{id}/wifi-info', [WifiSettingController::class, 'getWifiInfoForAdmin'])->name('admin.wifi.info');
    Route::post('/pelanggan/{id}/wifi-update', [WifiSettingController::class, 'adminUpdate'])->name('admin.wifi.update');
    Route::get('/pelanggan/{id}/rx-power', [WifiSettingController::class, 'getRxPower'])->name('admin.rx.power');

    // Notification Settings
    Route::get('/notification-settings', [NotificationSettingController::class, 'index'])->name('notification-settings.index');
    Route::put('/notification-settings', [NotificationSettingController::class, 'update'])->name('notification-settings.update');

    // Cetak Struk & PDF
    Route::post('/cetak-struk/{id}', [TagihanController::class, 'cetakStruk'])->name('cetak.struk');
    Route::get('/generate-pdf/{id}', [TagihanController::class, 'generatePdf'])->name('generate-pdf');

    // Pelanggan Status Views (admin only)
    Route::get('/pelanggan-lunas', [TagihanController::class, 'lunas'])->name('pelanggan.lunas');
    Route::get('/pelanggan-belum-lunas', [TagihanController::class, 'belumLunas'])->name('pelanggan.belumLunas');

    // Paket View
    Route::get('/paket/view', [PaketController::class, 'viewPaket'])->name('paket.view');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/export', [LaporanController::class, 'export']);
    Route::post('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

    // Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Fonnte WhatsApp Integration
    Route::get('/fonnte', [FonnteController::class, 'index'])->name('fonnte.index');
    Route::post('/fonnte/store-token', [FonnteController::class, 'storeToken'])->name('fonnte.storeToken');
    Route::delete('/fonnte/delete', [FonnteController::class, 'deleteToken'])->name('fonnte.deleteToken');
    Route::post('/fonnte/send-message', [FonnteController::class, 'sendMessage'])->name('fonnte.sendMessage');

    // Fonnte Notification
    Route::get('/fonnte/notification', [FonnteNotificationController::class, 'index'])->name('fonnte.notification.index');
    Route::post('/fonnte/notification/save-settings', [FonnteNotificationController::class, 'saveSettings'])->name('fonnte.notification.saveSettings');
    Route::post('/fonnte/notification/send', [FonnteNotificationController::class, 'sendNotifications'])->name('fonnte.notification.send');
    Route::post('/fonnte/notification/save-payment-settings', [FonnteNotificationController::class, 'savePaymentSettings'])->name('fonnte.notification.savePaymentSettings');

    // Pelanggan Import/Export
    Route::get('/pelanggan/export', [PelangganController::class, 'export'])->name('pelanggan.export');
    Route::post('/pelanggan/import', [PelangganController::class, 'import'])->name('pelanggan.import');
});

// MikroTik Routes
Route::middleware('auth')->prefix('mikrotik')->group(function () {
    Route::get('/', [MikrotikController::class, 'index'])->name('mikrotik.index');
    Route::get('/create', [MikrotikController::class, 'create'])->name('mikrotik.create');
    Route::post('/', [MikrotikController::class, 'store'])->name('mikrotik.store');
    Route::get('/{mikrotik}/edit', [MikrotikController::class, 'edit'])->name('mikrotik.edit');
    Route::put('/{mikrotik}', [MikrotikController::class, 'update'])->name('mikrotik.update');
    Route::delete('/{mikrotik}', [MikrotikController::class, 'destroy'])->name('mikrotik.destroy');
    Route::post('/{mikrotik}/test', [MikrotikController::class, 'test'])->name('mikrotik.test');
    Route::post('/{mikrotik}/disconnect', [MikrotikController::class, 'disconnect'])->name('mikrotik.disconnect');
    Route::post('/sync-netwatch', [MikrotikController::class, 'syncNetwatch'])->name('mikrotik.sync-netwatch');
    Route::post('/add-to-netwatch', [MikrotikController::class, 'addToNetwatch'])->name('mikrotik.add-to-netwatch');
    Route::post('/remove-from-netwatch', [MikrotikController::class, 'removeFromNetwatch'])->name('mikrotik.remove-from-netwatch');
    Route::get('/network-status', [MikrotikController::class, 'getNetworkStatus'])->name('mikrotik.network-status');
});
