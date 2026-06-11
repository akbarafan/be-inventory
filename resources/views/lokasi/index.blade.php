@extends('layouts.app')
@section('title','Lokasi')
@section('page-title','Lokasi Penyimpanan')
@section('page-sub','Kelola lokasi penyimpanan barang')
@push('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="document.getElementById('lokModal').style.display='flex'">
  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
  Tambah Lokasi
</button>
@endpush
@section('content')
<div class="p-page">
  <div class="card au">
    <div class="card-head"><span class="card-head-title">Daftar Lokasi</span></div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th width="50">No</th><th>Nama Lokasi</th><th>Deskripsi</th><th width="80">Jumlah Barang</th><th width="90">Aksi</th></tr></thead>
        <tbody>
          @forelse($lokasi as $i=>$l)
          <tr>
            <td style="color:var(--ink4)">{{ $i+1 }}</td>
            <td class="td-name">{{ $l->nama_lokasi }}</td>
            <td style="font-size:12px;color:var(--ink3)">{{ $l->deskripsi ?? '-' }}</td>
            <td style="font-weight:500;text-align:center">{{ $l->barang_lokasi_count }}</td>
            <td>
              <div style="display:flex;gap:4px">
                <button class="icbtn" onclick="showDetail({{ $l->id }})" title="Lihat barang">
                  <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 6s2-3.5 4.5-3.5S10.5 6 10.5 6s-2 3.5-4.5 3.5S1.5 6 1.5 6z" stroke="currentColor" stroke-width="1.2"/><circle cx="6" cy="6" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
                </button>
                <button class="icbtn" onclick="editLok({{ $l->id }},'{{ addslashes($l->nama_lokasi) }}','{{ addslashes($l->deskripsi ?? '') }}')">
                  <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M8.5 1.5l2 2L4 10H2V8L8.5 1.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                </button>
                <form action="{{ route('lokasi.destroy',$l->id) }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="icbtn del"><svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 3h9M4 3V2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1M5 5.5v4M7 5.5v4M2.5 3l.6 6.5a1 1 0 0 0 1 .9h3.8a1 1 0 0 0 1-.9L9.5 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--ink4)">Belum ada lokasi</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
<div id="lokModal" class="modal-overlay" style="display:none">
  <div class="modal-box sm">
    <div class="modal-head">
      <h3 class="modal-title" id="lokModalTitle">Tambah Lokasi</h3>
      <button class="modal-close" onclick="closeLokModal()">&times;</button>
    </div>
    <form id="lokForm" method="POST" action="{{ route('lokasi.store') }}">
      @csrf
      <input type="hidden" name="_method" id="lokMethod" value="POST">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div class="form-group"><label class="form-label">Nama Lokasi <span style="color:var(--red)">*</span></label><input type="text" name="nama_lokasi" id="lokNama" class="form-control" required placeholder="Contoh: Ruang IT"></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" id="lokDesc" class="form-control" placeholder="Keterangan opsional..." style="min-height:70px"></textarea></div>
        <div style="display:flex;gap:9px;justify-content:flex-end">
          <button type="button" class="btn btn-outline" onclick="closeLokModal()">Batal</button>
          <button type="submit" class="btn btn-primary" id="lokSubmit">Tambah</button>
        </div>
      </div>
    </form>
  </div>
</div>
{{-- DETAIL MODAL --}}
<div id="detailModal" class="modal-overlay" style="display:none">
  <div class="modal-box sm">
    <div class="modal-head">
      <h3 class="modal-title" id="detailModalTitle">Barang di Lokasi</h3>
      <button class="modal-close" onclick="closeDetailModal()">&times;</button>
    </div>
    <div id="detailBody" style="max-height:350px;overflow-y:auto"></div>
  </div>
</div>

@push('scripts')
<script>
var lokasiData = @json($lokasiData);

function showDetail(id){
  var data = lokasiData[id];
  if(!data) return;
  document.getElementById('detailModalTitle').textContent = 'Barang di ' + data.nama;
  var body = document.getElementById('detailBody');
  if(data.barang.length === 0){
    body.innerHTML = '<div style="padding:32px;text-align:center;color:var(--ink4);font-size:13px">Tidak ada barang di lokasi ini</div>';
  } else {
    var html = '<table style="width:100%;font-size:12px"><thead><tr><th style="padding:8px 12px;text-align:left;border-bottom:1px solid var(--paper3);color:var(--ink4);font-weight:500">Kode</th><th style="padding:8px 12px;text-align:left;border-bottom:1px solid var(--paper3);color:var(--ink4);font-weight:500">Nama Barang</th><th style="padding:8px 12px;text-align:center;border-bottom:1px solid var(--paper3);color:var(--ink4);font-weight:500">Stok</th></tr></thead><tbody>';
    data.barang.forEach(function(b){
      html += '<tr><td style="padding:8px 12px;font-family:monospace;color:var(--ink3)">' + (b.kode || '-') + '</td><td style="padding:8px 12px;font-weight:500">' + (b.nama || '-') + '</td><td style="padding:8px 12px;text-align:center;font-weight:600">' + b.jumlah + '</td></tr>';
    });
    html += '</tbody></table>';
    body.innerHTML = html;
  }
  document.getElementById('detailModal').style.display = 'flex';
}

function closeDetailModal(){
  document.getElementById('detailModal').style.display = 'none';
}

document.getElementById('detailModal').addEventListener('click',function(e){if(e.target===this)closeDetailModal();});

function editLok(id,nama,desc){
  document.getElementById('lokModalTitle').textContent='Edit Lokasi';
  document.getElementById('lokSubmit').textContent='Simpan';
  document.getElementById('lokForm').action='{{ url('/lokasi') }}/'+id;
  document.getElementById('lokMethod').value='PUT';
  document.getElementById('lokNama').value=nama;
  document.getElementById('lokDesc').value=desc;
  document.getElementById('lokModal').style.display='flex';
}
function closeLokModal(){
  document.getElementById('lokModal').style.display='none';
  document.getElementById('lokModalTitle').textContent='Tambah Lokasi';
  document.getElementById('lokSubmit').textContent='Tambah';
  document.getElementById('lokForm').action='{{ route('lokasi.store') }}';
  document.getElementById('lokMethod').value='POST';
  document.getElementById('lokForm').reset();
}
document.getElementById('lokModal').addEventListener('click',function(e){if(e.target===this)closeLokModal();});
</script>
@endpush
@endsection
