@extends('layouts.app')
@section('title','Detail Barang')
@section('page-title','Hasil Scan QR')
@section('page-sub','Detail barang dari hasil scan QR Code')
@section('content')
<div class="p-page">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px" class="au">
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Detail Barang</span>
        <span class="badge badge-ok">Terverifikasi</span>
        <a href="{{ route('barang.qr',$barang->kode_barang) }}" class="btn btn-outline btn-sm">Download QR</a>
      </div>
      <div style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:14px">
        @foreach([
          ['Kode Barang', $barang->kode_barang],
          ['Nama Barang', $barang->nama_barang],
          ['Kategori', $barang->kategori?->nama_kategori ?? '-'],
          ['Lokasi', $barang->barangLokasi->map(fn($bl) => $bl->lokasi?->nama_lokasi . ' (' . $bl->jumlah . ')')->implode(', ') ?: '-'],
          ['Kondisi', ucfirst($barang->kondisi)],
          ['Stok', $barang->jumlah.' unit'],
          ['Sumber', ucfirst(str_replace('_',' ',$barang->sumber))],
          ['Tanggal Masuk', $barang->tanggal_masuk?->format('d M Y') ?? '-'],
        ] as [$k,$v])
        <div>
          <div style="font-size:10px;color:var(--ink4);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px">{{ $k }}</div>
          <div style="font-size:13.5px;color:var(--ink);font-weight:500">{{ $v }}</div>
        </div>
        @endforeach
        @if($barang->deskripsi)
        <div style="grid-column:1/-1">
          <div style="font-size:10px;color:var(--ink4);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px">Deskripsi</div>
          <div style="font-size:13px;color:var(--ink2)">{{ $barang->deskripsi }}</div>
        </div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-head"><span class="card-head-title">Riwayat Scan Terakhir</span></div>
      <div class="tbl-wrap">
        <table>
          <thead><tr><th>Waktu</th><th>Petugas</th><th>IP</th></tr></thead>
          <tbody>
            @forelse($barang->scanLogs as $log)
            <tr>
              <td style="font-size:12px;color:var(--ink3)">{{ \Carbon\Carbon::parse($log->scanned_at)->format('d M Y H:i') }}</td>
              <td style="font-weight:500;font-size:12.5px">{{ $log->user?->name ?? 'Tamu' }}</td>
              <td style="font-size:11px;color:var(--ink4);font-family:monospace">{{ $log->ip_address }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink4)">Belum ada riwayat scan</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div style="margin-top:14px">
    <a href="{{ route('scan.camera') }}" class="btn btn-outline">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 7h8M5 4L2 7l3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Scan Barang Lain
    </a>
  </div>
</div>
@endsection
