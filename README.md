# EPerpus Cakrawala - CMS Panel Admin & Android REST API

Backend API dan Panel Admin CMS untuk aplikasi perpustakaan digital **EPerpus Cakrawala** berbasis **Jetpack Compose Android**.

---

## 🛠️ Stack Teknologi

- **Backend & CMS:** Laravel 13 (PHP 8.5)
- **Database:** PostgreSQL 18 (`cakrawala_db`)
- **Autentikasi Mobile:** Laravel Sanctum (Personal Access Token / Bearer Token)
- **Styling Admin Panel:** Tailwind CSS & Plus Jakarta Sans
- **Storage:** Public Disk Symlink (untuk Cover Buku, File PDF E-Reader, dan Gambar Banner/Artikel)

---

## 🔑 Kredensial Default

### 1. Akun Administrator (Panel Admin CMS)
- **URL Login:** `http://localhost:8000/admin/login`
- **Email:** `admin@cakrawala.id`
- **Password:** `admin123`

### 2. Akun Pengguna Mobile (Jetpack Compose Android)
- **Email:** `fadhel@gmail.com`
- **Password:** `password123`

---

## 🚀 Cara Menjalankan Server

1. **Jalankan Server Laravel:**
   ```bash
   php artisan serve
   ```
   Akses panel admin di browser: [http://localhost:8000/admin](http://localhost:8000/admin)

2. *(Opsional)* Jika ingin mengompilasi aset frontend secara lokal:
   ```bash
   npm install
   npm run build
   ```

---

## 📱 Panduan Integrasi REST API (Jetpack Compose Android)

Base URL: `http://10.0.2.2:8000/api` (jika menggunakan Android Emulator) atau IP LAN komputer Anda.

### 1. Autentikasi (`/api/auth/*`)
- `POST /api/auth/register` - Registrasi pengguna baru `{ name, email, password }`
- `POST /api/auth/login` - Login pengguna `{ email, password }` -> mengembalikan `token` dan data `user`
- `POST /api/auth/google-login` - Login via Google `{ google_id, email, name, avatar }`
- `GET /api/auth/profile` *(Perlu Token)* - Mengambil profil dan statistik buku di rak
- `POST /api/auth/logout` *(Perlu Token)* - Menghapus sesi token aktif

### 2. Beranda (`/api/home`)
- `GET /api/home` - Mengambil seluruh data Beranda sekaligus:
  - Banner Slider Carousel
  - Rekomendasi Buku
  - Buku Populer
  - Koleksi Baru Bulan Ini
  - Artikel & Kabar Terbaru
  - Daftar Kategori

### 3. Katalog Buku & E-Reader (`/api/books/*`)
- `GET /api/books` - Daftar buku berpaginasi.
  - Parameter query: `q` (pencarian), `category_id`, `category_slug`, `popular=1`, `recommended=1`, `new=1`
- `GET /api/books/{id}` - Detail buku beserta status rak pengguna (`is_in_wishlist`, `is_reading`, `progress_percentage`)
- `GET /api/books/{id}/pdf` - Stream / unduh file dokumen PDF untuk *PDF Reader* di Android

### 4. Kategori (`/api/categories/*`)
- `GET /api/categories` - Daftar kategori beserta total jumlah buku di dalamnya
- `GET /api/categories/{id}` - Detail kategori dan daftar bukunya

### 5. Artikel & Berita Perpustakaan (`/api/articles/*`)
- `GET /api/articles` - Daftar artikel & kabar (query `type=kabar` atau `type=artikel`)
- `GET /api/articles/{id}` - Detail artikel lengkap

### 6. Rak Saya (`/api/shelf/*`) *(Perlu Token)*
- `GET /api/shelf` - Mengambil buku di rak (tab `reading` dan `wishlist`)
- `POST /api/shelf/wishlist/toggle` - Simpan / hapus buku dari wishlist `{ book_id }`
- `POST /api/shelf/progress` - Memperbarui progress membaca dari PDF reader `{ book_id, last_page_read, progress_percentage }`
- `DELETE /api/shelf/{id}` - Menghapus buku dari rak

### 7. Penanda Halaman (Bookmarks PDF) (`/api/bookmarks/*`) *(Perlu Token)*
- `GET /api/books/{bookId}/bookmarks` - Ambil daftar bookmark untuk buku tertentu
- `POST /api/bookmarks` - Buat penanda halaman baru `{ book_id, page_number, note }`
- `DELETE /api/bookmarks/{id}` - Hapus penanda halaman

### 8. Ulasan & Feedback (`/api/reviews`)
- `GET /api/reviews` - Mengambil testimoni publik
- `POST /api/reviews` *(Perlu Token)* - Kirim rating (1-5) dan ulasan pengalaman membaca `{ rating, comment, book_id }`

---

## 🧪 Pengujian Unit & Fitur

Jalankan test suite otomatis untuk memverifikasi fungsionalitas panel admin dan endpoint API:
```bash
php artisan test
```
