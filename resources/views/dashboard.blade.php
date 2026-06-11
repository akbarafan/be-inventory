@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-sub'){{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}@endsection

@push('topbar-actions')
<a href="{{ route('barang.index') }}" class="btn btn-primary btn-sm">
  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
  Tambah Barang
</a>
@endpush

@section('content')
<div class="p-page">

  {{-- STAT CARDS --}}
  <div class="grid-4 mb-14 au au1">
    <div class="stat-card blue">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M11 4L6.5 2 2 4v4.5L6.5 11l4.5-2.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
        Total Barang
      </div>
      <div class="stat-value blue">{{ number_format($totalBarang) }}</div>
      <div class="stat-sub">Terdaftar di sistem</div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M3.5 4.5L2 3l1.5-1.5M2 3h8M9.5 8.5L11 10 9.5 11.5M11 10H3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Transaksi Hari Ini
      </div>
      <div class="stat-value green">{{ $transaksiHariIni }}</div>
      <div class="stat-sub">{{ $masukHariIni }} masuk &middot; {{ $keluarHariIni }} keluar</div>
    </div>
    <div class="stat-card amber">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.3"/><path d="M6.5 3.5v3M6.5 8.5v.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
        Stok Rendah
      </div>
      <div class="stat-value amber">{{ $stokRendah }}</div>
      <div class="stat-sub">Perlu restock segera</div>
    </div>
    <div class="stat-card red">
      <div class="stat-label">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.3"/><path d="M4.5 4.5l4 4M8.5 4.5l-4 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
        Rusak / Hilang
      </div>
      <div class="stat-value red">{{ $barangRusak }}</div>
      <div class="stat-sub">Butuh tindak lanjut</div>
    </div>
  </div>

  {{-- MAIN GRID --}}
  <div style="display:grid;grid-template-columns:1fr 300px;gap:12px;margin-bottom:12px" class="au au2">

    {{-- Tabel Barang Terbaru --}}
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Data Barang Terbaru</span>
        <a href="{{ route('barang.index') }}" class="btn btn-outline btn-sm">Lihat semua</a>
      </div>
      <div class="tbl-wrap">
        <table>
          <colgroup>
            <col style="width:90px">
            <col>
            <col style="width:100px">
            <col style="width:70px">
            <col style="width:50px">
          </colgroup>
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Barang</th>
              <th>Lokasi</th>
              <th>Kondisi</th>
              <th>Stok</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentBarang as $b)
            <tr>
              <td class="td-code">{{ $b->kode_barang }}</td>
              <td class="td-name">{{ $b->nama_barang }}</td>
              <td style="font-size:11px;color:var(--ink3)">
                @if($b->barangLokasi->isNotEmpty())
                  {{ $b->barangLokasi->first()->lokasi?->nama_lokasi ?? '-' }}
                  @if($b->barangLokasi->count() > 1)
                    <span style="color:var(--ink4)">+{{ $b->barangLokasi->count()-1 }} lagi</span>
                  @endif
                @else
                  -
                @endif
              </td>
              <td>
                <span class="badge badge-{{ $b->kondisi === 'baik' ? 'ok' : ($b->kondisi === 'rusak' ? 'warn' : 'bad') }}">
                  {{ ucfirst($b->kondisi) }}
                </span>
              </td>
              <td style="font-weight:500;color:{{ $b->jumlah == 0 ? 'var(--red)' : ($b->jumlah < 5 ? 'var(--amber)' : 'var(--ink)') }}">
                {{ $b->jumlah }}
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" style="text-align:center;color:var(--ink4);padding:24px">Belum ada data barang</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Transaksi Terbaru</span>
      </div>
      <div style="padding:4px 0">
        @forelse($recentTransaksi as $t)
        <div style="display:flex;align-items:center;gap:9px;padding:9px 14px;transition:background .1s"
          onmouseover="this.style.background='var(--paper)'"
          onmouseout="this.style.background='transparent'">
          <div style="width:30px;height:30px;border-radius:7px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
            background:{{ $t->jenis === 'masuk' ? 'var(--blue-pale)' : ($t->jenis === 'keluar' ? 'var(--red-pale)' : 'var(--amber-pale)') }};
            color:{{ $t->jenis === 'masuk' ? 'var(--blue)' : ($t->jenis === 'keluar' ? 'var(--red)' : 'var(--amber)') }}">
            @if($t->jenis === 'masuk')
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2.5 7.5l4 4 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @elseif($t->jenis === 'keluar')
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 11V2M2.5 5.5l4-4 4 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @else
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5h9M5 3.5L2 6.5l3 3M8 3.5l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @endif
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:11.5px;font-weight:500;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              {{ $t->barang?->nama_barang ?? '-' }}
            </div>
            <div style="font-size:10.5px;color:var(--ink4);margin-top:1px">
              {{ ucfirst($t->jenis) }} &middot; {{ \Carbon\Carbon::parse($t->tanggal)->format('H:i') }}
            </div>
          </div>
          <div style="font-size:12px;font-weight:600;flex-shrink:0;
            color:{{ $t->jenis === 'masuk' ? 'var(--blue)' : ($t->jenis === 'keluar' ? 'var(--red)' : 'var(--amber)') }}">
            {{ $t->jenis === 'masuk' ? '+' : ($t->jenis === 'keluar' ? '-' : '⇄') }}{{ $t->jumlah }}
          </div>
        </div>
        @empty
        <div style="padding:24px;text-align:center;color:var(--ink4);font-size:12.5px">
          Belum ada transaksi hari ini
        </div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- BOTTOM GRID --}}
  <div class="grid-2 au au3">

    {{-- Chart --}}
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Aktivitas 7 Hari Terakhir</span>
      </div>
      <div style="padding:14px 16px">
        @php $maxVal = max(array_merge($chartData, [1])); @endphp
        <div class="chart-bars" style="height:72px;margin-bottom:8px">
          @foreach($chartData as $i => $val)
          <div class="chart-bar-wrap">
            <div class="chart-bar {{ $i === 6 ? 'current' : '' }}"
              style="height:{{ $maxVal > 0 ? round(($val / $maxVal) * 100) : 5 }}%">
            </div>
            <div class="chart-bar-label">{{ $chartLabels[$i] }}</div>
          </div>
          @endforeach
        </div>
        <div style="display:flex;gap:16px">
          <span style="font-size:11px;color:var(--ink4)">Hari ini: <strong style="color:var(--ink)">{{ $transaksiHariIni }}</strong></span>
          <span style="font-size:11px;color:var(--ink4)">Total 7 hari: <strong style="color:var(--ink)">{{ array_sum($chartData) }}</strong></span>
        </div>
      </div>
    </div>

    {{-- Google Sheets Sync --}}
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Sinkronisasi Google Sheets</span>
        <button class="icbtn" title="Refresh">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M11 6.5A4.5 4.5 0 1 1 6.5 2M11 2v4.5H6.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
      <div style="padding:12px 14px">
        <div style="display:flex;align-items:center;gap:9px;background:var(--green-pale);border:1px solid var(--green-border);border-radius:7px;padding:8px 11px;margin-bottom:11px">
          <div class="sync-dot"></div>
          <span style="font-size:12px;color:var(--green);font-weight:500">Terhubung dan aktif</span>
          <span style="font-size:11px;color:var(--ink4);margin-left:auto">Otomatis</span>
        </div>
        <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:11px">
          @foreach(['Data Barang','Transaksi','Scan Log','Laporan'] as $s)
          <div style="display:flex;align-items:center;gap:4px;background:var(--paper2);border:1px solid var(--paper3);border-radius:5px;padding:3px 8px;font-size:11px;color:var(--ink2)">
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><rect x="1" y="1" width="9" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M3 4.5h5M3 6.5h5M3 8.5h3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
            {{ $s }}
          </div>
          @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:8px">
          <div class="progress" style="flex:1">
            <div class="progress-bar" style="width:100%"></div>
          </div>
          <span style="font-size:11px;color:var(--ink4);white-space:nowrap">{{ $totalBarang }} barang</span>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
