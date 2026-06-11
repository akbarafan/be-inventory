@extends('layouts.app')
@section('title','Transaksi')
@section('page-title','Transaksi Barang')
@section('page-sub','Catat aktivitas masuk, keluar, dan perpindahan barang')
@push('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="document.getElementById('trxModal').style.display='flex'">
  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
  Catat Transaksi
</button>
@endpush
@section('content')
<div class="p-page">
  <div class="grid-3 mb-14 au au1">
    <div class="stat-card blue" style="display:flex;align-items:center;gap:12px">
      <div style="width:38px;height:38px;border-radius:9px;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;color:var(--blue);flex-shrink:0">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 14V2M3 9l5 5 5-5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div><div style="font-size:22px;font-weight:600;color:var(--ink)">{{ $masuk }}</div><div style="font-size:11px;color:var(--ink4);margin-top:2px">Masuk Hari Ini</div></div>
    </div>
    <div class="stat-card red" style="display:flex;align-items:center;gap:12px">
      <div style="width:38px;height:38px;border-radius:9px;background:var(--red-pale);display:flex;align-items:center;justify-content:center;color:var(--red);flex-shrink:0">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 2v12M3 7l5-5 5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div><div style="font-size:22px;font-weight:600;color:var(--ink)">{{ $keluar }}</div><div style="font-size:11px;color:var(--ink4);margin-top:2px">Keluar Hari Ini</div></div>
    </div>
    <div class="stat-card amber" style="display:flex;align-items:center;gap:12px">
      <div style="width:38px;height:38px;border-radius:9px;background:var(--amber-pale);display:flex;align-items:center;justify-content:center;color:var(--amber);flex-shrink:0">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 8h12M6 4L2 8l4 4M10 4l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <div><div style="font-size:22px;font-weight:600;color:var(--ink)">{{ $pindah }}</div><div style="font-size:11px;color:var(--ink4);margin-top:2px">Perpindahan Hari Ini</div></div>
    </div>
  </div>

  <div class="card au au2">
    <div class="card-head">
      <span class="card-head-title">Riwayat Transaksi</span>
      <form method="GET" style="display:flex;gap:8px;margin-left:auto">
        <select name="jenis" class="form-control" style="width:auto;padding:5px 28px 5px 10px;font-size:12px" onchange="this.form.submit()">
          <option value="">Semua Jenis</option>
          <option value="masuk" {{ request('jenis')==='masuk'?'selected':'' }}>Masuk</option>
          <option value="keluar" {{ request('jenis')==='keluar'?'selected':'' }}>Keluar</option>
          <option value="pindah" {{ request('jenis')==='pindah'?'selected':'' }}>Pindah</option>
        </select>
        <div class="search-bar"><svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="4" stroke="currentColor" stroke-width="1.3"/><path d="M9 9l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg><input name="search" value="{{ request('search') }}" placeholder="Cari barang..."></div>
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
      </form>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th width="90">Kode</th><th>Nama Barang</th><th width="100">Jenis</th><th width="50">Jml</th><th width="110">Lokasi</th><th width="90">Petugas</th><th width="150">Waktu</th><th width="60">Aksi</th></tr></thead>
        <tbody>
          @forelse($transaksi as $t)
          <tr>
            <td class="td-code">{{ $t->barang?->kode_barang }}</td>
            <td class="td-name">{{ $t->barang?->nama_barang }}</td>
            <td><span class="badge badge-{{ $t->jenis }}">{{ ucfirst($t->jenis) }}</span></td>
            <td style="font-weight:500">{{ $t->jumlah }}</td>
            <td style="font-size:11px;color:var(--ink3)">
              @if($t->jenis === 'pindah' && $t->lokasiAsal && $t->lokasiTujuan)
                {{ $t->lokasiAsal->nama_lokasi }} &rarr; {{ $t->lokasiTujuan->nama_lokasi }}
              @elseif($t->jenis === 'masuk' && $t->lokasiTujuan)
                &rarr; {{ $t->lokasiTujuan->nama_lokasi }}
              @elseif($t->jenis === 'keluar' && $t->lokasiAsal)
                {{ $t->lokasiAsal->nama_lokasi }} &rarr;
              @else
                &mdash;
              @endif
            </td>
            <td style="font-size:12px">{{ $t->user?->name ?? 'System' }}</td>
            <td style="font-size:11.5px;color:var(--ink3)">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y H:i') }}</td>
            <td>
              <form action="{{ route('transaksi.destroy',$t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="icbtn del"><svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 3h9M4 3V2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1M5 5.5v4M7 5.5v4M2.5 3l.6 6.5a1 1 0 0 0 1 .9h3.8a1 1 0 0 0 1-.9L9.5 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--ink4)">Belum ada transaksi</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($transaksi->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--paper3)">{{ $transaksi->links() }}</div>
    @endif
  </div>
</div>

{{-- MODAL TRANSAKSI --}}
<div id="trxModal" class="modal-overlay" style="display:none">
  <div class="modal-box" style="width:480px">
    <div class="modal-head">
      <h3 class="modal-title">Catat Transaksi Baru</h3>
      <button class="modal-close" onclick="document.getElementById('trxModal').style.display='none'">&times;</button>
    </div>
    <form method="POST" action="{{ route('transaksi.store') }}">
      @csrf
      <div style="display:flex;flex-direction:column;gap:16px">
        <div class="form-group">
          <label class="form-label">Jenis Transaksi</label>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:6px">
            @foreach(['masuk'=>['Barang Masuk','blue'],'keluar'=>['Barang Keluar','red'],'pindah'=>['Pindah Lokasi','amber']] as $jenis=>[$label,$color])
            <label style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:12px 8px;border-radius:9px;cursor:pointer;border:1px solid var(--paper3);transition:all .12s;background:#fff" class="jenis-card" data-color="{{ $color }}">
              <input type="radio" name="jenis" value="{{ $jenis }}" style="display:none" {{ $jenis==='masuk'?'checked':'' }}>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                @if($jenis==='masuk')<path d="M8 14V2M3 9l5 5 5-5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                @elseif($jenis==='keluar')<path d="M8 2v12M3 7l5-5 5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                @else<path d="M2 8h12M6 4L2 8l4 4M10 4l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>@endif
              </svg>
              <span style="font-size:11.5px;font-weight:500;text-align:center">{{ $label }}</span>
            </label>
            @endforeach
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Pilih Barang <span style="color:var(--red)">*</span></label>
          <select name="barang_id" class="form-control" required>
            <option value="">Pilih barang</option>
            @foreach($barang as $b)
            <option value="{{ $b->id }}">{{ $b->nama_barang }} ({{ $b->kode_barang }}) — total {{ $b->jumlah }} unit</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" id="lokasiAsalField" style="display:none">
          <label class="form-label">Lokasi Asal <span style="color:var(--red)">*</span></label>
          <select name="lokasi_asal_id" class="form-control">
            <option value="">Pilih lokasi</option>
            @foreach($lokasi as $l)<option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>@endforeach
          </select>
        </div>
        <div class="grid-2">
          <div class="form-group"><label class="form-label">Jumlah <span style="color:var(--red)">*</span></label><input type="number" name="jumlah" class="form-control" value="1" min="1" required></div>
          <div class="form-group" id="lokasiTujuanField" style="display:none">
            <label class="form-label">Lokasi Tujuan <span style="color:var(--red)">*</span></label>
            <select name="lokasi_tujuan_id" class="form-control">
              <option value="">Pilih lokasi</option>
              @foreach($lokasi as $l)<option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" placeholder="Catatan opsional..." style="min-height:70px"></textarea></div>
        <div style="display:flex;gap:9px;justify-content:flex-end">
          <button type="button" class="btn btn-outline" onclick="document.getElementById('trxModal').style.display='none'">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.jenis-card').forEach(function(card){
  card.addEventListener('click',function(){
    document.querySelectorAll('.jenis-card').forEach(function(c){
      c.style.borderColor='var(--paper3)';
      c.style.background='#fff';
    });
    var color=this.dataset.color;
    var paleBg={'blue':'var(--blue-pale)','red':'var(--red-pale)','amber':'var(--amber-pale)'};
    var borderClr={'blue':'var(--blue)','red':'var(--red)','amber':'var(--amber)'};
    this.style.borderColor=borderClr[color];
    this.style.background=paleBg[color];
    var jenis=this.querySelector('input').value;
    document.getElementById('lokasiAsalField').style.display=(jenis==='keluar'||jenis==='pindah')?'block':'none';
    document.getElementById('lokasiTujuanField').style.display=(jenis==='masuk'||jenis==='pindah')?'block':'none';
    var lbl=document.querySelector('#lokasiTujuanField .form-label');
    if(lbl)lbl.textContent=(jenis==='masuk'?'Lokasi':'Lokasi Tujuan')+' *';
  });
});
// init first
document.querySelector('.jenis-card').click();
document.getElementById('trxModal').addEventListener('click',function(e){if(e.target===this)this.style.display='none';});
</script>
@endpush
@endsection
