# Aplikasi Sistem Manajemen Tagihan Pembayaran Internet

Aplikasi Sistem Manajemen Tagihan Pembayaran Internet dirancang untuk mempermudah proses pengelolaan dan pembayaran tagihan internet. Aplikasi ini dibuat menggunakan Laravel v10.* dan minimal PHP v8.2 jadi apabila pada saat proses instalasi atau penggunaan terdapat error atau bug kemungkinan karena versi dari PHP yang tidak support.

---

### **Fitur yang Tersedia**
- **Dashboard Admin:** Berisi semua informasi seperti fitur filter berdasarkan bulan dan tahun maka akan menampilkan prndapatan, pengeluaran, total profit dsb.
- **Manajemen Paket Internet:** Kelola informasi paket layanan.
- **Manajemen Pelanggan:** Tambah, edit, dan hapus data pelanggan.
- **Autogenerate Akun Pelanggan:** Email dan password pelanggan dibuat otomatis saat data pelanggan ditambahkan.
- **Buat Tagihan:** Buat tagihan otomatis semua pelanggan berdasarkan bulan dan tahun
- **Lihat Tagihan:** Buka data tagihan berdasarkan bulan dan tahun
- **Tagihan Lunas:** Menampilkan tagihan lunas semua pelanggan.
- **Rekening Bank:** Tambah, edit, dan hapus data rekening bank atau e-wallet.
- **Manajemen Pengguna Sistem:** Tambah, edit, dan hapus data pengguna sistem.
- **Manajemen Pengeluaran:** Tambah, edit, dan hapus data pengeluaran.
- **Konfigurasi Tripay:** Untuk mengkonfigurasikan data Payment Gateway Tripay.
- **Setting:** Untuk merubah icon vavicon atau icon aplikasi, merubah nama aplikasi dsb.
- **Role Admin:** Akses fitur khusus untuk pengelolaan aplikasi secara penuh.

---

### **Persyaratan Sistem**
- PHP minimal versi **8.2**  
- Sudah terinstal **XAMPP** atau perangkat sejenis  
  [Download XAMPP di sini](https://www.apachefriends.org/download.html)  
- Composer telah terinstal di komputer  
  [Unduh Composer di sini](https://getcomposer.org/download/)  

---

### **Langkah Instalasi**
1. **Clone repository**:
   ```bash
   git clone https://github.com/azizt91/Aplikasi-Sistem-Manajemen-Tagihan-Pembayaran-Internet.git
   ```
2. **Masuk ke folder proyek**:
   ```bash
   cd Aplikasi-Sistem-Manajemen-Tagihan-Pembayaran-Internet
   ```
3. **Instal dependensi Laravel menggunakan Composer**:
   ```bash
   composer install
   ```
4. **Buat file `.env` dari contoh**:
   ```bash
   cp .env.example .env
   ```
5. **Buat database baru di phpMyAdmin**:
   - Masuk ke phpMyAdmin dan buat database baru sesuai kebutuhan.
   - Import file SQL dari folder `db/laravel10.sql` ke dalam database.

6. **Sesuaikan konfigurasi database di file `.env`**:
   Edit baris berikut di file `.env` agar sesuai dengan database yang telah kamu buat:
   ```env
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

8. **Membuat symlink (symbolic link)**:
   Agar file bisa di akses dari URL publik
   ```bash
   php artisan storage:link
   ```

9. **Jalankan Database Seeder**:
   ```bash
   php artisan db:seed --class=NamaSeeder 
   ```

10. **Jalankan aplikasi lokal**:
    ```bash
    php artisan serve
       ```

11. Aplikasi akan dapat diakses biasanya di
    ```bash
    http://127.0.0.1:8000
     ```

12. **Informasi Login Admin Default**:
    
    Email: `admin@gmail.com`
    
    Password: `password123`

14. **Anda juga bisa membuat akun admin baru langsung dengan langkah seperti dibawah ini**:
    
    a. Klik Log in here
    ![image](https://github.com/user-attachments/assets/f4af2dc1-2e4e-42e9-bee5-1a87f0ee6f5b)
    b. klik Create an account
    ![image](https://github.com/user-attachments/assets/776c1153-3a92-4d21-9f46-2e4047a25a66)
    c. Anda akan diarahkan ke Dashboard Admin jika berhasil membuat akun


Selamat, aplikasi siap diakses! 😊  

---

### **Catatan**
- **Tambahkan Data Paket Terlebih Dahulu**: Sebelum menambahkan pelanggan, pastikan kamu sudah menambahkan data paket.  
- **Email dan Password Pelanggan**: Email dan password untuk login pelanggan akan otomatis dibuat saat menambahkan data pelanggan.

---

### **Author**
- Facebook : [Taufiq Aziz](https://www.facebook.com/azizt91) 
- Instagram : [Taufiq Aziz](https://www.instagram.com/azizt91) 
- Threads : [Taufiq Aziz](https://www.threads.net/@azizt91) 
- YouTube : [Taufiq Aziz](https://youtube.com/@taufiqaziz1691) 
- X : [Taufiq Aziz](https://x.com/azizt91)

---

## License
[MIT license](https://opensource.org/licenses/MIT).
