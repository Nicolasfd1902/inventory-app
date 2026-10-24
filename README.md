# Aplikasi Inventaris Sederhana (Laravel 10)

Aplikasi web manajemen inventaris sederhana yang dibangun menggunakan Laravel 10, MySQL, dan Fetch API (Vanilla JS).

## Fitur Utama
- **Daftar & Pencarian Barang:** Menampilkan data barang beserta filter pencarian *realtime* tanpa *reload* halaman (Fetch API).
- **Form Input & Edit Barang:** Tambah dan ubah data barang lengkap dengan validasi (*Kode unik*, *Stok & Harga >= 0*).
- **Pencatatan Stok Masuk & Keluar:** Transaksi perubahan stok dengan **Database Transaction**.
- **Riwayat Stok:** Log mutasi stok masuk dan keluar beserta alasannya.

## Persyaratan Sistem
- PHP >= 8.2
- Composer
- MySQL

## Cara Menjalankan Project

1. **Clone Repositori**
   ```bash
   git clone [https://github.com/Nicolasfd1902/inventory-app.git](hthttps://github.com/Nicolasfd1902/inventory-app.git)
   cd inventory-app
