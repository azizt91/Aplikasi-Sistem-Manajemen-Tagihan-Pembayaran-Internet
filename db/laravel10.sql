-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
<<<<<<< HEAD
-- Waktu pembuatan: 29 Agu 2025 pada 13.49
=======
-- Waktu pembuatan: 19 Jun 2025 pada 13.21
>>>>>>> 61197d9900b7c73bbd0202bcc3e4ff74d40a6b01
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel10`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `banks`
--

CREATE TABLE `banks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_bank` varchar(255) NOT NULL,
  `pemilik_rekening` varchar(255) NOT NULL,
  `nomor_rekening` varchar(255) NOT NULL,
  `url_icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bulan`
--

CREATE TABLE `bulan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bulan`
--

INSERT INTO `bulan` (`id`, `bulan`, `created_at`, `updated_at`) VALUES
(1, 'Januari', NULL, NULL),
(2, 'Februari', NULL, NULL),
(3, 'Maret', NULL, NULL),
(4, 'April', NULL, NULL),
(5, 'Mei', NULL, NULL),
(6, 'Juni', NULL, NULL),
(7, 'Juli', NULL, NULL),
(8, 'Agustus', NULL, NULL),
(9, 'September', NULL, NULL),
(10, 'Oktober', NULL, NULL),
(11, 'November', NULL, NULL),
(12, 'Desember', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `fonnte`
--

CREATE TABLE `fonnte` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

<<<<<<< HEAD
--
-- Dumping data untuk tabel `fonnte`
--

INSERT INTO `fonnte` (`id`, `token`, `created_at`, `updated_at`) VALUES
(6, 'q3yqhiRwa2UXzpwGydZ2', NULL, NULL),
(7, 'q3yqhiRwa2UXzpwGydZ2', NULL, NULL);

=======
>>>>>>> 61197d9900b7c73bbd0202bcc3e4ff74d40a6b01
-- --------------------------------------------------------

--
-- Struktur dari tabel `fonnte_notification_settings`
--

CREATE TABLE `fonnte_notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `send_date_option` varchar(255) NOT NULL DEFAULT 'tanggal_pasang',
  `custom_message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `fonnte_notification_settings`
--

INSERT INTO `fonnte_notification_settings` (`id`, `is_active`, `send_date_option`, `custom_message`) VALUES
(1, 1, 'tanggal_pasang', '*Informasi Tagihan WiFi Anda*\r\n\r\nHai Bapak/Ibu @{{nama}}\r\nID Pelanggan @{{id_pelanggan}}\r\n\r\nInformasi tagihan Bapak/Ibu bulan ini adalah:\r\nJumlah Tagihan: *Rp@{{tagihan}}*\r\nPeriode Tagihan: *@{{periode}}*\r\n\r\nBayar tagihan anda di salah satu rekening dibawah ini:\r\n• Seabank 901307925714 An. TAUFIQ AZIZ\r\n• BCA 3621053653 An. TAUFIQ AZIZ\r\n• ShopeePay 081914170701 An. azizt91\r\n• Dana 089609497390 An. TAUFIQ AZIZ\r\n\r\nTerima kasih atas kepercayaan Anda menggunakan layanan kami.\r\n_____________________________\r\n*Ini adalah pesan otomatis, jika telah membayar tagihan, abaikan pesan ini*');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '2023_03_09_030035_create_pelanggan_table', 2),
(6, '2014_10_12_000000_create_users_table', 3),
(8, '2023_03_08_004320_create_paket_table', 3),
(9, '2023_03_09_031711_create_pelanggan_table', 3),
(10, '2023_03_09_050710_create_peket_table', 4),
(11, '2023_03_09_051135_create_paket_table', 5),
(13, '2023_03_09_120657_create_pelanggan_table', 7),
(14, '2023_03_16_020947_create_paket_table', 8),
(24, '2019_12_14_000001_create_personal_access_tokens_table', 9),
(25, '2023_03_09_075602_create_users_table', 9),
(26, '2023_03_16_021239_create_pelanggan_table', 9),
(27, '2023_06_17_083000_create_activity_logs_table', 10),
(28, '2023_03_16_035911_create_paket_table', 11),
(29, '2024_01_25_022807_create_pelanggan_table', 12),
(30, '2024_01_25_022858_create_paket_table', 13),
(31, '2024_01_25_023026_create_bulan_table', 14),
(32, '2024_01_25_023106_create_tagihan_table', 15),
(33, '2024_01_26_011526_create_tagihan_table', 16),
(34, '2024_01_27_085223_create_tagihan_table', 17),
(35, '2024_01_27_090523_create_pelanggan_table', 18),
(36, '2024_01_27_090551_create_tagihan_table', 19),
(37, '2024_01_27_090924_create_paket_table', 20),
(38, '2024_01_27_111503_create_bulan_table', 21),
(39, '2024_01_27_111549_create_paket_table', 22),
(40, '2024_01_27_111650_create_pelanggan_table', 23),
(41, '2024_01_27_111739_create_tagihan_table', 24),
(42, '2024_01_28_081806_create_bulan_table', 25),
(43, '2024_01_28_081846_create_tagihan_table', 26),
(44, '2024_01_28_082710_create_tagihan_table', 27),
(45, '2024_02_15_015348_add_jatuh_tempo_to_pelanggan_table', 28),
(46, '2024_02_19_023318_add_profile_picture_to_pelanggan_table', 29),
(47, '2024_02_22_095637_add_status_to_pelanggan_table', 30),
(48, '2025_02_15_080121_create_tripay_config_table', 31),
(49, '2025_02_15_093804_add_tanggal_pasang_to_pelanggan_table', 32),
(50, '2025_02_20_045627_create_settings_table', 33),
(51, '2014_10_12_100000_create_password_reset_tokens_table', 34),
(52, '2014_10_12_100000_create_password_resets_table', 34),
(53, '2019_08_19_000000_create_failed_jobs_table', 34),
(54, '2025_04_22_035638_add_foreign_key_to_tagihan_table', 34),
(55, '2025_05_22_081519_add_jumlah_dibayar_to_tagihan_table', 35),
(56, '2025_07_27_000001_add_coordinates_to_pelanggans', 36),
(57, '2025_07_30_000001_add_mikrotik_fields_to_pelanggan', 37),
(58, '2025_07_30_000002_create_mikrotik_configs_table', 37),
(59, '2025_07_30_112559_add_disconnected_status_to_mikrotik_configs', 38);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mikrotik_configs`
--

CREATE TABLE `mikrotik_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Nama konfigurasi MikroTik',
  `ip_address` varchar(255) NOT NULL COMMENT 'IP Address MikroTik',
  `port` int(11) NOT NULL DEFAULT 8728 COMMENT 'Port API MikroTik',
  `username` varchar(255) NOT NULL COMMENT 'Username MikroTik',
  `password` text NOT NULL COMMENT 'Password MikroTik (encrypted)',
  `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Status aktif konfigurasi',
  `last_connected` timestamp NULL DEFAULT NULL COMMENT 'Waktu terakhir terhubung',
  `connection_status` enum('connected','failed','error','never_tested','disconnected') DEFAULT 'never_tested',
  `notes` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket`
--

CREATE TABLE `paket` (
  `id_paket` varchar(6) NOT NULL,
  `paket` varchar(20) NOT NULL,
  `tarif` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `paket`
--

INSERT INTO `paket` (`id_paket`, `paket`, `tarif`, `created_at`, `updated_at`) VALUES
('P001', '20 Mbps', 250000, '2024-11-11 00:04:58', '2024-11-11 00:04:58'),
('P002', '10 Mbps', 200000, '2024-01-27 21:45:02', '2024-01-27 21:51:39'),
('P003', '8 Mbps', 180000, '2024-01-27 21:45:35', '2024-01-27 21:52:09'),
('P004', '5 Mbps', 150000, '2024-01-27 21:45:56', '2024-02-19 18:00:31'),
('P005', '3 Mbps', 100000, '2024-02-19 18:00:21', '2024-02-19 18:04:26'),
('P006', '1.5 MB', 50000, '2024-02-19 18:04:50', '2024-02-19 18:04:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` varchar(16) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `house_image` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(15) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(5) NOT NULL,
  `id_paket` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jatuh_tempo` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL,
  `network_status` enum('up','down','unknown') NOT NULL DEFAULT 'unknown',
  `last_seen` timestamp NULL DEFAULT NULL,
  `mikrotik_notes` text DEFAULT NULL,
  `tanggal_pasang` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

<<<<<<< HEAD
INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `alamat`, `latitude`, `longitude`, `house_image`, `whatsapp`, `ip_address`, `email`, `password`, `level`, `id_paket`, `created_at`, `updated_at`, `jatuh_tempo`, `profile_picture`, `status`, `network_status`, `last_seen`, `mikrotik_notes`, `tanggal_pasang`) VALUES
('C001', 'Aufa Itratun Afifah', 'Desa Pamutih', '-6.8533890', '109.5496982', 'public/houses/lDbuRNyg6bDsAZZF4ySikg7YcoKjvOJ37jU46hYr.png', '6289530105003', '10.10.10.64', 'cst1@mail.com', '12345678', 'User', 'P006', '2025-06-14 22:39:11', '2025-07-30 03:54:43', 'Tanggal 15', NULL, 'aktif', 'unknown', NULL, NULL, '2025-06-15'),
('C002', 'Saeful Anwar', 'Desa Pamutih', NULL, NULL, NULL, '62895366892738', NULL, 'cst2@mail.com', '12345678', 'User', 'P005', '2025-06-14 22:40:40', '2025-06-14 22:40:40', 'Tanggal 10', NULL, 'aktif', 'unknown', NULL, NULL, '2025-06-10'),
('C003', 'Devi K A', 'Desa Pamutih', NULL, NULL, NULL, '6287830529041', NULL, 'cst3@mail.com', '12345678', 'User', 'P004', '2025-06-14 22:42:24', '2025-06-14 22:42:24', 'Tanggal 9', NULL, 'aktif', 'unknown', NULL, NULL, '2025-05-09'),
('C004', 'Eko Wijoyo', 'Desa Pamutih', NULL, NULL, NULL, '6282328680025', NULL, 'cst4@mail.com', '12345678', 'User', 'P003', '2025-06-14 22:43:22', '2025-06-14 22:43:22', 'Tanggal 10', NULL, 'aktif', 'unknown', NULL, NULL, '2025-04-10'),
('C005', 'Bambang Widodo', 'Desa Pamutih', NULL, NULL, NULL, '6282116568036', NULL, 'cst5@mail.com', '12345678', 'User', 'P002', '2025-06-14 22:44:22', '2025-06-14 22:44:22', 'Tanggal 14', NULL, 'aktif', 'unknown', NULL, NULL, '2025-06-14'),
('C006', 'Taufiq Aziz', 'Desa Pamutih', '-6.8531400', '109.5501300', 'public/houses/X9cKULn5Y6CmVh0Lj3h6mhn5NR6k7tgP0bGcrWO5.png', '6281914170701', '10.10.10.61', 'cst6@mail.com', '12345678', 'User', 'P001', '2025-06-14 22:44:59', '2025-08-03 00:29:48', 'Tanggal 1', NULL, 'aktif', 'unknown', NULL, NULL, '2025-06-03');
=======
INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `alamat`, `whatsapp`, `email`, `password`, `level`, `id_paket`, `created_at`, `updated_at`, `jatuh_tempo`, `profile_picture`, `status`, `tanggal_pasang`) VALUES
('C001', 'Aufa Itratun Afifah', 'Desa Pamutih', '6289530105003', 'cst1@mail.com', '12345678', 'User', 'P006', '2025-06-14 22:39:11', '2025-06-14 22:39:11', 'Tanggal 15', NULL, 'aktif', '2025-06-15'),
('C002', 'Saeful Anwar', 'Desa Pamutih', '62895366892738', 'cst2@mail.com', '12345678', 'User', 'P005', '2025-06-14 22:40:40', '2025-06-14 22:40:40', 'Tanggal 10', NULL, 'aktif', '2025-06-10'),
('C003', 'Devi K A', 'Desa Pamutih', '6287830529041', 'cst3@mail.com', '12345678', 'User', 'P004', '2025-06-14 22:42:24', '2025-06-14 22:42:24', 'Tanggal 9', NULL, 'aktif', '2025-05-09'),
('C004', 'Eko Wijoyo', 'Desa Pamutih', '6282328680025', 'cst4@mail.com', '12345678', 'User', 'P003', '2025-06-14 22:43:22', '2025-06-14 22:43:22', 'Tanggal 10', NULL, 'aktif', '2025-04-10'),
('C005', 'Bambang Widodo', 'Desa Pamutih', '6282116568036', 'cst5@mail.com', '12345678', 'User', 'P002', '2025-06-14 22:44:22', '2025-06-14 22:44:22', 'Tanggal 14', NULL, 'aktif', '2025-06-14'),
('C006', 'Taufiq Aziz', 'Desa Pamutih', '6281914170701', 'cst6@mail.com', '12345678', 'User', 'P001', '2025-06-14 22:44:59', '2025-06-14 22:44:59', 'Tanggal 1', NULL, 'aktif', '2025-06-01');
>>>>>>> 61197d9900b7c73bbd0202bcc3e4ff74d40a6b01

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengeluarans`
--

CREATE TABLE `pengeluarans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bulan` int(11) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'favicon', 'public/icons/aGBxAQtrnwvY0u6oNHzfBetq2unFgUUZvpI5QszA.png', '2025-02-19 22:59:53', '2025-03-29 23:23:04'),
(2, 'logo_admin', 'public/logos/oNYsDMB2tZaUw8hPViTzgIgB1AfWWuE6Yy91hwbN.png', '2025-02-19 22:59:53', '2025-03-29 23:23:04'),
(3, 'logo_pelanggan', 'public/logos/B2hEgzbzKqKmcCOHJRpuBkcoISXXCK7oZG2618Io.png', '2025-02-19 22:59:53', '2025-03-29 23:23:04'),
(4, 'sidebar_logo', 'public/logos/6KSbhSkv0jXEyQnurHn5EzQiJ5zY0RxoPKKyLsUr.png', '2025-02-19 22:59:53', '2025-07-31 12:16:56'),
(5, 'receipt_logo', 'public/logos/eo6LgLLrHmUFdo2Ggzdgqo5i1otPTauunRYnVJpm.png', '2025-02-19 22:59:53', '2025-03-29 23:23:04'),
(6, 'sidebar_text', 'Selinggonet', '2025-02-19 22:59:53', '2025-06-14 22:47:05'),
(7, 'company_address', 'Pamutih 52371', '2025-02-19 22:59:53', '2025-02-19 22:59:53'),
(8, 'whatsapp_number', '081914170701', '2025-02-19 22:59:53', '2025-02-19 22:59:53'),
(9, 'pwa_name', 'Selinggonet', '2025-02-19 22:59:53', '2025-06-14 22:47:05'),
(10, 'pwa_short_name', 'Selinggonet', '2025-02-19 22:59:53', '2025-06-14 22:47:05'),
(11, 'pwa_description', 'Sistem Manajemen Tagihan Pembayaran Internet', '2025-02-19 22:59:53', '2025-03-29 23:29:17'),
(12, 'pwa_logo', 'public/logos/jLcSetpiMnBMg8Z2zqhT6oCpLD0dUMEIudukgF0S.png', '2025-02-19 22:59:53', '2025-03-30 00:16:50'),
(13, 'app_name_admin', 'Selinggonet', '2025-03-18 17:01:10', '2025-06-14 22:47:05'),
(14, 'app_name_pelanggan', 'Selinggonet', '2025-03-18 17:01:10', '2025-06-14 22:47:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tagihan`
--

CREATE TABLE `tagihan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `bulan` bigint(20) UNSIGNED NOT NULL,
  `tahun` year(4) NOT NULL,
  `id_pelanggan` varchar(6) NOT NULL,
  `tagihan` int(11) NOT NULL,
  `jumlah_dibayar` int(11) NOT NULL DEFAULT 0,
  `status` enum('BL','LS') NOT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `pembayaran_via` enum('cash','online') NOT NULL DEFAULT 'cash',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tagihan`
--

INSERT INTO `tagihan` (`id`, `reference`, `bulan`, `tahun`, `id_pelanggan`, `tagihan`, `jumlah_dibayar`, `status`, `tgl_bayar`, `pembayaran_via`, `created_at`, `updated_at`) VALUES
(1504, 'DEV-T35589248084FDSX3', 6, 2025, 'C001', 50000, 0, 'BL', NULL, 'cash', '2025-06-19 03:56:03', '2025-06-19 03:57:01'),
(1505, NULL, 6, 2025, 'C002', 100000, 0, 'BL', NULL, 'cash', '2025-06-19 03:56:03', '2025-06-19 03:56:03'),
(1506, NULL, 6, 2025, 'C003', 150000, 0, 'BL', NULL, 'cash', '2025-06-19 03:56:03', '2025-06-19 03:56:03'),
(1507, NULL, 6, 2025, 'C004', 180000, 0, 'BL', NULL, 'cash', '2025-06-19 03:56:03', '2025-06-19 03:56:03'),
(1508, NULL, 6, 2025, 'C005', 200000, 0, 'BL', NULL, 'cash', '2025-06-19 03:56:03', '2025-06-19 03:56:03'),
<<<<<<< HEAD
(1509, NULL, 6, 2025, 'C006', 250000, 0, 'BL', NULL, 'cash', '2025-06-19 03:56:03', '2025-06-19 03:56:03'),
(1510, NULL, 7, 2025, 'C001', 50000, 0, 'BL', NULL, 'cash', '2025-07-29 17:51:20', '2025-07-29 17:51:20'),
(1511, NULL, 7, 2025, 'C002', 100000, 0, 'BL', NULL, 'cash', '2025-07-29 17:51:20', '2025-07-29 17:51:20'),
(1512, NULL, 7, 2025, 'C003', 150000, 0, 'BL', NULL, 'cash', '2025-07-29 17:51:20', '2025-07-29 17:51:20'),
(1513, NULL, 7, 2025, 'C004', 180000, 0, 'BL', NULL, 'cash', '2025-07-29 17:51:20', '2025-07-29 17:51:20'),
(1514, NULL, 7, 2025, 'C005', 200000, 0, 'BL', NULL, 'cash', '2025-07-29 17:51:20', '2025-07-29 17:51:20'),
(1515, 'DEV-T35589264453CRVF4', 7, 2025, 'C006', 250000, 0, 'BL', NULL, 'cash', '2025-07-29 17:51:20', '2025-07-29 17:58:14');
=======
(1509, NULL, 6, 2025, 'C006', 250000, 0, 'BL', NULL, 'cash', '2025-06-19 03:56:03', '2025-06-19 03:56:03');
>>>>>>> 61197d9900b7c73bbd0202bcc3e4ff74d40a6b01

-- --------------------------------------------------------

--
-- Struktur dari tabel `tripay_config`
--

CREATE TABLE `tripay_config` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `api_key` varchar(255) NOT NULL,
  `private_key` varchar(255) NOT NULL,
  `merchant_code` varchar(255) NOT NULL,
  `payment_channel_url` varchar(255) NOT NULL DEFAULT 'https://tripay.co.id/api-sandbox/merchant/payment-channel',
  `transaction_create_url` varchar(255) NOT NULL DEFAULT 'https://tripay.co.id/api-sandbox/transaction/create',
  `transaction_detail_url` varchar(255) NOT NULL DEFAULT 'https://tripay.co.id/api-sandbox/transaction/detail',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tripay_config`
--

INSERT INTO `tripay_config` (`id`, `is_enabled`, `api_key`, `private_key`, `merchant_code`, `payment_channel_url`, `transaction_create_url`, `transaction_detail_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'DEV-mhY2fWq180nc1vn6RhV57C0RIffEO56DVe7sJlwB', 'bIUJE-9OykS-d0L1c-cmIiW-axufz', 'T35589', 'https://tripay.co.id/api-sandbox/merchant/payment-channel', 'https://tripay.co.id/api-sandbox/transaction/create', 'https://tripay.co.id/api-sandbox/transaction/detail', '2025-02-15 01:17:53', '2025-06-19 03:48:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `level` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `profile_picture`, `level`, `created_at`, `updated_at`, `remember_token`) VALUES
(5, 'Admin', 'admin@gmail.com', '$2y$10$mnvJ6YKSHGQ2hWGPjElE0OjHLJJakOo1hEWvJNxJCpMTwk1V5H8Re', NULL, 'Admin', '2025-06-19 04:22:48', '2025-06-19 04:22:48', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `bulan`
--
ALTER TABLE `bulan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `fonnte`
--
ALTER TABLE `fonnte`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `fonnte_notification_settings`
--
ALTER TABLE `fonnte_notification_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mikrotik_configs`
--
ALTER TABLE `mikrotik_configs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mikrotik_configs_ip_address_port_index` (`ip_address`,`port`),
  ADD KEY `mikrotik_configs_is_active_index` (`is_active`);

--
-- Indeks untuk tabel `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id_paket`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD KEY `pelanggan_id_paket_foreign` (`id_paket`);

--
-- Indeks untuk tabel `pengeluarans`
--
ALTER TABLE `pengeluarans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`(191),`tokenable_id`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tagihan_bulan_foreign` (`bulan`),
  ADD KEY `tagihan_id_pelanggan_foreign` (`id_pelanggan`);

--
-- Indeks untuk tabel `tripay_config`
--
ALTER TABLE `tripay_config`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `banks`
--
ALTER TABLE `banks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `bulan`
--
ALTER TABLE `bulan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `fonnte`
--
ALTER TABLE `fonnte`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `fonnte_notification_settings`
--
ALTER TABLE `fonnte_notification_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `mikrotik_configs`
--
ALTER TABLE `mikrotik_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengeluarans`
--
ALTER TABLE `pengeluarans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
<<<<<<< HEAD
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1516;
=======
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1510;
>>>>>>> 61197d9900b7c73bbd0202bcc3e4ff74d40a6b01

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
<<<<<<< HEAD
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
=======
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
>>>>>>> 61197d9900b7c73bbd0202bcc3e4ff74d40a6b01

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `tagihan_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
