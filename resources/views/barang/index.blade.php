@extends('layouts.app')
@section('title','Data Barang')
@section('page-title','Data Barang')
@section('page-sub','Kelola data inventaris sekolah')
@push('topbar-actions')
<button class="btn btn-outline btn-sm" onclick="window.print()">
  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M3.5 4V2.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1V4M1.5 4h10a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-2v2h-6v-2h-2a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.3"/></svg>
  Export
</button>
<button class="btn btn-primary btn-sm" onclick="openModal()">
  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
  Tambah Barang
</button>
@endpush
@section('content')
<div class="p-page">
  <div class="card au">
    <div class="card-head">
      <div class="filter-tabs">
        @foreach(['semua'=>'Semua','baik'=>'Baik','rusak'=>'Rusak','hilang'=>'Hilang'] as $k=>$v)
        <button class="filter-tab {{ request('kondisi',$k==='semua'?'semua':'')===$k?'active':'' }}"
          onclick="window.location='{{ route('barang.index',array_merge(request()->query(),['kondisi'=>$k])) }}'">{{ $v }}</button>
        @endforeach
      </div>
      <div style="margin-left:auto;display:flex;gap:8px">
        <form method="GET" style="display:flex;gap:8px">
          @if(request('kondisi'))<input type="hidden" name="kondisi" value="{{ request('kondisi') }}">@endif
          <div class="search-bar">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.3"/><path d="M10 10l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            <input name="search" value="{{ request('search') }}" placeholder="Cari barang..." style="width:160px">
          </div>
          <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        </form>
      </div>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th width="95">Kode</th><th>Nama Barang</th><th width="90">Kategori</th><th width="100">Lokasi</th><th width="70">Kondisi</th><th width="50">Stok</th><th width="100">Aksi</th></tr></thead>
        <tbody>
          @forelse($barang as $b)
          <tr>
            <td class="td-code">{{ $b->kode_barang }}</td>
            <td class="td-name">{{ $b->nama_barang }}</td>
            <td style="font-size:12px">{{ $b->kategori?->nama_kategori ?? '-' }}</td>
            <td style="font-size:11px;line-height:1.5">
              @forelse($b->barangLokasi as $bl)
              <span>{{ $bl->lokasi?->nama_lokasi ?? '-' }}: <strong>{{ $bl->jumlah }}</strong>@if(!$loop->last), @endif</span>
              @empty
              <span style="color:var(--ink4)">-</span>
              @endforelse
            </td>
            <td><span class="badge badge-{{ $b->kondisi==='baik'?'ok':($b->kondisi==='rusak'?'warn':'bad') }}">{{ ucfirst($b->kondisi) }}</span></td>
            <td style="font-weight:500;color:{{ $b->jumlah==0?'var(--red)':($b->jumlah<5?'var(--amber)':'var(--ink)') }}">{{ $b->jumlah }}</td>
            <td>
              <div style="display:flex;gap:4px">
                <a href="{{ route('barang.qr',$b->kode_barang) }}" class="icbtn" title="Download QR">
                  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><rect x="1" y="1" width="4" height="4" rx="1" stroke="currentColor" stroke-width="1.2"/><rect x="8" y="1" width="4" height="4" rx="1" stroke="currentColor" stroke-width="1.2"/><rect x="1" y="8" width="4" height="4" rx="1" stroke="currentColor" stroke-width="1.2"/><rect x="2.2" y="2.2" width="1.6" height="1.6" fill="currentColor"/><rect x="9.2" y="2.2" width="1.6" height="1.6" fill="currentColor"/><rect x="2.2" y="9.2" width="1.6" height="1.6" fill="currentColor"/><rect x="8" y="8" width="1.6" height="1.6" fill="currentColor"/><rect x="11.2" y="8" width="1.6" height="1.6" fill="currentColor"/><rect x="8" y="11.2" width="1.6" height="1.6" fill="currentColor"/><rect x="9.6" y="9.6" width="1.6" height="1.6" fill="currentColor"/></svg>
                </a>
                <button class="icbtn" title="Edit" onclick="editBarang({{ $b->id }},'{{ $b->kode_barang }}','{{ addslashes($b->nama_barang) }}','{{ $b->kategori_id }}','{{ $b->sumber }}','{{ $b->kondisi }}','{{ $b->jumlah }}','{{ $b->tanggal_masuk?->format('Y-m-d') }}','{{ addslashes($b->deskripsi ?? '') }}')">
                  <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M8.5 1.5l2 2L4 10H2V8L8.5 1.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                </button>
                <form action="{{ route('barang.destroy',$b->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="icbtn del" title="Hapus">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 3h9M4 3V2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1M5 5.5v4M7 5.5v4M2.5 3l.6 6.5a1 1 0 0 0 1 .9h3.8a1 1 0 0 0 1-.9L9.5 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--ink4)">Belum ada data barang</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($barang->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--paper3)">
      {{ $barang->links('vendor.pagination.simple-tailwind') }}
    </div>
    @endif
  </div>
</div>

{{-- MODAL --}}
<div id="modalOverlay" class="modal-overlay" style="display:none">
  <div class="modal-box">
    <div class="modal-head">
      <h3 class="modal-title" id="modalTitle">Tambah Barang Baru</h3>
      <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <form id="barangForm" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="barang_id" id="barangId">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div class="grid-2">
          <div class="form-group"><label class="form-label">Kode Barang <span style="color:var(--red)">*</span></label><input type="text" name="kode_barang" id="f_kode" class="form-control" placeholder="Contoh: BRG-0001" required></div>
          <div class="form-group"><label class="form-label">Nama Barang <span style="color:var(--red)">*</span></label><input type="text" name="nama_barang" id="f_nama" class="form-control" placeholder="Nama lengkap barang" required></div>
        </div>
        <div class="form-group"><label class="form-label">Kategori <span style="color:var(--red)">*</span></label>
          <select name="kategori_id" id="f_kat" class="form-control" required>
            <option value="">Pilih kategori</option>
            @foreach($kategori as $k)<option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>@endforeach
          </select>
        </div>
        <div id="lokasiAwalGroup">
          <div class="grid-2">
            <div class="form-group"><label class="form-label">Lokasi Awal <span style="color:var(--red)">*</span></label>
              <select name="lokasi_awal_id" id="f_lok_awal" class="form-control">
                <option value="">Pilih lokasi</option>
                @foreach($lokasi as $l)<option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>@endforeach
              </select>
            </div>
            <div class="form-group"><label class="form-label">Jumlah Awal <span style="color:var(--red)">*</span></label>
              <input type="number" name="jumlah_awal" id="f_jml_awal" class="form-control" value="1" min="1">
            </div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
          <div class="form-group"><label class="form-label">Kondisi</label>
            <select name="kondisi" id="f_kondisi" class="form-control">
              <option value="baik">Baik</option><option value="rusak">Rusak</option><option value="hilang">Hilang</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Jumlah (total)</label><input type="number" name="jumlah" id="f_jml" class="form-control" value="1" min="0"></div>
          <div class="form-group"><label class="form-label">Sumber</label>
            <select name="sumber" id="f_sumber" class="form-control">
              <option value="pembelian">Pembelian</option><option value="donasi">Donasi</option><option value="hibah">Hibah</option><option value="bantuan">Bantuan</option><option value="inventaris_lama">Inventaris Lama</option>
            </select>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Tanggal Masuk</label><input type="date" name="tanggal_masuk" id="f_tgl" class="form-control"></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" id="f_desc" class="form-control" placeholder="Keterangan tambahan..."></textarea></div>
        <div class="form-group"><label class="form-label">Foto</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
        <div style="display:flex;gap:9px;justify-content:flex-end;margin-top:4px">
          <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">Tambah Barang</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const baseUrl = '{{ url('/') }}';
function openModal(){
  document.getElementById('modalTitle').textContent='Tambah Barang Baru';
  document.getElementById('submitBtn').textContent='Tambah Barang';
  document.getElementById('barangForm').action='{{ route('barang.store') }}';
  document.getElementById('formMethod').value='POST';
  document.getElementById('barangForm').reset();
  document.getElementById('lokasiAwalGroup').style.display='block';
  document.getElementById('modalOverlay').style.display='flex';
}
function editBarang(id,kode,nama,kat,sumber,kondisi,jml,tgl,desc){
  document.getElementById('modalTitle').textContent='Edit Barang';
  document.getElementById('submitBtn').textContent='Simpan Perubahan';
  document.getElementById('barangForm').action=baseUrl+'/barang/'+id;
  document.getElementById('formMethod').value='PUT';
  document.getElementById('barangId').value=id;
  document.getElementById('f_kode').value=kode;
  document.getElementById('f_nama').value=nama;
  document.getElementById('f_kat').value=kat;
  document.getElementById('f_sumber').value=sumber;
  document.getElementById('f_kondisi').value=kondisi;
  document.getElementById('f_jml').value=jml;
  document.getElementById('f_tgl').value=tgl;
  document.getElementById('f_desc').value=desc;
  document.getElementById('lokasiAwalGroup').style.display='none';
  document.getElementById('modalOverlay').style.display='flex';
}
function closeModal(){ document.getElementById('modalOverlay').style.display='none'; }
document.getElementById('modalOverlay').addEventListener('click',function(e){ if(e.target===this) closeModal(); });
</script>
@endpush
@endsection
