<?php

namespace App\Services;

use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Pengeluaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk caching data dashboard.
 * 
 * Mengurangi query database untuk data yang sering diakses.
 */
class DashboardCacheService
{
    /**
     * Cache duration in seconds (5 menit).
     */
    public const CACHE_DURATION = 300;

    /**
     * Cache keys.
     */
    public const CACHE_KEY_STATS = 'dashboard_stats';
    public const CACHE_KEY_MONTHLY = 'dashboard_monthly_';
    public const CACHE_KEY_CHART = 'dashboard_chart_';

    /**
     * Get dashboard statistics with caching.
     */
    public function getDashboardStats(): array
    {
        return Cache::remember(self::CACHE_KEY_STATS, self::CACHE_DURATION, function () {
            return [
                'jumlah_paket' => Paket::count(),
                'jumlah_user' => DB::table('users')->count(),
                'jumlah_pelanggan_aktif' => Pelanggan::where('status', 'aktif')->count(),
                'jumlah_pelanggan_nonaktif' => Pelanggan::where('status', 'nonaktif')->count(),
            ];
        });
    }

    /**
     * Get monthly revenue data with caching.
     */
    public function getMonthlyData(int $bulan, int $tahun): array
    {
        $cacheKey = self::CACHE_KEY_MONTHLY . $bulan . '_' . $tahun;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($bulan, $tahun) {
            // Active pelanggan IDs
            $activePelangganIds = Pelanggan::where('status', 'aktif')->pluck('id_pelanggan');

            // Total revenue
            $totalRevenue = Tagihan::where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('status', 'LS')
                ->sum('tagihan');

            // Total expenses
            $pengeluaranBulanIni = Pengeluaran::where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->sum('jumlah');

            // Pelanggan lunas count
            $jumlahPelangganLunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
                ->whereHas('tagihan', function ($query) use ($tahun, $bulan) {
                    $query->where('status', 'LS')
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan);
                })->count();

            // Pelanggan belum lunas count
            $jumlahPelangganBelumLunas = Pelanggan::whereIn('id_pelanggan', $activePelangganIds)
                ->whereHas('tagihan', function ($query) use ($tahun, $bulan) {
                    $query->where(function ($q) {
                        $q->where('status', '!=', 'LS')->orWhereNull('status');
                    })
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan);
                })->count();

            return [
                'total_revenue' => (int) $totalRevenue,
                'pengeluaran' => (int) $pengeluaranBulanIni,
                'net_revenue' => (int) ($totalRevenue - $pengeluaranBulanIni),
                'jumlah_pelanggan_lunas' => $jumlahPelangganLunas,
                'jumlah_pelanggan_belum_lunas' => $jumlahPelangganBelumLunas,
            ];
        });
    }

    /**
     * Get yearly chart data with caching.
     */
    public function getChartData(int $tahun): array
    {
        $cacheKey = self::CACHE_KEY_CHART . $tahun;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($tahun) {
            $pendapatan = [];
            $pengeluaran = [];

            for ($month = 1; $month <= 12; $month++) {
                $pendapatan[] = (int) Tagihan::where('tahun', $tahun)
                    ->where('bulan', $month)
                    ->where('status', 'LS')
                    ->sum('tagihan');

                $pengeluaran[] = (int) Pengeluaran::where('tahun', $tahun)
                    ->where('bulan', $month)
                    ->sum('jumlah');
            }

            return [
                'pendapatan' => $pendapatan,
                'pengeluaran' => $pengeluaran,
            ];
        });
    }

    /**
     * Get network status statistics with caching.
     */
    public function getNetworkStats(): array
    {
        return Cache::remember('network_stats', 60, function () { // 1 minute cache
            $totalCustomers = Pelanggan::where('status', 'aktif')->count();
            $onlineCustomers = Pelanggan::where('status', 'aktif')->where('network_status', 'up')->count();
            $offlineCustomers = Pelanggan::where('status', 'aktif')->where('network_status', 'down')->count();
            $unknownCustomers = $totalCustomers - $onlineCustomers - $offlineCustomers;

            return [
                'total' => $totalCustomers,
                'online' => $onlineCustomers,
                'offline' => $offlineCustomers,
                'unknown' => $unknownCustomers,
                'online_percentage' => $totalCustomers > 0 ? round(($onlineCustomers / $totalCustomers) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Clear all dashboard caches.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_STATS);
        Cache::forget('network_stats');

        // Clear monthly caches for current year
        $currentYear = now()->year;
        for ($month = 1; $month <= 12; $month++) {
            Cache::forget(self::CACHE_KEY_MONTHLY . $month . '_' . $currentYear);
        }

        // Clear chart caches
        Cache::forget(self::CACHE_KEY_CHART . $currentYear);
        Cache::forget(self::CACHE_KEY_CHART . ($currentYear - 1));
    }

    /**
     * Clear cache when tagihan is updated.
     */
    public function clearTagihanCache(int $bulan, int $tahun): void
    {
        Cache::forget(self::CACHE_KEY_MONTHLY . $bulan . '_' . $tahun);
        Cache::forget(self::CACHE_KEY_CHART . $tahun);
    }
}
