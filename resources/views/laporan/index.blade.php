@extends('layouts.app')
@section('title','Laporan')
@section('page-title','Laporan dan Statistik')
@section('page-sub','Statistik dan rekap data inventaris sekolah')

@push('topbar-actions')
<div style="display:flex;gap:8px">
  <a href="{{ route('laporan.export.barang') }}" class="btn btn-outline btn-sm">
    <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 2h9v9H2zM4.5 5.5h4M4.5 7.5h4M4.5 3.5h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
    Export Barang
  </a>
  <button onclick="document.getElementById('exportModal').style.display='flex'" class="btn btn-primary btn-sm">
    <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 9V2M3.5 6l3 3 3-3M2 11h9" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Export Excel
  </button>
</div>
@endpush

@section('content')
<div class="p-page">

  {{-- Filter Periode --}}
  <form method="GET" action="{{ route('laporan.index') }}" class="au au1"
    style="display:flex;align-items:center;gap:10px;background:#fff;border:1px solid var(--paper3);border-radius:10px;padding:12px 16px;margin-bottom:16px">
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="color:var(--ink4);flex-shrink:0"><rect x="1.5" y="2" width="11" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M1.5 5h11M4.5 1v2M9.5 1v2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
    <span style="font-size:12.5px;font-weight:500;color:var(--ink2)">Periode</span>
    <input type="date" name="dari" value="{{ $dari }}" class="form-control" style="width:auto;padding:6px 10px;font-size:12.5px">
    <span style="font-size:12px;color:var(--ink4)">sampai</span>
    <input type="date" name="sampai" value="{{ $sampai }}" class="form-control" style="width:auto;padding:6px 10px;font-size:12.5px">
    <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
    <a href="{{ route('laporan.index') }}" class="btn btn-outline btn-sm">Reset</a>
    <span style="margin-left:auto;font-size:11.5px;color:var(--ink4)">
      Data dari {{ \Carbon\Carbon::parse($dari)->isoFormat('D MMM Y') }} hingga {{ \Carbon\Carbon::parse($sampai)->isoFormat('D MMM Y') }}
    </span>
  </form>

  {{-- STAT CARDS --}}
  <div class="grid-4 mb-14 au au1">
    <div class="stat-card blue">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M3.5 4.5L2 3l1.5-1.5M2 3h8M9.5 8.5L11 10 9.5 11.5M11 10H3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Total Transaksi
      </div>
      <div class="stat-value blue">{{ number_format($totalTransaksi) }}</div>
      <div class="stat-sub">Periode ini</div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2.5 7.5l4 4 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Barang Masuk
      </div>
      <div class="stat-value green">{{ number_format($totalMasuk) }}</div>
      <div class="stat-sub">Periode ini</div>
    </div>
    <div class="stat-card red">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 11V2M2.5 5.5l4-4 4 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Barang Keluar
      </div>
      <div class="stat-value red">{{ number_format($totalKeluar) }}</div>
      <div class="stat-sub">Periode ini</div>
    </div>
    <div class="stat-card amber">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><rect x="1" y="1" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.2"/><rect x="7.5" y="1" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.2"/><rect x="1" y="7.5" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.2"/><rect x="7.5" y="7.5" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.2"/></svg>
        Total Scan QR
      </div>
      <div class="stat-value amber">{{ number_format($totalScan) }}</div>
      <div class="stat-sub">Periode ini</div>
    </div>
  </div>

  {{-- CHARTS --}}
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-bottom:12px" class="au au2">
    {{-- Chart Bulanan --}}
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Transaksi per Bulan — {{ now()->year }}</span>
        <a href="{{ route('laporan.export.transaksi') }}" class="btn btn-outline btn-sm">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 8.5V2M3.5 6l2.5 2.5L8.5 6M2 10.5h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Export Transaksi
        </a>
      </div>
      <div style="padding:14px 16px">
        @php $maxChart = max(array_merge($chartData, [1])); @endphp
        <div class="chart-bars" style="height:90px;margin-bottom:10px">
          @foreach($chartData as $i => $val)
          <div class="chart-bar-wrap">
            <div class="chart-bar {{ $i === now()->month - 1 ? 'current' : '' }}"
              style="height:{{ $maxChart > 0 ? max(round(($val / $maxChart) * 100), 2) : 2 }}%;position:relative;"
              title="{{ $chartMonths[$i] }}: {{ $val }} transaksi">
            </div>
            <div class="chart-bar-label">{{ $chartMonths[$i] }}</div>
          </div>
          @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:16px;padding-top:8px;border-top:1px solid var(--paper3)">
          <div style="display:flex;align-items:center;gap:5px">
            <div style="width:10px;height:10px;border-radius:2px;background:var(--navy)"></div>
            <span style="font-size:11px;color:var(--ink3)">Bulan berjalan</span>
          </div>
          <div style="display:flex;align-items:center;gap:5px">
            <div style="width:10px;height:10px;border-radius:2px;background:var(--blue-pale)"></div>
            <span style="font-size:11px;color:var(--ink3)">Bulan lain</span>
          </div>
          <span style="margin-left:auto;font-size:11px;color:var(--ink4)">
            Total tahun ini: <strong style="color:var(--ink)">{{ array_sum($chartData) }}</strong> transaksi
          </span>
        </div>
      </div>
    </div>

    {{-- Kondisi Barang --}}
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Kondisi Barang</span>
        <a href="{{ route('laporan.export.barang') }}" class="btn btn-outline btn-sm">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 8.5V2M3.5 6l2.5 2.5L8.5 6M2 10.5h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Export
        </a>
      </div>
      <div style="padding:16px">
        @php $totalKondisi = max(array_sum($kondisiData), 1); @endphp
        @foreach(['baik' => ['Baik','#059669','#DCFCE7'],'rusak' => ['Rusak','#D97706','#FEF9C3'],'hilang' => ['Hilang','#DC2626','#FEE2E2']] as $k => [$label,$color,$bg])
        <div style="margin-bottom:16px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
            <div style="display:flex;align-items:center;gap:7px">
              <div style="width:8px;height:8px;border-radius:50%;background:{{ $color }}"></div>
              <span style="font-size:13px;font-weight:500;color:var(--ink)">{{ $label }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="font-size:13px;font-weight:600;color:{{ $color }}">{{ number_format($kondisiData[$k]) }}</span>
              <span style="font-size:11px;color:var(--ink4)">{{ round(($kondisiData[$k]/$totalKondisi)*100) }}%</span>
            </div>
          </div>
          <div style="height:7px;background:var(--paper3);border-radius:4px;overflow:hidden">
            <div style="width:{{ round(($kondisiData[$k]/$totalKondisi)*100) }}%;height:100%;background:{{ $color }};border-radius:4px;transition:width .6s ease"></div>
          </div>
        </div>
        @endforeach
        <div style="border-top:1px solid var(--paper3);padding-top:10px;margin-top:4px;display:flex;justify-content:space-between">
          <span style="font-size:12px;color:var(--ink3)">Total keseluruhan</span>
          <span style="font-size:13px;font-weight:600;color:var(--ink)">{{ number_format($totalKondisi) }} barang</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Google Sheets Sync --}}
  <div class="card au au3" style="margin-bottom:12px">
    <div class="card-head">
      <span class="card-head-title">Status Sinkronisasi Google Sheets</span>
      <button class="icbtn" title="Refresh">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M11 6.5A4.5 4.5 0 1 1 6.5 2M11 2v4.5H6.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>
    </div>
    <div style="padding:14px 16px;display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
      @foreach($syncStatus as $s)
      <div style="background:var(--paper2);border-radius:8px;padding:12px 13px">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:7px">
          <div class="sync-dot {{ $s['synced'] ? '' : 'offline' }}"></div>
          <span style="font-size:12px;font-weight:500;color:var(--ink)">{{ $s['name'] }}</span>
        </div>
        <div style="font-size:20px;font-weight:600;color:var(--navy);margin-bottom:3px">{{ number_format($s['rows']) }}</div>
        <div style="font-size:10.5px;color:var(--ink4)">baris &middot; {{ $s['time'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Tabel Transaksi Terbaru --}}
  <div class="card au au3">
    <div class="card-head">
      <span class="card-head-title">Transaksi Periode Ini</span>
      <span style="font-size:11.5px;color:var(--ink4)">{{ $recentTransaksi->count() }} data terakhir</span>
      <a href="{{ route('laporan.export.transaksi', ['dari'=>$dari,'sampai'=>$sampai]) }}"
        class="btn btn-outline btn-sm" style="margin-left:auto">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 8.5V2M3.5 6l2.5 2.5L8.5 6M2 10.5h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Export
      </a>
    </div>
    <div class="tbl-wrap">
      <table>
        <colgroup>
          <col style="width:90px"><col><col style="width:90px">
          <col style="width:55px"><col style="width:90px"><col style="width:150px">
        </colgroup>
        <thead>
          <tr>
            <th>Kode</th><th>Nama Barang</th><th>Jenis</th>
            <th>Jml</th><th>Petugas</th><th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentTransaksi as $t)
          <tr>
            <td class="td-code">{{ $t->barang?->kode_barang ?? '-' }}</td>
            <td class="td-name">{{ $t->barang?->nama_barang ?? '-' }}</td>
            <td><span class="badge badge-{{ $t->jenis }}">{{ ucfirst($t->jenis) }}</span></td>
            <td style="font-weight:500;text-align:center">{{ $t->jumlah }}</td>
            <td style="font-size:12px">{{ $t->user?->name ?? 'System' }}</td>
            <td style="font-size:11.5px;color:var(--ink3)">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--ink4)">Tidak ada transaksi pada periode ini</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- EXPORT MODAL --}}
<div id="exportModal" class="modal-overlay" style="display:none">
  <div class="modal-box" style="width:480px">
    <div class="modal-head">
      <h3 class="modal-title">Export Excel</h3>
      <button class="modal-close" onclick="document.getElementById('exportModal').style.display='none'">&times;</button>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px">

      {{-- Option 1: Laporan Lengkap --}}
      <div style="border:1px solid var(--paper3);border-radius:10px;padding:16px;background:var(--paper)">
        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:14px">
          <div style="width:38px;height:38px;border-radius:9px;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;color:var(--blue);flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="2" y="2" width="14" height="14" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M6 6h6M6 9h6M6 12h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
          </div>
          <div>
            <div style="font-size:13.5px;font-weight:600;color:var(--ink)">Laporan Lengkap</div>
            <div style="font-size:12px;color:var(--ink3);margin-top:2px">3 sheet: Ringkasan, Data Barang, dan Transaksi</div>
          </div>
        </div>
        <form action="{{ route('laporan.export') }}" method="GET" style="display:flex;gap:8px;align-items:center">
          <input type="date" name="dari" value="{{ $dari }}" class="form-control" style="flex:1;padding:7px 10px;font-size:12.5px">
          <span style="font-size:12px;color:var(--ink4);flex-shrink:0">s/d</span>
          <input type="date" name="sampai" value="{{ $sampai }}" class="form-control" style="flex:1;padding:7px 10px;font-size:12.5px">
          <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 8.5V2M3.5 6l2.5 2.5L8.5 6M2 10.5h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Download
          </button>
        </form>
      </div>

      {{-- Option 2: Data Barang --}}
      <div style="border:1px solid var(--paper3);border-radius:10px;padding:16px;background:var(--paper)">
        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:14px">
          <div style="width:38px;height:38px;border-radius:9px;background:var(--green-pale);display:flex;align-items:center;justify-content:center;color:var(--green);flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M15 6L9 3 3 6v6L9 15l6-3z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
          </div>
          <div>
            <div style="font-size:13.5px;font-weight:600;color:var(--ink)">Data Barang</div>
            <div style="font-size:12px;color:var(--ink3);margin-top:2px">Semua data barang beserta kondisi dan stok</div>
          </div>
        </div>
        <form action="{{ route('laporan.export.barang') }}" method="GET" style="display:flex;gap:8px;align-items:center">
          <select name="kondisi" class="form-control" style="flex:1;padding:7px 10px;font-size:12.5px">
            <option value="">Semua Kondisi</option>
            <option value="baik">Baik</option>
            <option value="rusak">Rusak</option>
            <option value="hilang">Hilang</option>
          </select>
          <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 8.5V2M3.5 6l2.5 2.5L8.5 6M2 10.5h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Download
          </button>
        </form>
      </div>

      {{-- Option 3: Transaksi --}}
      <div style="border:1px solid var(--paper3);border-radius:10px;padding:16px;background:var(--paper)">
        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:14px">
          <div style="width:38px;height:38px;border-radius:9px;background:var(--amber-pale);display:flex;align-items:center;justify-content:center;color:var(--amber);flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 7L3 5l2-2M3 5h12M13 11l2 2-2 2M15 13H3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div>
            <div style="font-size:13.5px;font-weight:600;color:var(--ink)">Data Transaksi</div>
            <div style="font-size:12px;color:var(--ink3);margin-top:2px">Riwayat transaksi berdasarkan periode</div>
          </div>
        </div>
        <form action="{{ route('laporan.export.transaksi') }}" method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <input type="date" name="dari" value="{{ $dari }}" class="form-control" style="flex:1;min-width:120px;padding:7px 10px;font-size:12.5px">
          <span style="font-size:12px;color:var(--ink4);flex-shrink:0">s/d</span>
          <input type="date" name="sampai" value="{{ $sampai }}" class="form-control" style="flex:1;min-width:120px;padding:7px 10px;font-size:12.5px">
          <select name="jenis" class="form-control" style="flex:1;min-width:100px;padding:7px 10px;font-size:12.5px">
            <option value="">Semua Jenis</option>
            <option value="masuk">Masuk</option>
            <option value="keluar">Keluar</option>
            <option value="pindah">Pindah</option>
          </select>
          <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 8.5V2M3.5 6l2.5 2.5L8.5 6M2 10.5h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Download
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('exportModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
</script>
@endpush

@endsection
