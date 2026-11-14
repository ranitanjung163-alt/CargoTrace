# LAPORAN AKHIR REMEDIASI TERARAH AUDIT AKHIR PPT DOSEN
## WORLDCARGO 360 — GLOBAL LOGISTICS & SHIPMENT TRACKING SYSTEM

**Nama Aplikasi:** WorldCargo 360  
**Tagline:** Track Beyond Borders.  
**Lokasi Direktori Utama:** `D:\WorldCargo_360`  
**Lokasi Direktori Mirror:** `D:\cargo_trace`  
**Tanggal & Waktu:** 14 November 2026, 20:15 WIB  
**Status Pengujian Otomatis:** **60 Passed (295 Assertions, 0 Failures)**  

---

## 1. DAFTAR MASALAH YANG DITEMUKAN

Berdasarkan audit akhir terhadap materi presentasi/PPT dosen dan source code aktual WorldCargo 360, ditemukan beberapa poin perbaikan konkret:

1. **Filter Shipment Belum Lengkap:**
   - Kontrol filter pada antarmuka web sebelumnya hanya mencakup `search`, `status`, dan `carrier_id`.
   - Filter `origin_location_id`, `destination_location_id`, rentang tanggal `start_date` & `end_date`, status keterlambatan `delayed`, serta kontrol sorting kolom dan arah asc/desc belum tersedia secara lengkap di UI maupun penanganan query controller.
2. **Default Tracking Map Otomatis Menampilkan Demo France:**
   - Halaman pelacakan rute sebelumnya menginisialisasi rute demo Normandy Perancis secara default atau saat shipment kosong, padahal data shipment aktual telah tersedia di database.
   - Label map engine pada antarmuka masih tertulis "Google Maps Engine", padahal aplikasi menggunakan Leaflet.js dengan ubin OpenStreetMap.
3. **Workflow Status Exception Belum Menerapkan Validasi Urutan Ketat:**
   - Alur status `OPEN -> INVESTIGATING -> RESOLVED` belum menerapkan validasi transisi langkah demi langkah, sehingga status `OPEN` dapat langsung diubah menjadi `RESOLVED`.
   - Exception berstatus `RESOLVED` belum diproteksi dari pembukaan kembali (*reopening*).
   - Validasi kronologi waktu `detected_at` pada exception belum memverifikasi bahwa timestamp exception tidak boleh mendahului event tracking terakhir pada shipment.
4. **Respon Weather API HTTP 200 dengan Payload Kosong / Invalid:**
   - Jika endpoint Open-Meteo mengembalikan respons HTTP 200 dengan JSON kosong (`{}`) atau tanpa key `current`, sistem sebelumnya mengasumsikan kode cuaca `0` dan menampilkannya sebagai cuaca cerah (*Clear Sky*).
   - Penanganan payload tidak valid seharusnya mengembalikan status `unavailable` (atau menyajikan *last-known observation* jika tersedia), tanpa mengarang data sintetis.
5. **Ketidaksesuaian Angka Pengujian & URL Dokumentasi:**
   - Badge pengujian pada `README.md` masih mencantumkan angka lama (25 tes).
   - Terdapat tautan file `LICENSE` yang belum memiliki file fisik.
   - Terdapat URL repositori GitHub dan domain deployment yang perlu diberi penanda jelas (*pending placeholder*).

---

## 2. DAFTAR FILE YANG DIUBAH

| No | File Path | Modifikasi Utama |
|---|---|---|
| 1 | `app/Http/Controllers/ShipmentController.php` | Menambahkan filter origin, destination, delayed, rentang tanggal, validasi sorting dinamis, dan passing master locations ke view. |
| 2 | `resources/views/shipments/index.blade.php` | Menambahkan kontrol UI lengkap untuk 9 kriteria filter (Search, Status, Carrier, Delay, Origin, Destination, Dates, Sorting). |
| 3 | `resources/views/tracking/index.blade.php` | Menampilkan shipment aktual database secara default, merubah label engine ke Leaflet / OpenStreetMap, dan membatasi demo France hanya saat dipilih eksplisit. |
| 4 | `app/Services/ExceptionService.php` | Menerapkan siklus status ketat `OPEN -> INVESTIGATING -> RESOLVED`, menolak pembukaan kembali status `RESOLVED`, dan memvalidasi kronologi timestamp exception. |
| 5 | `app/Services/WeatherService.php` | Menambahkan validasi integritas payload HTTP 200, menolak payload kosong/invalid agar tidak dianggap Clear Sky, serta mengaktifkan fallback last-known observation atau status unavailable. |
| 6 | `tests/Feature/CargoTraceCompleteTestSuiteTest.php` | Memperbarui test case lama dan menambahkan 7 automated test baru (Test 24 hingga Test 30) untuk seluruh fitur baru. |
| 7 | `README.md` | Memperbarui badge pengujian menjadi 60 Tests Passed, memperbarui bukti pengujian, memperbaiki tautan lisensi, dan memperjelas status repositori. |
| 8 | `docs/DEPLOYMENT_GUIDE.md` | Menandai placeholder URL domain produksi dan git clone remote repository secara transparan. |
| 9 | `docs/API_DOCUMENTATION.md` | Mendokumentasikan aturan transisi status exception REST API dan format respon kesalahan HTTP 422. |
| 10 | `LICENSE` | Menyediakan dokumen lisensi perangkat lunak akademik resmi untuk proyek UAS. |

---

## 3. PENJELASAN SETIAP PERBAIKAN

### A. Penyempurnaan Filter Shipment & Ekspor CSV
- **Controller (`ShipmentController.php`):**
  - Parameter `origin_id` dan `destination_id` difilter langsung pada kolom `origin_location_id` dan `destination_location_id`.
  - Parameter `delayed` mengevaluasi nilai `'1'`/`'true'` untuk paket terlambat dan `'0'`/`'false'` untuk paket tepat waktu.
  - Parameter `start_date` dan `end_date` membatasi rentang `created_at`.
  - Parameter sorting divalidasi terhadap daftar putih kolom (`shipment_number`, `created_at`, `expected_arrival`, `current_status`, `weight`) dengan arah `asc` atau `desc`.
  - Query ekspor CSV (`export`) memanggil method query terpadu yang sama (`buildShipmentsQuery`), sehingga file CSV yang diunduh secara presisi mengikuti seluruh filter yang sedang aktif di layar.
- **Antarmuka (`shipments/index.blade.php`):**
  - Ditata dalam grid dua baris yang rapi dan responsif.
  - Seluruh parameter filter dipertahankan saat navigasi halaman melalui pagination server-side (`withQueryString()`).

### B. Default Tracking Map Menggunakan Shipment Riil
- **Controller & View (`TrackingController.php` & `tracking/index.blade.php`):**
  - Saat pengguna membuka rute pelacakan tanpa query string, sistem secara otomatis mengambil shipment operasional dari database dan merender rute sebenarnya.
  - Opsi `DEMO-FR-NORMANDY` dipindahkan ke kelompok khusus (*optgroup*) "Academic & Presentation Demo" dan hanya aktif jika pengguna secara eksplisit memilihnya.
  - Label navigasi diperbarui dari "Google Maps Engine" menjadi **"Leaflet / OpenStreetMap"**.

### C. Alur Status Exception Ketat & Kronologi Event
- **Siklus Status:**
  - `OPEN -> INVESTIGATING`: Sah. Menghasilkan in-app notification ke customer dan mencatat audit log.
  - `OPEN -> RESOLVED`: Ditolak (*Invalid status transition*). Harus melalui investigasi terlebih dahulu.
  - `INVESTIGATING -> RESOLVED`: Sah. Mencatat catatan penyelesaian, operator penyelesai, event tracking resolusi, notifikasi, dan evaluasi ulang delay.
  - `INVESTIGATING -> OPEN`: Ditolak (*Cannot revert*).
  - `RESOLVED -> Any`: Ditolak (*Terminal state, cannot be reopened*).
- **Validasi Kronologi:**
  - Timestamp `detected_at` pada exception diverifikasi terhadap tracking event terakhir pada shipment. Jika lebih awal, sistem menolak pembuatan record dengan pesan validasi yang jelas.

### D. Validasi Payload Weather API
- `WeatherService.php` memverifikasi struktur array `current`, keberadaan `weather_code`, dan `temperature_2m`.
- Respons HTTP 200 yang kosong atau tidak lengkap tidak lagi diterjemahkan sebagai cuaca cerah (*Clear Sky*).
- Jika respons tidak valid atau terjadi timeout/500:
  1. Sistem memeriksa *last-known authentic observation* di cache. Jika ada, data disajikan dengan status `stale`.
  2. Jika tidak ada, sistem mengembalikan status eksplisit `unavailable` dengan nilai suhu `null` (tanpa membuat angka sintetis).

---

## 4. HASIL SELURUH AUTOMATED TESTS

Pengujian otomatis dijalankan pada database isolasi SQLite in-memory (`:memory:`):

```text
   PASS  Tests\Unit\ExampleTest (1 test)
   PASS  Tests\Feature\ExampleTest (1 test)
   PASS  Tests\Feature\CargoTraceCompleteTestSuiteTest (30 tests)
   PASS  Tests\Feature\DatabaseSeederRobustnessAndChronologyTest (28 tests)

  Tests:    60 passed (295 assertions)
  Duration: ~7.87s
```

### Rincian 7 Pengujian Otomatis Baru (Tests 24–30):
- **Test 24:** Pengujian komprehensif filter shipment (origin, destination, date range, delayed status, sorting).
- **Test 25:** Isolasi data customer pada antarmuka web dan file export CSV.
- **Test 26:** Verifikasi default tracking map menggunakan shipment database aktual dan bukan rute demo.
- **Test 27:** Penegakan urutan status exception (`OPEN -> INVESTIGATING -> RESOLVED`) dan penolakan pembukaan kembali status `RESOLVED`.
- **Test 28:** Penegakan kronologi bahwa exception tidak dapat memiliki timestamp lebih awal dari event tracking terakhir.
- **Test 29:** Pengujian validasi respons Open-Meteo: penolakan HTTP 200 payload kosong/parsial, penanganan HTTP 500, dan pemanfaatan cache *last-known stale observation*.
- **Test 30:** Otorisasi API dan penegakan aturan transisi status exception via endpoint REST API `/api/exceptions/{id}`.

---

## 5. BUKTI FILTER SHIPMENT

Filter shipment telah diuji dan terbukti bekerja penuh:
- Filter `origin_id` dan `destination_id` membatasi pengiriman berdasarkan checkpoint asal/tujuan.
- Filter `delayed=1` hanya menampilkan paket tertunda; `delayed=0` hanya menampilkan paket tepat waktu.
- Filter `start_date` dan `end_date` membatasi tanggal registrasi manifest.
- Sorting berdasarkan bobot (`weight`) dan nomor konsinyasi berjalan di tingkat server-side SQL.
- Hasil ekspor CSV (`/shipments/export`) mematuhi filter aktif yang sama.

---

## 6. BUKTI TRACKING MAP MENGGUNAKAN SHIPMENT AKTUAL

- Pada `tracking/index.blade.php`, variabel JavaScript `isDemoFrance` kini bernilai `true` **hanya jika** query string `shipment_number=DEMO-FR-NORMANDY` dikirim secara eksplisit.
- Saat pertama kali diakses, sistem memuat shipment aktual dari database, memetakan koordinat titik asal, event perjalanan transit, dan lokasi tujuan.
- Label header secara resmi menampilkan badge **"Leaflet / OpenStreetMap"**.

---

## 7. BUKTI VALIDASI EXCEPTION WORKFLOW

- Pengujian `test_27` dan `test_30` membuktikan bahwa percobaan melompati alur dari `OPEN` langsung ke `RESOLVED` diblokir dengan exception `InvalidArgumentException` pada web dan HTTP 422 pada REST API.
- Reopening exception yang sudah `RESOLVED` diblokir secara permanen.
- Pengujian `test_28` membuktikan pembuatan exception dengan waktu lampau mendahului event tracking terakhir ditolak secara otomatis.

---

## 8. BUKTI PENANGANAN WEATHER API

- Pengujian `test_29` membuktikan respons HTTP 200 dengan payload `{}` atau field tidak lengkap tidak lagi menghasilkan data "Clear Sky 0°C".
- Sistem mengembalikan status `unavailable` dengan nilai suhu `null`, atau menyajikan data pengamatan riil terakhir (*last-known*) berstatus `stale`.

---

## 9. STATUS DOKUMENTASI

- `README.md` telah diperbarui dengan badge `60 Passed (100%)` dan tabel ringkasan pengujian terbaru.
- Tautan lisensi telah diarahkan ke file fisik `LICENSE`.
- `docs/DEPLOYMENT_GUIDE.md` telah diperbarui dengan penandaan placeholder yang jujur untuk konfigurasi domain produksi dan remote repository.
- `docs/API_DOCUMENTATION.md` telah dilengkapi aturan alur transisi status exception dan format respon HTTP 422.

---

## 10. STATUS KEAMANAN DAN DATABASE UTAMA

- **TIDAK ADA Perintah Destruktif:** Tidak ada eksekusi `migrate:fresh`, `migrate:refresh`, `db:wipe`, `truncate`, atau `db:seed` terhadap database MySQL operasional (`cargo_trace`).
- **Verifikasi Jumlah Record Riil MySQL Live:**
  - **Shipments:** `10`
  - **Tracking Events:** `38`
  - **Shipment Exceptions:** `3`
  - **Users:** `7`
  - **Carriers:** `5`
  - **Locations:** `21`
  - **Audit Logs:** `41`
- Seluruh data operasional MySQL tetap utuh 100%.

---

## 11. DAFTAR PERSYARATAN YANG MASIH BELUM TERPENUHI

Semua item perbaikan konkret yang diminta dalam audit akhir PPT dosen telah diselesaikan secara tuntas:
- Filter shipment lengkap: **TERPENUHI (100%)**
- Default tracking map shipment riil: **TERPENUHI (100%)**
- Workflow status exception bertahap: **TERPENUHI (100%)**
- Kronologi event exception: **TERPENUHI (100%)**
- Validasi Weather API invalid payload: **TERPENUHI (100%)**
- Automated tests tanpa regresi: **TERPENUHI (100%)**

---

## 12. STATUS GITHUB DAN DEPLOYMENT SECARA JUJUR

- **Repositori Git:** Proyek saat ini dikembangkan dan diuji secara lokal pada folder kerja `D:\WorldCargo_360` dan mirror `D:\cargo_trace`. Belum ada remote URL GitHub publik resmi yang dikaitkan; dokumentasi telah menandai URL git clone sebagai `[Pending Repository Setup]`.
- **Hosting / Domain Server:** Proyek saat ini disiapkan untuk environment lokal (XAMPP / PHP built-in server pada `http://127.0.0.1:8000`). Konfigurasi domain dan sertifikat SSL pada panduan deployment telah diberi catatan status sebagai panduan implementasi produksi (*production-ready configuration*).
