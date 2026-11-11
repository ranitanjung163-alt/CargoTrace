# Panduan Presentasi & Tanya Jawab Penguasaan Kode (Code Ownership)
## Proyek UAS Pemrograman Web: CargoTrace – Global Shipment Visibility & Tracking Platform

Dokumentasi ini disusun khusus untuk membantu mahasiswa dalam memahami seluruh alur arsitektur, *business logic*, dan implementasi kode pada proyek individu **CargoTrace**, serta siap menghadapi sesi wawancara / tanya jawab (*code ownership interview*) di hadapan dosen penguji.

---

# BAGIAN 1: PEMAHAMAN 15 ALUR & SISTEM UTAMA

### 1. Cara Kerja Autentikasi (Authentication)
* **Web:** Menggunakan session-based authentication bawaan Laravel (`Auth::attempt`). Pengguna memasukkan email dan password di form login ([`resources/views/auth/login.blade.php`](file:///d:/cargo_trace/resources/views/auth/login.blade.php)). Controller [`AuthController.php`](file:///d:/cargo_trace/app/Http/Controllers/AuthController.php) memvalidasi kredensial menggunakan hash Bcrypt dan membuat session database yang aman dengan proteksi CSRF.
* **REST API:** Menggunakan **Laravel Sanctum**. Endpoint `/api/login` memvalidasi kredensial dan menerbitkan *Personal Access Token* (Bearer Token). Request berikutnya menyertakan header `Authorization: Bearer <token>`.

### 2. Perbedaan Hak Akses Role (Authorization & RBAC)
* **Administrator (`admin`):** Memiliki hak penuh atas tata kelola sistem. Mengelola master data user, carrier, location, seluruh shipment, melihat audit logs, serta ringkasan metrik global.
* **Logistics Operator (`operator`):** Bertugas dalam eksekusi operasional pengiriman harian. Dapat membuat shipment baru, memperbarui status pengiriman (*status transition*), menginput titik koordinat transit (*tracking events*), serta melaporkan dan menyelesaikan *exception*. Tidak memiliki izin mengelola akun user atau pengaturan keamanan.
* **Customer (`customer`):** Hanya dapat membaca (*read-only*) data shipment miliknya sendiri. Sistem menerapkan *Customer Data Isolation* pada query backend (`$query->where('customer_id', $user->id)`). Customer diblokir secara mutlak dari shipment milik pihak lain, halaman admin, maupun manipulasi tracking.

### 3. Struktur & Relasi Database (Eloquent Relationships)
Aplikasi memiliki 9 tabel utama:
1. `users` &rarr; HasMany `shipments` (sebagai customer), HasMany `audit_logs`.
2. `carriers` &rarr; HasMany `shipments`.
3. `locations` &rarr; BelongsTo Origin, Destination, dan Current Location pada `shipments`.
4. `shipments` &rarr; BelongsTo `customer` (`User`), BelongsTo `carrier`, BelongsTo `origin` (`Location`), BelongsTo `destination` (`Location`), HasMany `shipment_items`, HasMany `tracking_events`, HasMany `shipment_exceptions`.
5. `shipment_items` &rarr; BelongsTo `shipment` (detail muatan barang).
6. `tracking_events` &rarr; BelongsTo `shipment` (riwayat titik singgah secara kronologis).
7. `shipment_exceptions` &rarr; BelongsTo `shipment` (insiden kendala seperti `WEATHER_DELAY`).
8. `notifications` &rarr; BelongsTo `user` (pemberitahuan in-app).
9. `audit_logs` &rarr; BelongsTo `user` (rekam jejak aktivitas mutasi data).

### 4. Siklus Hidup Pengiriman (Shipment Lifecycle)
Pengiriman bergerak maju melalui lima fase deterministik:
$$\text{CREATED} \longrightarrow \text{BOOKED} \longrightarrow \text{PICKED\_UP} \longrightarrow \text{IN\_TRANSIT} \longrightarrow \text{DELIVERED}$$
* Diatur dalam [`ShipmentService.php`](file:///d:/cargo_trace/app/Services/ShipmentService.php) melalui metode `transitionStatus()`.
* **Aturan Kunci:** Status tidak boleh melompat (*illegal skip*) dan status `DELIVERED` adalah *terminal state* yang tidak boleh diubah kembali.

### 5. Cara Kerja TrackingService
* Terletak di [`TrackingService.php`](file:///d:/cargo_trace/app/Services/TrackingService.php).
* Bertanggung jawab mencatat event pergerakan cargo ke tabel `tracking_events`.
* **Validasi Kronologi:** Timestamp event baru tidak boleh lebih lampau daripada event terakhir.
* **Auto Sync:** Otomatis memperbarui `current_location_id` dan `current_status` pada shipment terkait secara atomik dalam database transaction.

### 6. Cara Menghitung Estimasi Waktu Tiba (ETA Calculation)
* Dijalankan oleh [`EtaService.php`](file:///d:/cargo_trace/app/Services/EtaService.php).
* Membandingkan jadwal kedatangan kontrak SLA (`expected_arrival`) dengan prediksi waktu tiba terkini (`estimated_arrival`).
* Rumus durasi keterlambatan:
  $$\text{Delay Duration} = \text{estimated\_arrival} - \text{expected\_arrival}$$
* Jika $\text{estimated\_arrival} \le \text{expected\_arrival}$, maka durasi keterlambatan adalah $0$ menit dan pengiriman dikategorikan tepat waktu.

### 7. Penentuan Delay dan Toleransi Threshold 12 Jam
* Nilai ambang batas (*threshold*) diambil secara dinamis dari `config('cargotrace.delay_threshold_hours', 12)`.
* **Logika SLA:**
  * Keterlambatan $\le 12$ jam: Dianggap masih dalam batas toleransi variansi operasional (*operational buffer*), sehingga flag `is_delayed = false`.
  * Keterlambatan $> 12$ jam: Memenuhi kriteria pelanggaran SLA (*SLA breach*), sehingga flag `is_delayed = true`.
* **Klasifikasi Keparahan (Severity):**
  * $0 < \text{jam} \le 6$: `LOW`
  * $6 < \text{jam} \le 24$: `MEDIUM`
  * $\text{jam} > 24$: `HIGH`

### 8. Cara Kerja ExceptionService
* Terletak di [`ExceptionService.php`](file:///d:/cargo_trace/app/Services/ExceptionService.php).
* Mendukung penanganan kendala operasional seperti `WEATHER_DELAY`, `CUSTOMS_HOLD`, dan `PORT_CONGESTION`.
* Siklus insiden: `OPEN` &rarr; `INVESTIGATING` &rarr; `RESOLVED`.
* Saat *exception* dibuka dengan tipe `WEATHER_DELAY`, sistem otomatis menandai pengiriman tertunda dan mengirim notifikasi in-app kepada customer.

### 9. Cara Kerja Peta Global (Global Map & Leaflet.js)
* Memanfaatkan pustaka **Leaflet.js** dan ubin peta **OpenStreetMap** (OSM).
* **Cakupan Seluruh Dunia (Worldwide Coverage):** Mendukung rentang penuh koordinat latitude $-90^\circ \text{ s/d } +90^\circ$ dan longitude $-180^\circ \text{ s/d } +180^\circ$, termasuk koordinat khatulistiwa dan meridian utama $(0, 0)$.
* **Penanganan Garis Tanggal Internasional (Antimeridian Crossing):** Rute trans-Pasifik (misalnya Tokyo $\to$ Los Angeles) dipotong secara cerdas di garis $180^\circ$ (`Math.abs(lng2 - lng1) > 180`) agar garis polyline tidak salah membentang memutari belahan bumi lain.
* **Keamanan XSS:** Semua data nama kota dan nomor shipment disanitasi menggunakan fungsi `escapeHtml()` sebelum dimasukkan ke dalam Leaflet popup.

### 10. Integrasi Geocoding API
* Terletak di [`GeocodingService.php`](file:///d:/cargo_trace/app/Services/GeocodingService.php).
* Terhubung ke OpenStreetMap **Nominatim API** secara live dengan *User-Agent* yang terstandarisasi.
* Hasil koordinat disimpan di cache sistem selama 30 hari untuk efisiensi dan kepatuhan terhadap *rate limit*.
* Apabila jaringan eksternal terputus atau timeout, service secara otomatis menggunakan *internal logistics fallback database* (mencakup pelabuhan utama dunia) sehingga sistem tidak pernah *crash*.

### 11. Integrasi Weather API (Open-Meteo)
* Terletak di [`WeatherService.php`](file:///d:/cargo_trace/app/Services/WeatherService.php).
* Mengambil data observasi cuaca riil secara global dari **Open-Meteo Forecasting API** berdasarkan koordinat GPS terkini shipment.
* Menyimpan riwayat pembacaan terakhir (*last-known real data*). Jika API eksternal offline, sistem secara transparan menampilkan status `stale` atau `Weather data unavailable`, tanpa memanipulasi angka suhu palsu.

### 12. Cara Kerja REST API & Sanctum
* Endpoint didefinisikan pada [`routes/api.php`](file:///d:/cargo_trace/routes/api.php).
* Seluruh endpoint operasional dilindungi oleh middleware `auth:sanctum`.
* Respon dikembalikan dalam format standar JSON:
  `{"success": true, "data": [...], "message": "...", "meta": {...}}`
* Dilengkapi Postman Collection v2.1 di [`docs/cargotrace_postman_collection.json`](file:///d:/cargo_trace/docs/cargotrace_postman_collection.json).

### 13. Cara Kerja Dashboard & Chart.js
* Controller [`DashboardController.php`](file:///d:/cargo_trace/app/Http/Controllers/DashboardController.php) mengumpulkan 5 kartu KPI langsung dari query database: Total, In Transit, Delivered, Delayed, Open Exceptions.
* **On-Time Delivery Rate:** Dihitung dengan rumus aktual:
  $$\text{On-Time Delivery Rate} = \left( \frac{\text{delivered tepat waktu}}{\text{total delivered}} \right) \times 100\%$$
* **Grafik 5 Status Lifecycle:** Bar chart menampilkan jumlah shipment aktual untuk 5 fase (`Created`, `Booked`, `Picked Up`, `In Transit`, `Delivered`).
* **Grafik Delay Rate Carrier:** Donut chart menampilkan persentase keterlambatan tiap vendor logistik dengan indikator tengah `Overall Delay Rate`.

### 14. Cara Kerja Authorization Backend
* Authorization diperiksa pada level Controller menggunakan Gate/Policy Laravel dan pemeriksaan peran pengguna (`$user->isAdmin()`, `$user->isOperator()`, `$user->isCustomer()`).
* Query database menerapkan *scoping*: jika user bertindak sebagai Customer, query otomatis menambahkan filter `where('customer_id', $user->id)`. Ini menjamin keamanan mutlak meskipun ada manipulasi ID pada URL browser.

### 15. Cara Menjalankan Automated Testing
* Proyek dilengkapi 25 skenario automated tests pada [`tests/Feature/CargoTraceCompleteTestSuiteTest.php`](file:///d:/cargo_trace/tests/Feature/CargoTraceCompleteTestSuiteTest.php).
* Menggunakan basis data SQLite in-memory yang terisolasi dari database utama development.
* Perintah untuk mengeksekusi pengujian:
  ```powershell
  php artisan test
  ```
  *Hasil: 25 Passed (94 assertions, 100% lulus).*

---

# BAGIAN 2: 20 DAFTAR PERTANYAAN DOSEN & JAWABAN LENGKAP

#### Q1: "Mengapa kamu menggunakan tema terang (Light Theme) pada dashboard?"
**Jawaban:**
> "Sesuai dengan ketentuan tugas dan gambar referensi dosen, tema terang dipilih karena merefleksikan standar antarmuka aplikasi logistik enterprise modern. Warna latar `#F5F8FC` dengan kartu putih `#FFFFFF` dan aksen biru `#3182CE` memberikan kontras tinggi, keterbacaan yang optimal bagi operator pada siang hari, serta visual yang bersih."

#### Q2: "Bagaimana cara sistem memisahkan data antarpengguna, khususnya customer?"
**Jawaban:**
> "Pemisahan data customer dilakukan pada level database query di backend, bukan hanya menyembunyikan tombol di frontend. Pada `ShipmentController` dan `DashboardController`, kami memeriksa apakah user adalah customer: jika ya, sistem otomatis menambahkan klausul `where('customer_id', Auth::id())`. Dengan demikian, customer tidak akan pernah bisa mengakses pengiriman milik customer lain."

#### Q3: "Bagaimana rumus perhitungan KPI On-Time Delivery Rate?"
**Jawaban:**
> "Rumus yang digunakan adalah:
> $$\text{On-Time Rate} = \frac{\text{Jumlah Shipment DELIVERED yang Tidak Terlambat}}{\text{Total Shipment Berstatus DELIVERED}} \times 100\%$$
> Shipment yang masih dalam perjalanan (*in transit*) sengaja tidak dimasukkan ke dalam penyebut agar persentase mencerminkan performa pengiriman yang sudah selesai secara objektif. Jika belum ada barang yang terkirim, sistem menampilkan `N/A` atau `0%` untuk mencegah pembagian dengan nol."

#### Q4: "Di mana konfigurasi ambang batas keterlambatan 12 jam disimpan dan bagaimana cara kerjanya?"
**Jawaban:**
> "Konfigurasi disimpan di `config/cargotrace.php` dan file `.env` melalui variabel `CARGOTRACE_DELAY_THRESHOLD_HOURS=12`. Pada `EtaService.php`, jika selisih waktu antara estimasi tiba dan jadwal tiba masih kurang dari atau sama dengan 12 jam, pengiriman dianggap masih berada dalam toleransi variansi jadwal (`is_delayed = false`). Begitu melebihi 12 jam, sistem menandainya sebagai pelanggaran SLA (`is_delayed = true`)."

#### Q5: "Apa bedanya status keterlambatan (delay status) dengan tingkat keparahan (delay severity)?"
**Jawaban:**
> "Delay status adalah nilai boolean (`true`/`false`) yang menyatakan apakah keterlambatan telah melanggar ambang batas SLA 12 jam. Sedangkan *severity* adalah klasifikasi dampak operasional berdasarkan durasi jam keterlambatan: `LOW` untuk 0–6 jam, `MEDIUM` untuk 6–24 jam, dan `HIGH` untuk di atas 24 jam."

#### Q6: "Mengapa pada grafik status pengiriman tidak ada kategori 'DELAYED'?"
**Jawaban:**
> "Karena `DELAYED` bukanlah status fase siklus hidup (*lifecycle state*), melainkan kondisi kepatuhan waktu (*SLA condition*). Siklus hidup shipment hanya terdiri dari 5 fase: `CREATED`, `BOOKED`, `PICKED_UP`, `IN_TRANSIT`, dan `DELIVERED`. Memasukkan 'DELAYED' ke dalam grafik status akan menyebabkan penghitungan ganda (*double-counting*) karena sebuah barang yang berstatus `IN_TRANSIT` bisa sekaligus berstatus `DELAYED`."

#### Q7: "Bagaimana cara sistem mencegah perubahan status yang tidak sah?"
**Jawaban:**
> "Pada `ShipmentService.php`, kami mendefinisikan array transisi legal: `CREATED` hanya bisa ke `BOOKED`, `BOOKED` ke `PICKED_UP`, `PICKED_UP` ke `IN_TRANSIT`, dan `IN_TRANSIT` ke `DELIVERED`. Jika ada request yang melompati fase atau mencoba mengubah status barang yang sudah `DELIVERED`, sistem akan melempar exception `InvalidStatusTransitionException` dan membatalkan transaksi."

#### Q8: "Bagaimana cara sistem menangani rute kapal/pesawat yang melewati Garis Tanggal Internasional (Antimeridian)?"
**Jawaban:**
> "Pada tampilan peta Leaflet.js, rute yang melintasi antimeridian (seperti rute Asia ke Amerika Utara) memiliki selisih bujur lebih dari $180^\circ$. Kami mengimplementasikan fungsi `drawRouteSegments()` yang mendeteksi kondisi `Math.abs(lng2 - lng1) > 180` dan memotong segmen garis polyline di garis tanggal, sehingga garis rute tidak salah melintang memutari seluruh peta dunia."

#### Q9: "Apakah koordinat $(0, 0)$ valid di dalam sistem ini?"
**Jawaban:**
> "Ya, koordinat $(0, 0)$ adalah titik valid di Teluk Guinea (pertemuan Khatulistiwa dan Meridian Utama). Kami tidak menggunakan pengecekan boolean JavaScript biasa seperti `if (lat && lng)` karena angka 0 bernilai falsy di JavaScript. Sebagai gantinya, kami menggunakan validasi numerik `isValidCoord()` dengan `Number.isFinite()` serta batas rentang $-90 \le \text{lat} \le 90$ dan $-180 \le \text{lng} \le 180$."

#### Q10: "Bagaimana cara aplikasi mencegah celah keamanan XSS pada popup peta Leaflet?"
**Jawaban:**
> "Popup Leaflet secara bawaan menerima string HTML. Jika data nama tempat atau input pengguna langsung diinterpolasi, script berbahaya bisa dieksekusi di browser. Kami menambahkan fungsi sanitasi `escapeHtml()` yang mengubah karakter khusus seperti `<`, `>`, `&`, dan `\"` menjadi entitas HTML aman sebelum dimasukkan ke dalam `bindPopup()`."

#### Q11: "Apa yang terjadi jika Open-Meteo Weather API sedang offline atau gangguan jaringan?"
**Jawaban:**
> "Pada `WeatherService.php`, request HTTP dibatasi timeout 4 detik dan dibungkus blok `try-catch`. Jika API gagal, sistem pertama-tama memeriksa apakah ada data pengamatan riil terakhir di cache. Jika tidak ada, sistem mengembalikan status eksplisit `unavailable` dengan nilai suhu `null` dan keterangan 'Weather data unavailable', bukan menampilkan angka suhu palsu seperti 22°C."

#### Q12: "Bagaimana cara kerja Geocoding menggunakan Nominatim?"
**Jawaban:**
> "Pada `GeocodingService.php`, sistem memanggil endpoint pencarian OpenStreetMap Nominatim dengan menyertakan header `User-Agent` resmi sesuai aturan OSM. Respon koordinat disimpan dalam cache selama 30 hari. Jika koneksi terputus, sistem memiliki *fallback database* yang berisi koordinat pelabuhan kargo utama di 5 benua."

#### Q13: "Bagaimana cara kamu mengamankan fitur Export CSV dari serangan CSV Injection (Formula Injection)?"
**Jawaban:**
> "Jika sebuah data shipment (misalnya nomor pengiriman atau nama customer) diawali karakter formula spreadsheet seperti `=`, `+`, `-`, atau `@`, aplikasi secara otomatis menyisipkan tanda petik tunggal (`'`) di depannya. Hal ini mencegah aplikasi spreadsheet seperti Excel mengeksekusi formula atau perintah berbahaya saat file CSV dibuka."

#### Q14: "Mengapa kamu menggunakan Database Transactions pada pembuatan shipment dan tracking?"
**Jawaban:**
> "Database Transaction (`DB::transaction`) menjamin prinsip atomisitas (*ACID*). Ketika shipment dibuat bersama item barangnya, atau ketika tracking event dibuat bersama pembaruan lokasi terkini, seluruh query harus berhasil bersamaan. Jika salah satu gagal, database akan di-rollback sehingga tidak terjadi inkonsistensi data."

#### Q15: "Apa fungsi tabel `audit_logs` dalam aplikasi ini?"
**Jawaban:**
> "Tabel `audit_logs` berfungsi sebagai catatan kepatuhan (*compliance trail*). Setiap aksi kritikal seperti pembuatan shipment, pergantian status, pencatatan exception, dan resolusi kendala dicatat beserta ID pengguna, aksi, nilai sebelum, nilai sesudah, alamat IP, dan waktu kejadian."

#### Q16: "Bagaimana autentikasi REST API bekerja pada aplikasi ini?"
**Jawaban:**
> "REST API menggunakan **Laravel Sanctum**. Klien melakukan POST ke `/api/login` dengan email dan password. Jika valid, controller menerbitkan token string unik. Klien wajib menyertakan token ini pada header `Authorization: Bearer <token>` di setiap request berikutnya. Middleware `auth:sanctum` memverifikasi token dan mengidentifikasi user terkait."

#### Q17: "Apakah data seeder aman jika dijalankan berulang kali?"
**Jawaban:**
> "Ya, seluruh pemanggilan data seeder di `DatabaseSeeder.php` menggunakan metode idempotent seperti `User::firstOrCreate()`, `Carrier::firstOrCreate()`, dan `Location::firstOrCreate()`. Hal ini menjamin bahwa menjalankan seeder berkali-kali tidak akan menggandakan data ataupun merusak data demo yang sudah ada."

#### Q18: "Berapa banyak automated test yang kamu buat dan apa saja yang diuji?"
**Jawaban:**
> "Terdapat 25 metode pengujian dengan total 94 assertions yang semuanya lulus 100%. Pengujian mencakup alur login, hak akses RBAC ketiga role, CRUD shipment, isolasi data customer, validasi siklus status, kronologi tracking, batas threshold 12 jam, severity delay, perhitungan on-time rate, validasi koordinat global, penanganan error API eksternal, hingga pencegahan formula injection pada export CSV."

#### Q19: "Bagaimana jika ada shipment yang mengalami kendala cuaca buruk di tengah laut?"
**Jawaban:**
> "Operator atau dispatcher dapat mencatat kejadian tersebut melalui menu *Exceptions* dengan memilih tipe `WEATHER_DELAY`. Sistem akan membuat insiden dengan status `OPEN`, mencatat tracking event gangguan, memperbarui status pengiriman menjadi delayed, serta mengirimkan notifikasi in-app kepada customer pemilik barang."

#### Q20: "Mengapa proyek ini siap untuk dideploy ke lingkungan produksi?"
**Jawaban:**
> "Proyek telah memenuhi standar deployment: environment variable rahasia dipisahkan ke `.env` (dengan `.env.example` yang bersih), seluruh aset dikompilasi, routing dan view dapat di-cache (`php artisan config:cache`, `route:cache`, `view:cache`), database memiliki indeks dan foreign key yang rapi, serta terdapat dokumentasi deployment lengkap di `docs/DEPLOYMENT_GUIDE.md`."
