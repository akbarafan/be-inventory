@extends('layouts.app')
@section('title','Transaksi')
@section('page-title','Transaksi Barang')
@section('page-sub','Catat aktivitas masuk, keluar, perpindahan, dan perubahan kondisi barang')
@push('topbar-actions')
<button class="btn btn-primary btn-sm" onclick="document.getElementById('trxModal').style.display='flex'">
  <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
  Catat Transaksi
</button>
@endpush
@section('content')
<div class="p-page">
  @if($errors->any())
  <div class="alert alert-error" style="margin-bottom:14px">
    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v3.5M7.5 10v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
    {{ $errors->first() }}
  </div>
  @endif
  @if(session('success'))
  <div class="alert alert-ok" style="margin-bottom:14px">
    <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M5 7.5l2 2 3-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
    {{ session('success') }}
  </div>
  @endif
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
          <option value="update_kondisi" {{ request('jenis')==='update_kondisi'?'selected':'' }}>Update Kondisi</option>
        </select>
        <div class="search-bar"><svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="4" stroke="currentColor" stroke-width="1.3"/><path d="M9 9l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg><input name="search" value="{{ request('search') }}" placeholder="Cari barang..."></div>
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
      </form>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th width="90">Kode</th><th>Nama Barang</th><th width="110">Jenis</th><th width="50">Jml</th><th width="110">Lokasi</th><th width="90">Petugas</th><th width="150">Waktu</th><th width="60">Aksi</th></tr></thead>
        <tbody>
          @forelse($transaksi as $t)
          <tr>
            <td class="td-code">{{ $t->barang?->kode_barang }}</td>
            <td class="td-name">{{ $t->barang?->nama_barang }}</td>
            <td><span class="badge badge-{{ $t->jenis }}">{{ ucfirst(str_replace('_',' ',$t->jenis)) }}</span></td>
            <td style="font-weight:500">{{ $t->jumlah }}</td>
            <td style="font-size:11px;color:var(--ink3)">
              @if($t->jenis === 'pindah' && $t->lokasiAsal && $t->lokasiTujuan)
                {{ $t->lokasiAsal->nama_lokasi }} &rarr; {{ $t->lokasiTujuan->nama_lokasi }}
              @elseif($t->jenis === 'update_kondisi' && $t->lokasiAsal)
                {{ $t->lokasiAsal->nama_lokasi }}
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
  <div class="modal-box" style="width:560px">
    <div class="modal-head">
      <h3 class="modal-title">Catat Transaksi Baru</h3>
      <button class="modal-close" onclick="document.getElementById('trxModal').style.display='none'">&times;</button>
    </div>
    <form method="POST" action="{{ route('transaksi.store') }}">
      @csrf
      <div style="display:flex;flex-direction:column;gap:14px">
        <div class="form-group">
          <label class="form-label">Jenis Transaksi</label>
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:6px">
            @foreach(['masuk'=>['Barang Masuk','blue'],'keluar'=>['Barang Keluar','red'],'pindah'=>['Pindah Lokasi','amber'],'update_kondisi'=>['Update Kondisi','green']] as $jenis=>[$label,$color])
            <label style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:12px 6px;border-radius:9px;cursor:pointer;border:1.5px solid var(--paper3);transition:all .12s;background:#fff" class="jenis-card" data-color="{{ $color }}">
              <input type="radio" name="jenis" value="{{ $jenis }}" style="display:none">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                @if($jenis==='masuk')<path d="M8 14V2M3 9l5 5 5-5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                @elseif($jenis==='keluar')<path d="M8 2v12M3 7l5-5 5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                @elseif($jenis==='pindah')<path d="M2 8h12M6 4L2 8l4 4M10 4l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                @else<path d="M8 2v12M4 8h8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>@endif
              </svg>
              <span style="font-size:11px;font-weight:500;text-align:center">{{ $label }}</span>
            </label>
            @endforeach
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Pilih Barang <span style="color:var(--red)">*</span></label>
          <select name="barang_id" class="form-control" id="barangSelect" required>
            <option value="">Pilih barang</option>
            @foreach($barang as $b)
            <option value="{{ $b->id }}">{{ $b->nama_barang }} ({{ $b->kode_barang }}) — total {{ $b->jumlah }} unit</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group" id="asalField">
            <label class="form-label" id="asalLabel">Lokasi Asal</label>
            <select name="lokasi_asal_id" class="form-control" id="asalSelect">
              <option value="">Pilih lokasi</option>
              @foreach($lokasi as $l)<option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>@endforeach
            </select>
          </div>
          <div class="form-group" id="tujuanField">
            <label class="form-label" id="tujuanLabel">Lokasi Tujuan</label>
            <select name="lokasi_tujuan_id" class="form-control" id="tujuanSelect">
              <option value="">Pilih lokasi</option>
              @foreach($lokasi as $l)<option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>@endforeach
            </select>
          </div>
        </div>

        {{-- SUMBER (only for masuk) --}}
        <div class="form-group" id="sumberField">
          <label class="form-label">Sumber Barang <span style="color:var(--red)">*</span></label>
          <select name="sumber" class="form-control">
            <option value="">Pilih sumber</option>
            <option value="pembelian">Pembelian</option>
            <option value="donasi">Donasi</option>
            <option value="hibah">Hibah</option>
            <option value="bantuan">Bantuan</option>
            <option value="inventaris_lama">Inventaris Lama</option>
          </select>
        </div>

        {{-- STOCK INFO (for non-masuk) --}}
        <div id="stockInfo" style="display:none;background:var(--paper2);border-radius:8px;padding:10px 12px;font-size:12px">
          <div style="font-weight:500;color:var(--ink2);margin-bottom:4px">Stok tersedia di lokasi ini:</div>
          <div id="stockList" style="display:flex;gap:8px;flex-wrap:wrap"></div>
        </div>

        {{-- BATCH SECTION (for masuk) --}}
        <div id="batchSection">
          <label class="form-label">Unit Barang <span style="color:var(--red)">*</span></label>
          <div style="display:flex;flex-direction:column;gap:6px;margin-top:4px" id="batchContainer">
            <div class="batch-row" style="display:flex;gap:8px;align-items:center">
              <span style="font-size:11px;color:var(--ink4);min-width:56px">Kondisi</span>
              <span style="font-size:11px;color:var(--ink4);min-width:60px">Jumlah</span>
              <span style="width:28px"></span>
            </div>
            <div class="batch-row" style="display:flex;gap:8px;align-items:center">
              <select name="kondisi[]" class="form-control" style="width:auto;min-width:100px;padding:6px 24px 6px 8px;font-size:12px">
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
                <option value="hilang">Hilang</option>
              </select>
              <input type="number" name="jumlah[]" class="form-control" style="width:72px;padding:6px 8px;font-size:12px;text-align:center" value="1" min="1">
              <button type="button" class="icbtn" onclick="this.closest('.batch-row').remove()" style="visibility:hidden">
                <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><path d="M2 2l7 7M9 2l-7 7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>
          <button type="button" class="btn btn-outline btn-sm" style="margin-top:6px" onclick="addBatch()">+ Tambah Kondisi</button>
        </div>

        {{-- ITEM SECTION (for non-masuk) --}}
        <div id="itemSection" style="display:none">
          <label class="form-label">Pilih Unit Barang <span style="color:var(--red)">*</span></label>
          <div style="margin-top:4px;max-height:240px;overflow-y:auto;border:1px solid var(--paper3);border-radius:8px" id="itemList">
            <div style="padding:20px;text-align:center;font-size:12px;color:var(--ink4)">Pilih barang dan lokasi asal terlebih dahulu</div>
          </div>
        </div>

        <div class="form-group"><label class="form-label">Keterangan</label><textarea name="keterangan" class="form-control" placeholder="Catatan opsional..." style="min-height:60px"></textarea></div>
        <div style="display:flex;gap:9px;justify-content:flex-end;border-top:1px solid var(--paper3);padding-top:12px">
          <button type="button" class="btn btn-outline" onclick="document.getElementById('trxModal').style.display='none'">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
var barangStok = @json($barangStok);
var barangKondisi = @json($barangKondisi);
var barangItems = @json($barangItems);
var daftarLokasi = @json($lokasiList);
var kondisiOpts = '<option value="baik">Baik</option><option value="rusak">Rusak</option><option value="hilang">Hilang</option>';

function updateStockInfo(){
  var jenis = document.querySelector('input[name="jenis"]:checked');
  if(!jenis) return;
  var jVal = jenis.value;
  var barangId = document.getElementById('barangSelect').value;
  var lokasiId = document.getElementById('asalSelect').value;
  var info = document.getElementById('stockInfo');
  var list = document.getElementById('stockList');

  if((jVal==='keluar'||jVal==='pindah'||jVal==='update_kondisi') && barangId && lokasiId && barangStok[barangId] && barangStok[barangId][lokasiId]){
    info.style.display = 'block';
    var html = 'Total: <strong>' + barangStok[barangId][lokasiId] + '</strong> unit';
    if(barangKondisi[barangId] && barangKondisi[barangId][lokasiId]){
      html += '<div style="margin-top:4px;display:flex;gap:6px;font-size:11px">';
      var labels = {'baik':'Baik','rusak':'Rusak','hilang':'Hilang'};
      for(var k in labels){
        if(barangKondisi[barangId][lokasiId][k]){
          html += '<span style="background:var(--paper);padding:2px 8px;border-radius:4px">' + labels[k] + ': ' + barangKondisi[barangId][lokasiId][k] + '</span>';
        }
      }
      html += '</div>';
    }
    list.innerHTML = html;
  } else {
    info.style.display = 'none';
  }
}

function renderItems(){
  var jenis = document.querySelector('input[name="jenis"]:checked');
  if(!jenis) return;
  var jVal = jenis.value;
  var barangId = document.getElementById('barangSelect').value;
  var lokasiId = document.getElementById('asalSelect').value;
  var list = document.getElementById('itemList');

  if(jVal==='masuk'){
    document.getElementById('itemSection').style.display = 'none';
    document.getElementById('batchSection').style.display = 'block';
    return;
  }

  document.getElementById('batchSection').style.display = 'none';
  document.getElementById('itemSection').style.display = 'block';

  if(!barangId || !lokasiId || !barangItems[barangId] || !barangItems[barangId][lokasiId]){
    list.innerHTML = '<div style="padding:20px;text-align:center;font-size:12px;color:var(--ink4)">Pilih barang dan lokasi asal terlebih dahulu</div>';
    return;
  }

  var items = barangItems[barangId][lokasiId];
  if(!items || items.length===0){
    list.innerHTML = '<div style="padding:20px;text-align:center;font-size:12px;color:var(--ink4)">Tidak ada unit tersedia di lokasi ini</div>';
    return;
  }

  var html = '<table style="width:100%;font-size:12px"><thead><tr style="background:var(--paper2)">' +
    '<th style="padding:6px 8px;text-align:left"><input type="checkbox" onchange="toggleAll(this)" checked></th>' +
    '<th style="padding:6px 8px;text-align:left">#</th>' +
    '<th style="padding:6px 8px;text-align:left">Kondisi Saat Ini</th>' +
    '<th style="padding:6px 8px;text-align:left">Kondisi Baru</th></tr></thead><tbody>';

  items.forEach(function(item, idx){
    var badgeClass = item.kondisi==='baik'?'badge-ok':(item.kondisi==='rusak'?'badge-warn':'badge-bad');
    html += '<tr>' +
      '<td style="padding:6px 8px"><input type="checkbox" name="item_ids[]" value="' + item.id + '" checked class="item-cb"></td>' +
      '<td style="padding:6px 8px;color:var(--ink4)">' + (idx+1) + '</td>' +
      '<td style="padding:6px 8px"><span class="badge ' + badgeClass + '">' + item.kondisi.charAt(0).toUpperCase() + item.kondisi.slice(1) + '</span></td>' +
      '<td style="padding:6px 8px"><select name="item_kondisi[' + item.id + ']" class="form-control" style="width:auto;min-width:90px;padding:4px 20px 4px 6px;font-size:11px">' +
        '<option value="">Tetap</option><option value="baik"' + (item.kondisi==='baik'?'':'') + '>Baik</option>' +
        '<option value="rusak"' + (item.kondisi==='rusak'?'':'') + '>Rusak</option>' +
        '<option value="hilang"' + (item.kondisi==='hilang'?'':'') + '>Hilang</option>' +
      '</select></td></tr>';
  });

  html += '</tbody></table>';
  list.innerHTML = html;
}

function toggleAll(cb){
  document.querySelectorAll('.item-cb').forEach(function(c){ c.checked = cb.checked; });
}

function rebuildSelect(sel, onlyWithStock, barangId){
  var val = sel.value;
  sel.innerHTML = '<option value="">Pilih lokasi</option>';
  daftarLokasi.forEach(function(l){
    if(onlyWithStock && barangId && barangStok[barangId] && (!barangStok[barangId][l.id] || barangStok[barangId][l.id]<=0)) return;
    var opt = document.createElement('option');
    opt.value = l.id;
    opt.textContent = l.nama;
    if(val == l.id) opt.selected = true;
    sel.appendChild(opt);
  });
}

function filterLokasi(){
  var jenisEl = document.querySelector('input[name="jenis"]:checked');
  if(!jenisEl) return;
  var jenis = jenisEl.value;
  var barangId = document.getElementById('barangSelect').value;
  var asal = document.getElementById('asalSelect');
  var tujuan = document.getElementById('tujuanSelect');
  var filterAsal = (jenis==='keluar'||jenis==='pindah'||jenis==='update_kondisi');

  rebuildSelect(asal, filterAsal, barangId);
  rebuildSelect(tujuan, false, null);
  updateStockInfo();
  renderItems();
}

function addBatch(){
  var c = document.getElementById('batchContainer');
  var row = document.createElement('div');
  row.className = 'batch-row';
  row.style.cssText = 'display:flex;gap:8px;align-items:center';
  row.innerHTML = '<select name="kondisi[]" style="width:auto;min-width:100px;padding:6px 24px 6px 8px;font-size:12px;border:1px solid var(--paper3);border-radius:7px;background:#fff;font-family:var(--fs);color:var(--ink)">' +
    kondisiOpts +
    '</select>' +
    '<input type="number" name="jumlah[]" style="width:72px;padding:6px 8px;font-size:12px;text-align:center;border:1px solid var(--paper3);border-radius:7px;background:#fff;font-family:var(--fs);color:var(--ink)" value="1" min="1">' +
    '<button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--ink4);padding:4px;display:flex">' +
    '<svg width="11" height="11" viewBox="0 0 11 11" fill="none"><path d="M2 2l7 7M9 2l-7 7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></button>';
  c.appendChild(row);
}

document.querySelectorAll('.jenis-card').forEach(function(card){
  card.addEventListener('click',function(){
    document.querySelectorAll('.jenis-card').forEach(function(c){
      c.style.borderColor='var(--paper3)';
      c.style.background='#fff';
      c.querySelector('input').checked = false;
    });
    var color=this.dataset.color;
    var paleBg={'blue':'var(--blue-pale)','red':'var(--red-pale)','amber':'var(--amber-pale)','green':'var(--green-pale)'};
    var borderClr={'blue':'var(--blue)','red':'var(--red)','amber':'var(--amber)','green':'var(--green)'};
    this.style.borderColor=borderClr[color];
    this.style.background=paleBg[color];
    this.querySelector('input').checked = true;
    var jenis=this.querySelector('input').value;

    var asalEl=document.getElementById('asalField');
    var tujuanEl=document.getElementById('tujuanField');
    var asalSel=document.getElementById('asalSelect');
    var tujuanSel=document.getElementById('tujuanSelect');
    var asalLbl=document.getElementById('asalLabel');
    var tujuanLbl=document.getElementById('tujuanLabel');
    var srcEl=document.getElementById('sumberField');

    // Show & enable all first
    [asalEl, tujuanEl].forEach(function(el){ el.style.display='block'; });
    [asalSel, tujuanSel].forEach(function(sel){ sel.disabled=false; });

    if(jenis==='masuk'){
      srcEl.style.display='block';
      asalEl.style.display='none';
      asalSel.disabled=true;
      asalLbl.textContent='Lokasi Asal (dari luar)';
      tujuanLbl.innerHTML='Lokasi Tujuan <span style="color:var(--red)">*</span>';
    } else if(jenis==='keluar'){
      srcEl.style.display='none';
      tujuanEl.style.display='none';
      tujuanSel.disabled=true;
      asalLbl.innerHTML='Lokasi Asal <span style="color:var(--red)">*</span>';
      tujuanLbl.textContent='Lokasi Tujuan (ke luar)';
    } else if(jenis==='pindah'){
      srcEl.style.display='none';
      asalLbl.innerHTML='Lokasi Asal <span style="color:var(--red)">*</span>';
      tujuanLbl.innerHTML='Lokasi Tujuan <span style="color:var(--red)">*</span>';
    } else { // update_kondisi
      srcEl.style.display='none';
      tujuanEl.style.display='none';
      tujuanSel.disabled=true;
      asalLbl.innerHTML='Lokasi <span style="color:var(--red)">*</span>';
      tujuanLbl.textContent='Lokasi Tujuan (tidak dipakai)';
    }
    filterLokasi();
  });
});

document.getElementById('barangSelect').addEventListener('change', filterLokasi);
document.getElementById('asalSelect').addEventListener('change', function(){ updateStockInfo(); renderItems(); });

// init
document.querySelector('.jenis-card[data-color="blue"]').click();
document.getElementById('trxModal').addEventListener('click',function(e){if(e.target===this)this.style.display='none';});

// Auto-open modal if validation error
if(document.querySelector('.alert-error')) document.getElementById('trxModal').style.display='flex';
</script>
@endpush
@endsection
