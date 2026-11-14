# WORLD CARGO 360 - LAPORAN KONSISTENSI PERSYARATAN & ARSIP SUBMISSION FINAL UAS

**Platform:** WorldCargo 360 (Track Beyond Borders)  
**Mata Kuliah:** Pemrograman Web 2 (UAS)  
**Dosen Pengampu:** Pak Ikhwan  
**Tanggal Evaluasi & Finalisasi:** 14 November 2026  
**Primary Workspace:** `D:\WorldCargo_360`  
**Mirror Workspace:** `D:\cargo_trace`  

---

## 1. PENDAHULUAN & TUJUAN AUDIT KONSISTENSI

Audit dan penyesuaian akhir ini dilakukan secara terarah untuk memastikan keselarasan penuh antara dokumen proyek, spesifikasi dosen pada slide PPT UAS, integritas suite pengujian otomatis, status lisensi, dan kesiapan berkas arsip submission final (`worldcargo360_uas_final_submission.zip`).

Prinsip kerja yang dipegang:
1. **Zero Destructive DB Operations:** Tidak ada perintah `migrate:fresh`, `migrate:refresh`, `db:wipe`, `truncate`, maupun `db:seed` yang dijalankan terhadap basis data MySQL aktif.
2. **Kesesuaian Spesifikasi:** Dokumentasi diselaraskan dengan persyaratan PHP 8.3+ dosen tanpa merusak kompatibilitas lingkungan lokal.
3. **Kejujuran & Transparansi Akademik:** Tidak mengklaim pengujian pada lingkungan yang belum tersedia, tidak memalsukan nama pemegang hak cipta atau lisensi, dan mencatat batasan teknis secara terbuka.
4. **Sinkronisasi 100%:** Kedua direktori kerja (`D:\WorldCargo_360` dan `D:\cargo_trace`) disinkronkan secara konsisten.

---

## 2. DETAIL TINDAKAN PERBAIKAN KONSISTENSI

### 2.1 Penyelarasan Persyaratan PHP Runtime (Target PPT Dosen vs Lingkungan Lokal)
- **Persyaratan Dosen:** PPT Dosen Pemrograman Web 2 mensyaratkan PHP 8.3+.
- **Pemeriksaan File Konfigurasi:**
  - `composer.json` mengonfigurasi `"php": "^8.2"`. Batasan semantic versioning ini mengizinkan PHP >= 8.2.0 hingga < 9.0.0, sehingga secara arsitektur mendukung penuh PHP 8.3 maupun PHP 8.4.
  - Sesuai instruksi khusus dosen/pengguna, batasan Composer **tidak dinaikkan menjadi `^8.3`** di mesin lokal ini karena CLI PHP yang terpasang di sistem operasi pengembang adalah PHP 8.2.12 (`C:\xampp\php\php.exe`). Menaikkan batasan Composer sebelum PHP 8.3 terinstal secara fisik akan memicu kegagalan dependency resolution dan test runner.
- **Pembaruan Dokumentasi:**
  - `README.md` dan `docs/DEPLOYMENT_GUIDE.md` telah diperbarui sehingga tidak lagi menyebut PHP 8.2 sebagai satu-satunya persyaratan minimum, melainkan menyatakan secara eksplisit:
    - **Target PHP Runtime (PPT Dosen):** PHP 8.3+
    - **Lingkungan Pengujian Aktual (Local):** PHP 8.2.12 CLI (XAMPP Windows 64-bit)
    - **Catatan Transparansi:** Source code dan dependensi Laravel 12.x siap dijalankan pada PHP 8.3+. Verifikasi runtime langsung pada PHP 8.3 direkomendasikan saat deployment ke server produksi atau penilaian dosen.
  - Badge pada `README.md` diperbarui menjadi `PHP 8.3+ Target | 8.2.12 Tested`.
  - Konfigurasi FastCGI Nginx pada `docs/DEPLOYMENT_GUIDE.md` mencantumkan socket `php8.3-fpm.sock` dengan catatan penyesuaian runtime.

### 2.2 Penyelarasan Angka Pengujian Otomatis (Test Count Consistency)
- Angka pengujian lama (seperti 20 atau 25 tes) pada seluruh dokumen telah disinkronkan dengan hasil pengujian aktual:
  - **Total Tests:** 60 tests passed
  - **Total Assertions:** 295 assertions
  - **Failures:** 0 failures
  - **Skipped:** 0 skipped
  - **Success Rate:** 100%
- File yang diperbarui:
  - `README.md`: Bagian badges, Tech Stack, dan Bab 7 (Automated Test Suite).
  - `docs/DEPLOYMENT_GUIDE.md`: Bab 5 (Pre-Presentation Verification Checklist).
  - `docs/INTERVIEW_GUIDE.md`: Bab 15 (Cara Menjalankan Automated Testing) dan Pertanyaan Wawancara Q18 (Berapa banyak automated test yang kamu buat).

### 2.3 Peninjauan & Pembaruan LICENSE
- **Temuan Sebelumnya:** File `LICENSE` sebelumnya mencantumkan nama pemegang hak cipta fiktif *"WorldCargo 360 Authors & Academic Contributors"* dan nama lisensi yang belum terdaftar secara legal.
- **Tindakan Perbaikan:**
  - Mengubah dokumen `LICENSE` menjadi **Academic Project Notice & Evaluation Terms**.
  - Menghilangkan entitas fiktif dan menyatakan dengan jujur bahwa proyek ini adalah karya tugas akhir semester (UAS) perseorangan mahasiswa untuk mata kuliah Pemrograman Web 2.
  - Memberikan hak izin evaluasi penuh kepada dosen pengampu, penguji, dan penilai akademik untuk menginspeksi, menjalankan, dan menguji source code.
  - Merinci atribusi upstream open-source legal pihak ketiga yang digunakan (Laravel: MIT, Leaflet: BSD-2-Clause, OpenStreetMap: ODbL, Chart.js: MIT, Tailwind CSS: MIT).
  - Memberikan rekomendasi transparan bahwa jika proyek ini di masa depan hendak dipublikasikan secara publik open-source, pemegang hak cipta riil harus didaftarkan dan lisensi standar (seperti MIT) dieksekusi secara resmi.
  - Badge di `README.md` disesuaikan menjadi `License - Academic Evaluation`.

### 2.4 Konsolidasi Postman Collection
- **Temuan:** Terdapat dua file identik di folder `docs/`:
  - `docs/cargotrace_postman_collection.json` (13.235 bytes)
  - `docs/worldcargo360_postman_collection.json` (13.235 bytes)
  Keduanya memiliki hash SHA-256 yang identik dan koleksi bernama *"WorldCargo 360 REST API - Complete UAS Collection"*.
- **Tindakan Perbaikan:**
  - Mempertahankan satu file kanonikal bernama `docs/worldcargo360_postman_collection.json`.
  - Menghapus file redundan `docs/cargotrace_postman_collection.json` dari kedua repositori.
  - Tidak mengubah isi request, endpoint, header, maupun payload API.
  - Memperbarui semua referensi tautan di `README.md` dan `docs/INTERVIEW_GUIDE.md` agar mengarah ke `docs/worldcargo360_postman_collection.json`.

---

## 3. HASIL PENGUJIAN OTOMATIS & VERIFIKASI RUNTIME

### 3.1 Status Lingkungan Eksekusi Pengujian
- **PHP CLI:** PHP 8.2.12 (cli) (built: Oct 24 2023 21:15:15) (ZTS Visual C++ 2019 x64)
- **Framework:** Laravel 12.12.2 (Framework 12.71.0)
- **Test Runner:** PHPUnit 11.5.50
- **Test Database:** SQLite `:memory:` (Terisolasi dari MySQL)

### 3.2 Breakdown Eksekusi `php artisan test`
- `Tests\Unit\ExampleTest`: 1 passed
- `Tests\Feature\ExampleTest`: 1 passed
- `Tests\Feature\CargoTraceCompleteTestSuiteTest`: 30 passed
- `Tests\Feature\DatabaseSeederRobustnessAndChronologyTest`: 28 passed
- **Total:** 60 passed, 295 assertions, 0 failed, 0 skipped.

---

## 4. KEAMANAN & INTEGRITAS BASIS DATA MYSQL

Sesuai aturan keamanan wajib, **tidak ada operasi destructive** yang dijalankan terhadap basis data MySQL aktif (`cargo_trace` pada `127.0.0.1:3306`). Seluruh tabel dan data operasional tetap utuh:

| Tabel | Jumlah Record Aktif | Keterangan Integritas |
| :--- | :---: | :--- |
| `shipments` | **10** | Utuh, tidak ada perubahan record atau status |
| `tracking_events` | **38** | Utuh, riwayat tracking operasional terjaga lengkap |
| `shipment_exceptions` | **3** | Utuh, status exception tidak tereset |
| `users` | **7** | Utuh, 7 akun operasional aktif: 1 Admin, 1 Operator, 5 Customer (100% konsisten) |
| `carriers` | **5** | Utuh, data master carrier logistik aktif |
| `locations` | **21** | Utuh, master lokasi pelabuhan dan hub kargo global |
| `audit_logs` | **41** | Utuh, append-only security logs terjaga 100% |

---

## 5. PEMBUATAN ARSIP SUBMISSION FINAL (`worldcargo360_uas_final_submission.zip`)

### 5.1 Standar Struktur & Portabilitas
Arsip ZIP dikemas ulang menggunakan path separator standar forward slash `/` (POSIX-compliant) sesuai standar spesifikasi PKWARE ZIP. Hal ini menjamin arsip dapat diekstrak dengan sempurna pada sistem operasi Windows (Explorer, 7-Zip, WinRAR) maupun Linux/macOS (`unzip`, `tar`, dsb.) tanpa menghasilkan nama file rusak bertanda backslash.

### 5.2 Kelengkapan Komponen yang Disertakan
- **Source Code Inti:** Seluruh controller, middleware, models, services, requests, dan helper pada direktori `app/`.
- **Konfigurasi & Bootstrap:** `bootstrap/app.php`, `bootstrap/providers.php`, seluruh file pada `config/`.
- **Database Assets:** Seluruh migrasi pada `database/migrations/`, seeder pada `database/seeders/`, dan factories.
- **Frontend & Routing:** Seluruh views Blade pada `resources/views/`, routing pada `routes/web.php` dan `routes/api.php`, public web assets (`public/`).
- **Dokumentasi Lengkap & API:**
  - `README.md`
  - `LICENSE` (Academic Project Evaluation Terms)
  - `docs/worldcargo360_postman_collection.json` (Postman Collection v2.1)
  - `docs/API_DOCUMENTATION.md`
  - `docs/DEPLOYMENT_GUIDE.md`
  - `docs/INTERVIEW_GUIDE.md`
  - `docs/ARCHITECTURE.md`
  - `docs/DATA_DICTIONARY.md`
  - `docs/ERD.md`
  - `FINAL_READ_ONLY_AUDIT_REPORT.md`
  - `FINAL_UAS_REMEDIATION_REPORT.md`
  - `FINAL_SUBMISSION_CONSISTENCY_REPORT.md`
- **File Build & Dependency Declarations:** `composer.json`, `composer.lock`, `package.json`, `vite.config.js`, `phpunit.xml`, `.env.example`, `.gitignore`, `.gitattributes`, `.editorconfig`, `artisan`.

### 5.3 Komponen yang Dikecualikan (Exclusions)
Arsip secara ketat mengecualikan:
- File kredensial lokal: `.env`
- Direktori version control lokal: `.git/`
- Dependensi biner besar yang dapat diunduh: `vendor/`, `node_modules/`
- Basis data lokal runtime: `database/database.sqlite`
- File cache pengujian dan logs: `.phpunit.result.cache`, `storage/logs/*.log`, `storage/framework/cache/data/*`, `storage/framework/sessions/*`, `storage/framework/views/*`.

---

## 6. SINKRONISASI DUA WORKSPACE & SHA-256 CHECKSUM

Semua berkas pada repositori primer (`D:\WorldCargo_360`) dan mirror (`D:\cargo_trace`) telah diverifikasi memiliki nilai SHA-256 yang identik (100% synchronized):

| Berkas | Status SHA-256 |
| :--- | :---: |
| `README.md` | **IDENTIK** |
| `docs/DEPLOYMENT_GUIDE.md` | **IDENTIK** |
| `docs/INTERVIEW_GUIDE.md` | **IDENTIK** |
| `LICENSE` | **IDENTIK** |
| `docs/worldcargo360_postman_collection.json` | **IDENTIK** |
| `FINAL_SUBMISSION_CONSISTENCY_REPORT.md` | **IDENTIK** |
| `worldcargo360_uas_final_submission.zip` | **IDENTIK** |

---

## 7. DAFTAR KETERBATASAN TEKNIS (KNOWN LIMITATIONS)

Secara transparan dicatat beberapa batasan yang berada di luar cakupan atau memerlukan lingkungan server eksternal:
1. **Verifikasi Lingkungan PHP 8.3:** Mesin lokal pengembang hanya memiliki runtime PHP 8.2.12. Meskipun kode Laravel 12 dan semua package kompatibel dengan PHP 8.3+, verifikasi runtime langsung pada PHP 8.3 belum dilakukan di mesin ini dan harus diuji saat deployment di VPS/server target.
2. **Hardware IoT GPS Terhubung:** Simulasi pergerakan kapal kargo menggunakan waypoints tracking kronologis berbasis koordinat pelabuhan dan waypoint laut; belum terhubung langsung ke feed satelit transponder AIS kapal maritim real-time.
3. **Model Prediksi ETA Lanjutan:** Kalkulasi SLA delay menggunakan threshold komparatif deterministik 12 jam, belum menggunakan model Machine Learning prediksi rute cuaca prediktif.

---

## 8. KESIMPULAN

Seluruh instruksi pemeriksaan dan perbaikan konsistensi final telah diselesaikan dengan akurat. Proyek **WorldCargo 360** telah memenuhi seluruh kriteria kelulusan, bebas dari klaim legalitas palsu, memiliki dokumentasi pengujian yang jujur dan konsisten (60 passed, 295 assertions), serta memiliki arsip submission bersih yang siap dikumpulkan.
