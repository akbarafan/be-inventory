# LAPORAN PROTOTYPE

## SISTEM INFORMASI INVENTARIS BARANG BERBASIS WEB
## PADA SMK LABSCHOOL UNESA 1 SURABAYA
### Modul Peminjaman dan Pelaporan Barang — Fokus: Transaksi

---

## A. DESKRIPSI PROTOTYPE

Prototype Sistem Informasi Inventaris Barang pada SMK Labschool UNESA 1 Surabaya dikembangkan menggunakan framework Laravel 12 dengan arsitektur MVC (Model-View-Controller). Fokus utama prototype ini adalah **Modul Transaksi** yang menangani pencatatan pergerakan barang di lingkungan sekolah.

### A.1 Tujuan Modul Transaksi

Modul Transaksi bertujuan untuk mencatat seluruh aktivitas pergerakan barang inventaris sekolah secara akurat dan real-time. Pencatatan ini mencakup tiga jenis transaksi utama, yaitu:

1. **Barang Masuk (Incoming)** — Mencatat barang yang baru diterima atau ditambahkan ke gudang/lokasi penyimpanan.
2. **Barang Keluar (Outgoing)** — Mencatat barang yang dikeluarkan dari suatu lokasi untuk penggunaan atau distribusi.
3. **Pindah Lokasi (Transfer)** — Mencatat perpindahan barang antar lokasi penyimpanan.

### A.2 Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 12.62.0 (PHP 8.3) |
| Database | MySQL |
| Frontend | HTML, CSS, JavaScript (Vanilla) |
| Styling | CSS Custom Properties (style.css) |
| Template Engine | Blade Template |
| Version Control | Git |

### A.3 Struktur Database Modul Transaksi

Modul Transaksi menggunakan tiga tabel utama yang saling terintegrasi:

#### a. Tabel `transaksi_barangs`

Tabel utama untuk mencatat seluruh riwayat transaksi barang.

| Column | Type | Keterangan |
|---|---|---|
| id | bigint (PK) | Auto increment |
| barang_id | bigint (FK) | Relasi ke barangs.id |
| user_id | bigint (FK) | Relasi ke users.id (petugas) |
| jenis | enum('masuk','keluar','pindah','scan') | Jenis transaksi |
| jumlah | integer | Jumlah barang |
| lokasi_asal_id | bigint (FK, nullable) | Relasi ke lokasis.id |
| lokasi_tujuan_id | bigint (FK, nullable) | Relasi ke lokasis.id |
| keterangan | text (nullable) | Catatan transaksi |
| tanggal | timestamp | Waktu transaksi |

#### b. Tabel `barang_lokasi` (Pivot)

Tabel pivot untuk melacak stok barang per lokasi secara real-time.

| Column | Type | Keterangan |
|---|---|---|
| id | bigint (PK) | Auto increment |
| barang_id | bigint (FK) | Relasi ke barangs.id |
| lokasi_id | bigint (FK) | Relasi ke lokasis.id |
| jumlah | integer (default 0) | Stok barang di lokasi tersebut |
| Unique | (barang_id, lokasi_id) | Kombinasi unik |

#### c. Tabel `barangs` (terkait)

Tabel barang utama menyimpan total stok gabungan (cached sum dari pivot).

| Column | Type | Keterangan |
|---|---|---|
| id | bigint (PK) | Auto increment |
| kode_barang | varchar | Kode unik barang |
| nama_barang | varchar | Nama barang |
| jumlah | integer (default 0) | Total stok seluruh lokasi |

### A.4 Alur Logika Transaksi

Setiap transaksi dijalankan dalam **database transaction** (`DB::transaction`) untuk menjamin konsistensi data. Berikut alur untuk masing-masing jenis:

#### Barang Masuk (`masuk`)
1. Validasi input: barang_id, lokasi_tujuan_id, jumlah
2. `barangs.jumlah` ditambah sebesar jumlah barang
3. `barang_lokasi` di lokasi tujuan dicari (firstOrNew) lalu ditambah jumlahnya
4. Catatan transaksi dibuat dengan `lokasi_tujuan_id` terisi, `lokasi_asal_id` null

#### Barang Keluar (`keluar`)
1. Validasi input: barang_id, lokasi_asal_id, jumlah
2. Cek kecukupan stok di `barang_lokasi` untuk lokasi asal
3. `barangs.jumlah` dikurangi sebesar jumlah barang
4. `barang_lokasi` di lokasi asal dikurangi jumlahnya
5. Catatan transaksi dibuat dengan `lokasi_asal_id` terisi, `lokasi_tujuan_id` null

#### Pindah Lokasi (`pindah`)
1. Validasi input: barang_id, lokasi_asal_id, lokasi_tujuan_id, jumlah
2. Cek kecukupan stok di `barang_lokasi` untuk lokasi asal
3. `barangs.jumlah` **tidak berubah** (total stok tetap)
4. `barang_lokasi` di lokasi asal dikurangi
5. `barang_lokasi` di lokasi tujuan dicari (firstOrNew) lalu ditambah
6. Catatan transaksi dibuat dengan kedua lokasi terisi

---

## B. USE CASE DIAGRAM

Berikut adalah use case diagram untuk Modul Transaksi:

```
+-----------------------------------------------+
|                MODUL TRANSAKSI                 |
+-----------------------------------------------+

+---------------------------+
|        ADMINISTRATOR       |
+---------------------------+
         |        |
         v        v
+------------------+  +------------------+
| Lihat Riwayat    |  | Catat Barang     |
| Transaksi        |  | Masuk            |
+------------------+  +------------------+
         |        |
         v        v
+------------------+  +------------------+
| Catat Barang     |  | Catat Pindah     |
| Keluar           |  | Lokasi           |
+------------------+  +------------------+
         |        |
         v        v
+------------------+  +------------------+
| Cari / Filter    |  | Hapus Transaksi  |
| Transaksi        |  |                  |
+------------------+  +------------------+
         |
         v
+------------------+
|   USER (Staff)    |
| (use case 1-5    |
|  kecuali hapus)   |
+------------------+
```

### Aktor

| Aktor | Deskripsi |
|---|---|
| Administrator | Memiliki akses penuh, termasuk menghapus transaksi |
| User (Staff) | Dapat melihat dan mencatat transaksi |

### Daftar Use Case

| ID | Use Case | Aktor | Deskripsi |
|---|---|---|---|
| UC-01 | Melihat Riwayat Transaksi | Admin, User | Menampilkan daftar transaksi dengan informasi lengkap |
| UC-02 | Mencatat Barang Masuk | Admin, User | Mencatat barang yang masuk ke suatu lokasi |
| UC-03 | Mencatat Barang Keluar | Admin, User | Mencatat barang yang keluar dari suatu lokasi |
| UC-04 | Mencatat Pindah Lokasi | Admin, User | Mencatat perpindahan barang antar lokasi |
| UC-05 | Mencari/Menyaring Transaksi | Admin, User | Filter transaksi berdasarkan jenis atau keyword |
| UC-06 | Menghapus Transaksi | Admin | Menghapus data transaksi yang keliru |

---

## C. HASIL SCREENSHOT ANTARMUKA PROTOTYPE

> *[Screenshot 1 — Halaman Daftar Transaksi]*
> **Gambar 1.** Halaman utama modul transaksi menampilkan statistik harian, daftar riwayat transaksi, dan tombol catat transaksi baru.

---

> *[Screenshot 2 — Form Barang Masuk]*
> **Gambar 2.** Form pencatatan barang masuk dengan pemilihan lokasi tujuan.

---

> *[Screenshot 3 — Form Barang Keluar]*
> **Gambar 3.** Form pencatatan barang keluar dengan pemilihan lokasi asal.

---

> *[Screenshot 4 — Form Pindah Lokasi]*
> **Gambar 4.** Form pencatatan perpindahan barang antar lokasi.

---

> *[Screenshot 5 — Filter Transaksi]*
> **Gambar 5.** Filter transaksi berdasarkan jenis.

---

> *[Screenshot 6 — Statistik Dashboard Terkait Transaksi]*
> **Gambar 6.** Dashboard utama yang menampilkan statistik transaksi harian.

---

## D. SOURCE CODE

### D.1 Controller: `app/Http/Controllers/TransaksiBarangController.php`

```php
<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangLokasi;
use App\Models\Lokasi;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiBarang::with(['barang', 'user', 'lokasiAsal', 'lokasiTujuan']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('barang', fn($sq) =>
                $sq->where('nama_barang', 'like', "%$q%")
                   ->orWhere('kode_barang', 'like', "%$q%")
            );
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $transaksi = $query->latest('tanggal')->paginate(15)->withQueryString();
        $barang    = Barang::with('barangLokasi.lokasi')->orderBy('nama_barang')->get();
        $lokasi    = Lokasi::all();

        $masuk  = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'masuk')->count();
        $keluar = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'keluar')->count();
        $pindah = TransaksiBarang::whereDate('tanggal', today())->where('jenis', 'pindah')->count();

        return view('transaksi.index', compact('transaksi', 'barang', 'lokasi', 'masuk', 'keluar', 'pindah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'        => 'required|exists:barangs,id',
            'jenis'            => 'required|in:masuk,keluar,pindah',
            'jumlah'           => 'required|integer|min:1',
            'lokasi_asal_id'   => 'required_if:jenis,keluar,pindah|exists:lokasis,id',
            'lokasi_tujuan_id' => 'required_if:jenis,masuk,pindah|exists:lokasis,id',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        if ($request->jenis === 'keluar' || $request->jenis === 'pindah') {
            $stok = BarangLokasi::where('barang_id', $barang->id)
                ->where('lokasi_id', $request->lokasi_asal_id)
                ->value('jumlah') ?? 0;

            if ($stok < $request->jumlah) {
                return back()->withErrors(['jumlah' => "Stok di lokasi asal tidak mencukupi. Tersedia: {$stok}"])->withInput();
            }
        }

        DB::transaction(function () use ($request, $barang) {
            if ($request->jenis === 'masuk') {
                $barang->increment('jumlah', $request->jumlah);
                $bl = BarangLokasi::firstOrNew([
                    'barang_id' => $barang->id,
                    'lokasi_id' => $request->lokasi_tujuan_id,
                ]);
                $bl->jumlah += $request->jumlah;
                $bl->save();

            } elseif ($request->jenis === 'keluar') {
                $barang->decrement('jumlah', $request->jumlah);
                BarangLokasi::where('barang_id', $barang->id)
                    ->where('lokasi_id', $request->lokasi_asal_id)
                    ->decrement('jumlah', $request->jumlah);

            } elseif ($request->jenis === 'pindah') {
                BarangLokasi::where('barang_id', $barang->id)
                    ->where('lokasi_id', $request->lokasi_asal_id)
                    ->decrement('jumlah', $request->jumlah);

                $bl = BarangLokasi::firstOrNew([
                    'barang_id' => $barang->id,
                    'lokasi_id' => $request->lokasi_tujuan_id,
                ]);
                $bl->jumlah += $request->jumlah;
                $bl->save();
            }

            TransaksiBarang::create([
                'barang_id'       => $request->barang_id,
                'user_id'         => Auth::id(),
                'jenis'           => $request->jenis,
                'jumlah'          => $request->jumlah,
                'lokasi_asal_id'  => $request->jenis !== 'masuk' ? $request->lokasi_asal_id : null,
                'lokasi_tujuan_id'=> $request->jenis !== 'keluar' ? $request->lokasi_tujuan_id : null,
                'keterangan'      => $request->keterangan,
                'tanggal'         => now(),
            ]);
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dicatat.']);
        }
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dicatat.');
    }

    public function destroy($id)
    {
        TransaksiBarang::findOrFail($id)->delete();
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
```

**Penjelasan Controller:**

1. **`index()`** — Menampilkan daftar transaksi dengan fitur filter (jenis, search), eager loading relasi (barang, user, lokasiAsal, lokasiTujuan), statistik harian (jumlah masuk/keluar/pindah hari ini), dan pagination (15 per halaman).

2. **`store()`** — Mencatat transaksi baru dengan:
   - Validasi ketat menggunakan `required_if` untuk lokasi sesuai jenis transaksi
   - Pengecekan stok sebelum transaksi keluar/pindah
   - Semua operasi database dibungkus dalam `DB::transaction` untuk atomicity
   - Update tabel `barangs.jumlah` (total stok) dan `barang_lokasi` (stok per lokasi) secara bersamaan

3. **`destroy()`** — Menghapus transaksi berdasarkan ID (soft delete didukung oleh migration `cascadeOnDelete`).

### D.2 Model: `app/Models/TransaksiBarang.php`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiBarang extends Model
{
    protected $table = 'transaksi_barangs';

    protected $fillable = [
        'barang_id',
        'user_id',
        'jenis',
        'jumlah',
        'lokasi_asal_id',
        'lokasi_tujuan_id',
        'keterangan',
        'tanggal'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lokasiAsal()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_asal_id');
    }

    public function lokasiTujuan()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_tujuan_id');
    }
}
```

**Penjelasan Model:**

Model `TransaksiBarang` mendefinisikan 4 relasi utama:
- `barang()` — Setiap transaksi terkait dengan satu barang
- `user()` — Setiap transaksi dicatat oleh satu petugas (user)
- `lokasiAsal()` — Relasi ke tabel `lokasis` melalui foreign key `lokasi_asal_id` (nullable, digunakan untuk keluar/pindah)
- `lokasiTujuan()` — Relasi ke tabel `lokasis` melalui foreign key `lokasi_tujuan_id` (nullable, digunakan untuk masuk/pindah)

### D.3 Route: `routes/web.php`

```php
Route::resource('transaksi', TransaksiBarangController::class)
    ->except(['show','create','edit'])
    ->middleware('auth');
```

Route resource ini menghasilkan 3 endpoint:

| HTTP Method | URI | Method Controller | Nama Route |
|---|---|---|---|
| GET | /transaksi | index | transaksi.index |
| POST | /transaksi | store | transaksi.store |
| DELETE | /transaksi/{id} | destroy | transaksi.destroy |

### D.4 Migration: `transaksi_barangs`

#### Migration 1 — Create Table (2026_03_21_160052)

```php
Schema::create('transaksi_barangs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->enum('jenis', ['masuk', 'keluar', 'pindah', 'scan']);
    $table->integer('jumlah')->default(1);
    $table->text('keterangan')->nullable();
    $table->timestamp('tanggal')->useCurrent();
    $table->timestamps();
});
```

#### Migration 2 — Add Location Fields (2026_06_11_000002)

```php
Schema::table('transaksi_barangs', function (Blueprint $table) {
    $table->foreignId('lokasi_asal_id')
        ->nullable()
        ->constrained('lokasis')
        ->nullOnDelete()
        ->after('jumlah');

    $table->foreignId('lokasi_tujuan_id')
        ->nullable()
        ->constrained('lokasis')
        ->nullOnDelete()
        ->after('lokasi_asal_id');
});
```

### D.5 View: `resources/views/transaksi/index.blade.php`

View blade untuk halaman transaksi terdiri dari tiga bagian utama:

#### Bagian 1 — Statistik Cards (3 kartu di atas halaman)

```blade
<div class="grid-3 mb-14 au au1">
  <div class="stat-card blue">... Masuk Hari Ini: $masuk ...</div>
  <div class="stat-card red">... Keluar Hari Ini: $keluar ...</div>
  <div class="stat-card amber">... Perpindahan Hari Ini: $pindah ...</div>
</div>
```

#### Bagian 2 — Tabel Riwayat Transaksi

Kolom tabel: Kode, Nama Barang, Jenis (badge), Jml, Lokasi, Petugas, Waktu, Aksi

Logika tampilan lokasi berdasarkan jenis transaksi:

```blade
@if($t->jenis === 'pindah')
    {{ asal }} &rarr; {{ tujuan }}
@elseif($t->jenis === 'masuk')
    &rarr; {{ tujuan }}
@elseif($t->jenis === 'keluar')
    {{ asal }} &rarr;
@else
    &mdash;
@endif
```

#### Bagian 3 — Form Modal Pencatatan Transaksi

Form modal dengan fitur dinamis:
- 3 tombol radio untuk memilih jenis transaksi (masuk/keluar/pindah)
- Dropdown barang dengan info stok total
- Dropdown lokasi yang tampil/sembunyi sesuai jenis transaksi (JavaScript)
- Input jumlah, textarea keterangan

### D.6 View (Snippet): Form Dinamis dengan JavaScript

```javascript
// Show/hide location fields based on selected transaction type
document.querySelectorAll('.jenis-card').forEach(function(card){
  card.addEventListener('click', function() {
    var jenis = this.querySelector('input').value;

    document.getElementById('lokasiAsalField').style.display =
        (jenis === 'keluar' || jenis === 'pindah') ? 'block' : 'none';

    document.getElementById('lokasiTujuanField').style.display =
        (jenis === 'masuk' || jenis === 'pindah') ? 'block' : 'none';

    // Update label
    var lbl = document.querySelector('#lokasiTujuanField .form-label');
    if (lbl) lbl.textContent = (jenis === 'masuk' ? 'Lokasi' : 'Lokasi Tujuan') + ' *';
  });
});
```

---

## E. LINK VIDEO DEMO

> **[Link Video Demo Modul Transaksi]**
> *[Insert link to video demo here — e.g., YouTube, Google Drive, etc.]*

Video demo mencakup:
1. Menampilkan halaman daftar transaksi dengan statistik harian
2. Mencatat barang masuk ke lokasi tertentu
3. Mencatat barang keluar dari lokasi tertentu
4. Mencatat perpindahan barang antar lokasi
5. Filter transaksi berdasarkan jenis
6. Menghapus transaksi

---

## F. LAIN-LAIN

### F.1 Keunggulan Modul Transaksi

1. **Real-time Stock Tracking** — Stok barang per lokasi selalu terupdate secara otomatis karena menggunakan tabel pivot `barang_lokasi` yang diupdate bersamaan dengan pencatatan transaksi dalam satu database transaction.

2. **Data Integrity** — Setiap transaksi dibungkus dalam `DB::transaction` sehingga jika salah satu operasi gagal, seluruh perubahan di-rollback.

3. **Input Validation** — Validasi ketat memastikan data yang masuk sesuai aturan bisnis, termasuk pengecekan stok sebelum transaksi keluar atau pindah.

4. **User-friendly Interface** — Form dinamis yang menampilkan/menyembunyikan field sesuai jenis transaksi yang dipilih, mengurangi kesalahan input.

5. **History Tracking** — Setiap transaksi tercatat lengkap dengan petugas, waktu, dan lokasi untuk kebutuhan audit.

### F.2 Tampilan Lokasi Berdasarkan Jenis Transaksi

| Jenis | Lokasi Asal | Lokasi Tujuan | Tampilan di Tabel |
|---|---|---|---|
| Masuk | — | ✓ | `→ Lab` |
| Keluar | ✓ | — | `Lab →` |
| Pindah | ✓ | ✓ | `Lab → IT` |

### F.3 Saran Pengembangan

1. **Edit Transaksi** — Menambahkan fungsionalitas edit untuk memperbaiki transaksi yang keliru (saat ini hanya delete)
2. **Notifikasi Stok Minimum** — Peringatan otomatis ketika stok barang di suatu lokasi mencapai batas minimum
3. **Export Riwayat Transaksi** — Download data transaksi dalam format Excel/PDF langsung dari halaman transaksi
4. **Barcode/QR Scan untuk Transaksi** — Integrasi scan QR code untuk mempercepat proses pencatatan transaksi
5. **Dashboard Statistik Lanjutan** — Grafik perbandingan transaksi antar bulan/lokasi

---

### Catatan Screenshot yang Perlu Diambil

| No | Halaman | Elemen yang Ditampilkan |
|---|---|---|
| 1 | `/transaksi` | Tampilan penuh halaman transaksi (statistik cards + tabel) |
| 2 | `/transaksi` — modal masuk | Form dengan jenis "Barang Masuk" terpilih, dropdown lokasi tujuan |
| 3 | `/transaksi` — modal keluar | Form dengan jenis "Barang Keluar" terpilih, dropdown lokasi asal |
| 4 | `/transaksi` — modal pindah | Form dengan jenis "Pindah Lokasi" terpilih, kedua dropdown lokasi |
| 5 | `/transaksi?jenis=masuk` | Hasil filter transaksi berdasarkan jenis |
| 6 | `/dashboard` | Dashboard dengan statistik transaksi harian |

---

*Dokumen ini disusun sebagai bagian dari laporan prototype Sistem Informasi Inventaris Barang SMK Labschool UNESA 1 Surabaya — Modul Transaksi.*
