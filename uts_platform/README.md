# 📖 Buku Tamu Digital - UTS Teknologi Platform

**Mata Kuliah:** 11251301501 Teknologi Platform  
**Program Studi:** Sistem Informasi S1 - Universitas Uyelindo  
**Nama Mahasiswa:** Petrus Admo Samon KW  
**NIM:** 25110027  

---

## 📌 Deskripsi Proyek
Proyek ini merupakan implementasi platform engineering untuk aplikasi **Buku Tamu Digital** berbasis web PHP dan database MySQL 8.0. Arsitektur dibangun menggunakan container Docker dan diorkestrasikan dengan Docker Compose, dilengkapi dengan volume persistence dan custom bridge network.

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Prasyarat
- Docker Engine dan Docker Compose telah terpasang.

### 2. Menjalankan Multi-Container
Jalankan perintah berikut di folder proyek `uts_platform`:
```bash
docker compose up -d
```

### 3. Mengakses Aplikasi
Buka peramban (browser) dan akses:
```
http://localhost:8000
```

### 4. Menghentikan Layanan
```bash
docker compose down
```
*(Data buku tamu akan tetap tersimpan karena menggunakan named volume `db_data`)*.

---

## 📁 Struktur Direktori
```
uts_platform/
├── app/
│   └── index.php           # Kode antarmuka & logika aplikasi Buku Tamu
├── config/
│   ├── .env                # File kredensial (diabaikan oleh git)
│   └── .env.example        # Template konfigurasi environment
├── logs/                   # Log akses aplikasi (volume mount)
├── screenshots/            # Bukti pengujian & eksekusi tiap tahap
├── Dockerfile              # Konfigurasi container PHP 8.2 Apache
├── docker-compose.yml      # Orkestrasi web + database MySQL 8.0
├── .gitignore              # Pencegahan kebocoran berkas rahasia
└── README.md               # Dokumentasi proyek
```
