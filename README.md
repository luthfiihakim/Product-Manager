# Product Manager

Aplikasi manajemen produk berbasis PHP dan MySQL (PDO) untuk Praktikum 3 Pemrograman Web. Mendukung operasi CRUD dengan validasi, perlindungan CSRF pada hapus, dan tampilan card responsif.

## Fitur

- Create: tambah produk (nama, kategori, harga, stok)
- Read: daftar produk dalam card responsif (Flexbox)
- Update: edit produk berdasarkan ID
- Delete: hapus produk lewat POST dengan token CSRF
- Validasi: nama minimal 3 karakter, harga lebih dari 0, stok 0 atau lebih, nama produk unik
- Pola PRG: redirect setelah create, update, dan delete agar refresh tidak menggandakan data
- Bonus: pencarian (nama/kategori) dan filter kategori memakai parameter GET

## Keamanan

- Semua query memakai PDO prepared statement
- Semua output di-escape dengan htmlspecialchars (ENT_QUOTES, UTF-8)
- Hapus hanya menerima POST dan memeriksa token CSRF

## Struktur Proyek

- config/db.php: koneksi database (PDO)
- database/store_db.sql: skema database
- public/index.php: daftar produk (READ)
- public/create.php: tambah produk (CREATE)
- public/edit.php: ubah produk (UPDATE)
- public/delete.php: hapus produk (DELETE)
- public/assets/style.css: tampilan

## Cara Menjalankan

1. Install XAMPP, lalu Start Apache dan MySQL di XAMPP Control Panel
2. Salin folder product-manager ke C:\xampp\htdocs
3. Buka http://localhost/phpmyadmin
4. Import file database/store_db.sql (menu Import, atau tempel isinya di tab SQL lalu klik Go)
5. Pastikan pengaturan di config/db.php sesuai (default XAMPP: user root, password kosong, database store_db)
6. Buka http://localhost/product-manager/public/index.php di browser

## Teknologi

- PHP 8 dan PDO
- MySQL
- HTML dan CSS (Flexbox)

## Author

Muhammad Luthfi Hakim Lubis (250180104)