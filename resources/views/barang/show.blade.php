@extends('layouts.app')
@section('title',$barang->nama_barang)
@section('page-title','Detail Barang')
@section('page-sub',$barang->kode_barang)
@section('content')
<div class="p-page">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px" class="au">
    <div class="card">
      <div class="card-head">
        <span class="card-head-title">Informasi Barang</span>
        <span class="badge badge-{{ $barang->kondisi==='baik'?'ok':($barang->kondisi==='rusak'?'warn':'bad') }}">{{ ucfirst($barang->kondisi) }}</span>
        <a href="{{ route('barang.qr',$barang->kode_barang) }}" class="btn btn-outline btn-sm">Download QR</a>
      </div>
      <div style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:14px">
        @foreach([['Kode Barang',$barang->kode_barang],['Nama Barang',$barang->nama_barang],['Kategori',$barang->kategori?->nama_kategori??'-'],['Kondisi',ucfirst($barang->kondisi)],['Total Stok',$barang->jumlah.' unit'],['Sumber',ucfirst(str_replace('_',' ',$barang->sumber??''))],['Tanggal Masuk',$barang->tanggal_masuk?->format('d M Y')??'-']] as [$k,$v])
        <div>
          <div style="font-size:10px;color:var(--ink4);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px">{{ $k }}</div>
          <div style="font-size:13.5px;color:var(--ink);font-weight:500">{{ $v }}</div>
        </div>
        @endforeach
        @if($barang->deskripsi)<div style="grid-column:1/-1"><div style="font-size:10px;color:var(--ink4);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px">Deskripsi</div><div style="font-size:13px;color:var(--ink2)">{{ $barang->deskripsi }}</div></div>@endif
        <div style="grid-column:1/-1">
          <div style="font-size:10px;color:var(--ink4);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Stok Per Lokasi</div>
          @forelse($barang->barangLokasi as $bl)
          <div style="display:flex;justify-content:space-between;padding:6px 10px;background:var(--paper2);border-radius:6px;margin-bottom:4px;font-size:13px">
            <span>{{ $bl->lokasi?->nama_lokasi ?? '-' }}</span>
            <span style="font-weight:600">{{ $bl->jumlah }} unit</span>
          </div>
          @empty
          <div style="font-size:12px;color:var(--ink4)">Belum ada stok tercatat</div>
          @endforelse
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-head"><span class="card-head-title">Riwayat Transaksi</span></div>
      <div class="tbl-wrap">
        <table>
          <thead><tr><th>Jenis</th><th width="50">Jml</th><th>Petugas</th><th>Waktu</th></tr></thead>
          <tbody>
            @forelse($barang->transaksi->take(10) as $t)
            <tr>
              <td><span class="badge badge-{{ $t->jenis }}">{{ ucfirst($t->jenis) }}</span></td>
              <td style="font-weight:500">{{ $t->jumlah }}</td>
              <td style="font-size:12.5px">{{ $t->user?->name??'System' }}</td>
              <td style="font-size:11.5px;color:var(--ink3)">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--ink4)">Belum ada transaksi</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div style="margin-top:14px;display:flex;gap:8px">
    <a href="{{ route('barang.index') }}" class="btn btn-outline"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 7h8M5 4L2 7l3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>Kembali</a>
  </div>
</div>
@endsection
