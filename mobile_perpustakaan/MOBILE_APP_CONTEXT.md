# Mobile Perpustakaan - AI Context & Architecture

Dokumen ini berisi informasi teknis mengenai aplikasi mobile "Sistem Manajemen Perpustakaan" yang dikembangkan menggunakan Flutter.

## 🛠 Tech Stack & Dependencies
- **Framework:** Flutter (Dart)
- **State Management:** `provider` (ChangeNotifier)
- **HTTP Client:** `http` (untuk komunikasi dengan REST API Laravel)
- **Local Storage:** `shared_preferences` (untuk menyimpan JWT Token dan Role)
- **Utilities:** 
  - `intl`: Pemformatan tanggal.
  - `image_picker`: Mengambil gambar dari galeri untuk cover buku.

## 🏗 Architecture (Folder Structure)
- `lib/main.dart`: Entry point aplikasi dan konfigurasi `MultiProvider`.
- `lib/providers/`: Berisi logic state management dan interaksi API (Book, Member, Loan, Category).
- `lib/screens/`: Berisi UI (Widget) untuk setiap fitur.
- `lib/services/`: Berisi `AuthService` untuk login, register, dan manajemen token.
- `lib/utils/`: Berisi konstanta aplikasi (Base URL API, dsb).

## 🔑 Authentication Flow
1. **Login:** Mengirim email & password ke `/api/login`, menyimpan JWT Token, Nama, dan Role ke `shared_preferences`.
2. **Register:** Pengguna baru dapat mendaftar melalui `/api/register` (otomatis role `user`).
3. **Role Based Access:** 
   - `admin`: Memiliki akses penuh (CRUD) ke semua modul.
   - `user`: Hanya memiliki akses baca (Read-only) pada katalog, anggota, dan riwayat.

## 📱 Modules & Screens Detail
### 1. Katalog Buku (`BookScreen` & `BookFormScreen`)
- Menampilkan daftar buku dengan gambar cover, penulis, stok, dan deskripsi.
- Mendukung fitur Tambah/Edit/Hapus (Admin).
- **Endpoint:** `/api/books` (CRUD).

### 2. Manajemen Anggota (`MemberScreen` & `MemberFormScreen`)
- Mengelola data anggota perpustakaan (Nama, ID/NIM, Alamat, dsb).
- **Endpoint:** `/api/members` (CRUD).

### 3. Riwayat Peminjaman (`LoanScreen` & `LoanFormScreen`)
- Mencatat transaksi peminjaman buku oleh anggota.
- Menampilkan status "Dipinjam" atau "Dikembalikan".
- **Endpoint:** `/api/loans` (CRUD).

### 4. Kategori/Genre (`CategoryScreen`)
- Manajemen kategori buku menggunakan sistem Dialog Form.
- **Endpoint:** `/api/categories` (CRUD).

## 📡 API Integration Notes
- **Base URL:** `http://192.168.1.15:8000/api` (Dapat diubah di `lib/utils/constants.dart`).
- **Headers:** Setiap request (kecuali login/register) memerlukan header `Authorization: Bearer {token}`.
- **Image Handling:** Cover buku dikirim sebagai `MultipartFile` melalui `http.MultipartRequest`.

## 🤖 Instructions for Other AI
- Jika ingin memodifikasi UI, fokuslah pada folder `lib/screens/`.
- Jika ingin menambah logic bisnis atau endpoint baru, tambahkan di folder `lib/providers/`.
- Pastikan untuk selalu mengecek role (`isAdmin`) sebelum menampilkan fitur modifikasi data.
