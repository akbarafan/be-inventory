@extends('layouts.app')
@section('title','Kategori')
@section('page-title','Kategori Barang')
@section('page-sub','Kelola kategori inventaris')
@push('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="document.getElementById('katModal').style.display='flex'">
  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
  Tambah Kategori
</button>
@endpush
@section('content')
<div class="p-page">
  <div class="card au">
    <div class="card-head"><span class="card-head-title">Daftar Kategori</span></div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th width="50">No</th><th>Nama Kategori</th><th>Deskripsi</th><th width="80">Jumlah Barang</th><th width="90">Aksi</th></tr></thead>
        <tbody>
          @forelse($kategori as $i=>$k)
          <tr>
            <td style="color:var(--ink4)">{{ $i+1 }}</td>
            <td class="td-name">{{ $k->nama_kategori }}</td>
            <td style="font-size:12px;color:var(--ink3)">{{ $k->deskripsi ?? '-' }}</td>
            <td style="font-weight:500;text-align:center">{{ $k->barang_count }}</td>
            <td>
              <div style="display:flex;gap:4px">
                <button class="icbtn" onclick="editKat({{ $k->id }},'{{ addslashes($k->nama_kategori) }}','{{ addslashes($k->deskripsi ?? '') }}')">
                  <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M8.5 1.5l2 2L4 10H2V8L8.5 1.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                </button>
                <form action="{{ route('kategori.destroy',$k->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="icbtn del"><svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1.5 3h9M4 3V2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1M5 5.5v4M7 5.5v4M2.5 3l.6 6.5a1 1 0 0 0 1 .9h3.8a1 1 0 0 0 1-.9L9.5 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--ink4)">Belum ada kategori</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
<div id="katModal" class="modal-overlay" style="display:none">
  <div class="modal-box sm">
    <div class="modal-head">
      <h3 class="modal-title" id="katModalTitle">Tambah Kategori</h3>
      <button class="modal-close" onclick="closeKatModal()">&times;</button>
    </div>
    <form id="katForm" method="POST" action="{{ route('kategori.store') }}">
      @csrf
      <input type="hidden" name="_method" id="katMethod" value="POST">
      <div style="display:flex;flex-direction:column;gap:14px">
        <div class="form-group"><label class="form-label">Nama Kategori <span style="color:var(--red)">*</span></label><input type="text" name="nama_kategori" id="katNama" class="form-control" required placeholder="Contoh: Elektronik"></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="deskripsi" id="katDesc" class="form-control" placeholder="Keterangan opsional..." style="min-height:70px"></textarea></div>
        <div style="display:flex;gap:9px;justify-content:flex-end">
          <button type="button" class="btn btn-outline" onclick="closeKatModal()">Batal</button>
          <button type="submit" class="btn btn-primary" id="katSubmit">Tambah</button>
        </div>
      </div>
    </form>
  </div>
</div>
@push('scripts')
<script>
function editKat(id,nama,desc){
  document.getElementById('katModalTitle').textContent='Edit Kategori';
  document.getElementById('katSubmit').textContent='Simpan';
  document.getElementById('katForm').action='{{ url('/kategori') }}/'+id;
  document.getElementById('katMethod').value='PUT';
  document.getElementById('katNama').value=nama;
  document.getElementById('katDesc').value=desc;
  document.getElementById('katModal').style.display='flex';
}
function closeKatModal(){
  document.getElementById('katModal').style.display='none';
  document.getElementById('katModalTitle').textContent='Tambah Kategori';
  document.getElementById('katSubmit').textContent='Tambah';
  document.getElementById('katForm').action='{{ route('kategori.store') }}';
  document.getElementById('katMethod').value='POST';
  document.getElementById('katForm').reset();
}
document.getElementById('katModal').addEventListener('click',function(e){if(e.target===this)closeKatModal();});
</script>
@endpush
@endsection
