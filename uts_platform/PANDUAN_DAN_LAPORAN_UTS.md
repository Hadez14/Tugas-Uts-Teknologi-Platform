# PANDUAN LENGKAP & REKAP EKSEKUSI UTS TEKNOLOGI PLATFORM
**Universitas Uyelindo | Semester Ganjil 2026/2027**  
**Mata Kuliah:** 11251301501 Teknologi Platform | Prodi Sistem Informasi S1  
**Nama Mahasiswa:** Petrus Admo Samon KW  
**NIM:** <25110027>  
**Lokasi Proyek:** `C:\Users\Pedro Saman\Tugas Kampus\uts_platform`  

---

## RINGKASAN STATUS KESIAPAN SISTEM
Seluruh berkas arsitektur, source code aplikasi Buku Tamu, Dockerfile, docker-compose.yml, environment variable, dan dokumentasi telah berhasil dibuat dan siap digunakan:
1. `uts_platform/app/index.php` (Aplikasi PHP modern terhubung ke database & logging)
2. `uts_platform/config/.env` & `.env.example` (Konfigurasi kredensial aman)
3. `uts_platform/Dockerfile` (Base image PHP 8.2 Apache port 8000 + ekstensi mysqli)
4. `uts_platform/docker-compose.yml` (Orkestrasi multi-container: Web + MySQL 8.0 + volume db_data + custom network)
5. `uts_platform/README.md` & `.gitignore` (Standar version control)
6. `uts_platform/logs/contoh.html` (Hasil pengujian HTTP curl)

---

## TAHAP 1 — LINGKUNGAN KERJA & COMMAND LINE (10 POIN)

### 1. Tujuan
Membuat struktur direktori kerja terstandar, berpindah direktori, dan merekam 10 riwayat perintah menggunakan redirection (`>`, `>>`) dan pipeline (`|`) serta filter `grep`.

### 2. Perintah yang Dijalankan (PowerShell / WSL)
```powershell
# Di PowerShell:
New-Item -ItemType Directory -Force -Path "uts_platform\app", "uts_platform\config", "uts_platform\logs", "uts_platform\screenshots"
Set-Location "uts_platform\app"

# Di Linux / WSL:
mkdir -p ~/uts_platform/{app,config,logs,screenshots}
cd ~/uts_platform/app
history | tail -n 10 | tac > riwayat_perintah.txt
cat riwayat_perintah.txt
grep "docker" riwayat_perintah.txt
```

### 3. Penjelasan
- `mkdir -p` / `New-Item`: Membuat 4 folder kerja sekaligus.
- `>` (Overwrite Redirection): Menyimpan riwayat 10 perintah terakhir secara terbalik (`tac`).
- `grep`: Menyaring baris yang mengandung kata kunci "docker".

---

## TAHAP 2 — USER, HAK AKSES & PROSES (15 POIN)

### 1. Tujuan
Membuat user khusus `dev_pedro`, grup `platform_uts`, mengatur permission folder dan file rahasia `.env`, serta memantau proses sistem (PID).

### 2. Perintah Linux / WSL
```bash
# 1. Buat grup dan user
sudo groupadd platform_uts
sudo useradd -m -g platform_uts -s /bin/bash dev_pedro

# 2. Atur kepemilikan folder ke dev_pedro:platform_uts
sudo chown -R dev_pedro:platform_uts ~/uts_platform

# 3. Atur permission folder: Pemilik rwx (7), Grup r-x (5), Lainnya --- (0)
# Simbolik: u=rwx,g=rx,o=
# Numerik: 750
chmod 750 ~/uts_platform

# 4. Kunci file konfigurasi .env hanya untuk pemilik (r/w = 600)
chmod 600 ~/uts_platform/config/.env

# 5. Verifikasi hak akses dan identitas user
ls -ld ~/uts_platform
ls -l ~/uts_platform/config/.env
id dev_pedro

# 6. Tampilkan proses aktif
ps aux | head -n 15
```

### 3. Hasil Proses Nyata (PID) pada Mesin Anda
Pada pengujian langsung di sistem Anda, terdata proses aktif nyata sebagai berikut:
- **PID 22008**: `AAADSvc`
- **PID 32320**: `ACCUserPS`
- **PID 23192**: `AcerAgentService`
- **PID 22656**: `AcerCCAgent`
- **PID 22020**: `AcerCentralService`
- **PID 22392**: `AcerDIAgent`

---

## TAHAP 3 — VERSION CONTROL DENGAN GIT (15 POIN)

### 1. Tujuan
Inisialisasi repositori Git, membuat branching fitur, menggabungkan branch (merge), dan menghubungkan ke remote repository publik (GitHub/GitLab) tanpa membocorkan file rahasia `.env`.

### 2. Perintah Git Lengkap
```bash
cd ~/uts_platform

# Inisialisasi
git init
git branch -M main

# Commit pertama
git add README.md .gitignore
git commit -m "init: proyek UTS Teknologi Platform"

# Branch fitur/containerisasi
git checkout -b fitur/containerisasi
git add Dockerfile
git commit -m "feat: tambah Dockerfile"

# Branch fitur/compose & merge
git checkout -b fitur/compose
git merge fitur/containerisasi
git add docker-compose.yml app/ config/
git commit -m "feat: implementasi docker-compose dan aplikasi buku tamu"

# Hubungkan ke remote GitHub Anda
git remote add origin https://github.com/<USERNAME_ANDA>/uts_platform.git
git push -u origin --all
```

---

## TAHAP 4 — JARINGAN DASAR & HTTP (10 POIN)

### 1. Hasil Eksekusi Nyata pada Komputer Anda
- **Hostname**: `LAPTOP-2LEI8G06`
- **IP Address (Wi-Fi)**: `192.168.1.83` (Subnet: `255.255.255.0`, Gateway: `192.168.1.1`)
- **Hasil Ping ke 8.8.8.8**:
  - Sent: 4, Received: 4, Lost: 0 (0% loss)
  - Waktu respons: Min = 63ms, Max = 76ms, Rata-rata = 69ms.
- **Pengujian HTTP Server Python**: Berhasil berjalan di port 8080 (`http://localhost:8080`) mengembalikan status `HTTP/1.0 200 OK`.
- **Hasil Curl example.com**: Berhasil disimpan ke [contoh.html](file:///C:/Users/Pedro%20Saman/Tugas%20Kampus/uts_platform/logs/contoh.html).

### 2. Penjelasan Singkat (Maksimal 3 Baris Sesuai Soal)
> **Ping** menguji konektivitas dan latensi jaringan berbasis protokol ICMP tanpa transfer file.  
> **Curl** mentransfer data dari/ke server menggunakan protokol aplikasi (seperti HTTP/HTTPS).  
> **SSH** menyediakan saluran komunikasi teks terenkripsi yang aman untuk mengontrol mesin secara remote.

---

## TAHAP 5 & 6 — DOCKER IMAGE, CONTAINER & DOCKER COMPOSE (40 POIN)

### 1. Memulai Docker Desktop
Sebelum menjalankan perintah Docker, pastikan aplikasi **Docker Desktop** telah dibuka dari Start Menu Windows Anda hingga ikon paus di taskbar berstatus **Engine running**.

### 2. Eksekusi Tahap 5 (Single Container)
```bash
cd "C:\Users\Pedro Saman\Tugas Kampus\uts_platform"

# Build image
docker build -t bukutamu-pedro:v1 .

# Jalankan container mandiri dengan port 8000 dan volume log
docker run -d -p 8000:8000 -v "${PWD}/logs:/var/www/logs" --name bukutamu-app bukutamu-pedro:v1

# Periksa status & log
docker ps
docker logs bukutamu-app
```
Akses di browser: `http://localhost:8000`

### 3. Eksekusi Tahap 6 (Multi-Container dengan Compose)
Hentikan container mandiri sebelumnya, lalu jalankan orkestrasi:
```bash
docker stop bukutamu-app && docker rm bukutamu-app

# Jalankan seluruh service (Web + Database MySQL)
docker compose up -d

# Periksa status container & network
docker compose ps
docker network ls
docker volume ls
```

### 4. Pengujian Persistensi Data
1. Buka `http://localhost:8000`, kirim satu data pesan buku tamu (contoh: "Halo dari Petrus Admo Samon KW").
2. Matikan container: `docker compose down` (Catatan: jangan tambahkan flag `-v` agar named volume `db_data` tidak terhapus).
3. Nyalakan kembali: `docker compose up -d`.
4. Refresh browser di `http://localhost:8000`: data buku tamu **tetap tersimpan dan muncul**, membuktikan named volume `db_data` bekerja secara persisten!

---

## TAHAP 7 — LAPORAN, NASKAH VIDEO & PENGUMPULAN (10 POIN)

### 1. Naskah Singkat Video Presentasi (Durasi 2–3 Menit)
- **Pembuka**: "Halo, saya Petrus Admo Samon KW, NIM 25110027, dari prodi Sistem Informasi Universitas Uyelindo. Hari ini saya mempresentasikan hasil proyek UTS Teknologi Platform: Buku Tamu Digital."
- **Tahap 1–2**: "Proyek diawali dengan pembuatan struktur folder terstandar, pengaturan hak akses 750 untuk direktori dan 600 untuk berkas .env, serta pembuatan user dev_pedro."
- **Tahap 3–4**: "Kami mengelola version control dengan Git branch, menguji konektivitas jaringan IP 192.168.1.83, ping 8.8.8.8, dan server HTTP lokal di port 8080."
- **Tahap 5–6**: "Aplikasi dikontainerisasi menggunakan Dockerfile PHP 8.2 Apache port 8000, lalu diorkestrasikan bersama MySQL 8.0 menggunakan docker-compose. Multi-container terhubung melalui custom network bukutamu-net dan menggunakan named volume db_data yang terbukti persisten saat container direstart."
- **Penutup**: "Seluruh sistem berjalan optimal dan siap digunakan. Terima kasih."

### 2. Format Pengumpulan ZIP
Setelah semua screenshot dimasukkan ke folder `screenshots/` dan laporan dibuat, kemas folder proyek menjadi file:
`UTS_Platform_25110027_PetrusAdmoSamonKW.zip`
