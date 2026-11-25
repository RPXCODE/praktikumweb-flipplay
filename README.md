# FlipPlay REST API (Laravel 12) - Modul 4

Pengembangan backend lanjutan **FlipPlay** dari simulasi terminal OOP (Modul 3) menjadi **RESTful API** menggunakan **Laravel 12** dan database SQLite/MySQL, sesuai ketentuan praktikum Pemrograman Web Modul 4.

---

## 📌 Pemenuhan Ketentuan Demo Modul 4

| No | Ketentuan Demo | Implementasi di Proyek |
|---|---|---|
| 1 | **Repository GitHub & Laravel Project** | Proyek berbasis Laravel 12 dengan struktur standar, git repository diinisialisasi. |
| 2 | **Minimal Dua Entitas dengan Relasi Eloquent** | Model `Media` dan `User` menggunakan relasi Many-to-Many (`belongsToMany`) melalui tabel pivot `media_user` untuk koleksi media pengguna. |
| 3 | **Migration & Struktur Tabel yang Benar** | Migration `create_media_table`, `create_users_table`, dan pivot `create_media_user_table` dengan foreign key dan cascade delete. |
| 4 | **Controller CRUD Standar untuk Media** | `MediaController` mengimplementasikan method `index`, `show`, `store`, `update`, dan `destroy` dengan status code HTTP yang tepat (200, 201). |
| 5 | **Form Request Validation & Error 422** | `StoreMediaRequest`, `UpdateMediaRequest`, dan `AttachMediaRequest` dengan validasi judul unik, tipe data, serta respons error 422 jika input tidak valid. |
| 6 | **API Resource & whenLoaded** | `MediaResource` dan `UserResource` menjaga respons JSON konsisten, termasuk kondisional relasi menggunakan `whenLoaded('users')` dan `whenLoaded('media')`. |
| 7 | **Seluruh Endpoint di routes/api.php** | Didaftarkan di `routes/api.php` dan terverifikasi via `php artisan route:list`. |
| 8 | **Custom Action Relasi Antartabel** | `attachMedia` (menambahkan media ke koleksi pengguna dengan proteksi duplikasi 422) dan `detachMedia` (menghapus media dari koleksi pengguna). |

---

## 🚀 Cara Menjalankan Proyek

1. **Instal dependensi Composer** (jika baru di-clone):
   ```bash
   composer install
   ```
2. **Setup file Environment & App Key**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Jalankan Migration & Seeder data awal**:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. **Jalankan Server Development**:
   ```bash
   php artisan serve
   ```
   API akan aktif di: `http://127.0.0.1:8000/api`

5. **Jalankan Pengujian Otomatis (Feature Test)**:
   ```bash
   php artisan test
   ```

---

## 📡 Daftar Endpoint API

### 1. Entitas Media (CRUD Standar)
| Method | Endpoint | Deskripsi | Status Code |
|---|---|---|---|
| `GET` | `/api/media` | Menampilkan semua media (dukung filter `?title=...`, `?type=...`, `?genre=...`) | `200 OK` |
| `POST` | `/api/media` | Menambahkan media baru | `201 Created` / `422` |
| `GET` | `/api/media/{id}` | Menampilkan detail media beserta daftar pengguna yang mengoleksinya (`whenLoaded`) | `200 OK` / `404` |
| `PUT/PATCH` | `/api/media/{id}` | Memperbarui data media | `200 OK` / `422` |
| `DELETE` | `/api/media/{id}` | Menghapus media dari sistem | `200 OK` / `404` |

### 2. Entitas User & Koleksi Media (Custom Action)
| Method | Endpoint | Deskripsi | Status Code |
|---|---|---|---|
| `GET` | `/api/users` | Menampilkan seluruh pengguna | `200 OK` |
| `GET` | `/api/users/{id}/collection` | Menampilkan koleksi media milik pengguna tertentu | `200 OK` |
| `POST` | `/api/users/{id}/collection` | **Custom Action (Attach)**: Menambahkan media ke koleksi pengguna | `201 Created` / `422 Duplikat` |
| `DELETE` | `/api/users/{id}/collection/{mediaId}` | **Custom Action (Detach)**: Menghapus media dari koleksi pengguna | `200 OK` / `404` |

---

## 🧪 Pengujian Postman

File koleksi Postman sudah disertakan di root repositori:
📁 **`FlipPlay_API_Modul4.postman_collection.json`**

### Cara Import ke Postman:
1. Buka aplikasi **Postman**.
2. Klik tombol **Import** di kiri atas.
3. Drag & drop file `FlipPlay_API_Modul4.postman_collection.json` atau pilih file tersebut.
4. Pastikan server Laravel sudah berjalan (`php artisan serve`).
5. Jalankan request satu per satu dari folder:
   - **Media (CRUD & Filter)** (Happy Path & Failure Path 422)
   - **Users & Collections (Custom Actions)** (Attach, Duplicate 422, Detach, 404)
