@extends('layouts.app')
@section('title','Scan QR')
@section('page-title','Scan QR Barang')
@section('page-sub','Scan QR Code untuk melihat detail dan riwayat barang')
@section('content')
<div class="p-page">
  <div style="display:grid;grid-template-columns:360px 1fr;gap:16px" class="au">
    <div class="card">
      <div class="card-head"><span class="card-head-title">Kamera Scanner</span></div>
      <div style="padding:20px;display:flex;flex-direction:column;align-items:center;gap:14px">
        <div class="qr-viewport" style="max-width:240px">
          <div class="qr-corner tl"></div><div class="qr-corner tr"></div>
          <div class="qr-corner bl"></div><div class="qr-corner br"></div>
          <div class="qr-scanline" id="scanLine" style="display:none"></div>
          <div id="cameraFeed" style="width:100%;height:100%;object-fit:cover;display:none">
            <video id="videoElem" style="width:100%;height:100%;object-fit:cover" autoplay playsinline></video>
            <canvas id="canvasElem" style="display:none"></canvas>
          </div>
          <div id="cameraOff" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--ink4);width:100%;height:100%">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none"><path d="M4 10a4 4 0 0 1 4-4h2l2-3h8l2 3h2a4 4 0 0 1 4 4v16a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z" stroke="currentColor" stroke-width="1.5"/><circle cx="20" cy="18" r="6" stroke="currentColor" stroke-width="1.5"/></svg>
            <span style="font-size:12px">Kamera tidak aktif</span>
          </div>
        </div>
        <p style="font-size:12px;color:var(--ink3);text-align:center;line-height:1.6;max-width:240px">
          Arahkan kamera ke QR Code pada barang atau masukkan kode manual
        </p>
        <div style="display:flex;gap:8px;width:100%">
          <button class="btn btn-primary" style="flex:1;justify-content:center" id="btnCamera" onclick="startCamera()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1.5 4a2 2 0 0 1 2-2h.7l1-1.5h3.6l1 1.5h.7a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-9a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="1.3"/><circle cx="7" cy="7.5" r="2.5" stroke="currentColor" stroke-width="1.3"/></svg>
            Buka Kamera
          </button>
          <label class="btn btn-outline" style="flex:1;justify-content:center;cursor:pointer">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 10V2M3.5 5.5l3.5-3.5 3.5 3.5M2 12h10" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Upload
            <input type="file" accept="image/*" onchange="handleUpload(event)" style="display:none">
          </label>
        </div>
        <div style="width:100%">
          <div style="font-size:11.5px;color:var(--ink3);margin-bottom:6px;text-align:center">atau masukkan kode manual</div>
          <form action="{{ route('barang.scan','__kode__') }}" method="GET" id="manualForm" onsubmit="submitManual(event)">
            <div style="display:flex;gap:7px">
              <input type="text" id="manualKode" class="form-control" placeholder="Contoh: BRG-0001" style="flex:1">
              <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div id="resultArea">
      <div class="card" style="height:100%;display:flex;align-items:center;justify-content:center">
        <div style="text-align:center;padding:40px">
          <div style="width:64px;height:64px;border-radius:16px;background:var(--paper2);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:var(--ink4)">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none"><rect x="2" y="2" width="10" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><rect x="20" y="2" width="10" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><rect x="2" y="20" width="10" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/><rect x="5" y="5" width="4" height="4" fill="currentColor"/><rect x="23" y="5" width="4" height="4" fill="currentColor"/><rect x="5" y="23" width="4" height="4" fill="currentColor"/><rect x="20" y="20" width="4" height="4" fill="currentColor"/><rect x="28" y="20" width="4" height="4" fill="currentColor"/><rect x="20" y="28" width="4" height="4" fill="currentColor"/><rect x="24" y="24" width="4" height="4" fill="currentColor"/></svg>
          </div>
          <div style="font-size:16px;font-weight:500;color:var(--ink);margin-bottom:6px">Belum ada hasil scan</div>
          <div style="font-size:13px;color:var(--ink3);line-height:1.6;max-width:260px">Gunakan kamera atau upload gambar QR Code untuk melihat detail barang</div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let scanInterval;
function startCamera(){
  navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}})
  .then(function(stream){
    document.getElementById('cameraOff').style.display='none';
    document.getElementById('cameraFeed').style.display='block';
    document.getElementById('scanLine').style.display='block';
    document.getElementById('videoElem').srcObject=stream;
    scanInterval=setInterval(scanFrame,400);
  })
  .catch(function(){ alert('Tidak dapat mengakses kamera. Gunakan input manual.'); });
}
function scanFrame(){
  var video=document.getElementById('videoElem');
  if(video.readyState!==video.HAVE_ENOUGH_DATA)return;
  var canvas=document.getElementById('canvasElem');
  canvas.width=video.videoWidth; canvas.height=video.videoHeight;
  var ctx=canvas.getContext('2d');
  ctx.drawImage(video,0,0,canvas.width,canvas.height);
  var imageData=ctx.getImageData(0,0,canvas.width,canvas.height);
  var code=jsQR(imageData.data,imageData.width,imageData.height);
  if(code){
    clearInterval(scanInterval);
    navigateScan(code.data);
  }
}
function navigateScan(data){
  var kode=data.includes('/')? data.split('/').pop() : data;
  window.location.href='{{ url('/scan') }}/'+kode;
}
function handleUpload(e){
  var file=e.target.files[0]; if(!file)return;
  var reader=new FileReader();
  reader.onload=function(ev){
    var img=new Image(); img.src=ev.target.result;
    img.onload=function(){
      var canvas=document.createElement('canvas');
      canvas.width=img.width; canvas.height=img.height;
      var ctx=canvas.getContext('2d'); ctx.drawImage(img,0,0);
      var d=ctx.getImageData(0,0,canvas.width,canvas.height);
      var code=jsQR(d.data,d.width,d.height);
      if(code){ navigateScan(code.data); }
      else{ alert('QR Code tidak ditemukan dalam gambar.'); }
    };
  };
  reader.readAsDataURL(file);
}
function submitManual(e){
  e.preventDefault();
  var kode=document.getElementById('manualKode').value.trim();
  if(kode) window.location.href='{{ url('/scan') }}/'+kode;
}
</script>
@endpush
@endsection
