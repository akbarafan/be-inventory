<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — Inventaris SMK Labschool</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
.login-shell{display:grid;grid-template-columns:1fr 420px;min-height:100vh}
.login-left{background:var(--navy);display:flex;flex-direction:column;justify-content:space-between;padding:52px 56px;position:relative;overflow:hidden}
.login-right{background:var(--paper);display:flex;flex-direction:column;justify-content:center;padding:56px 48px;border-left:1px solid var(--paper3)}
.role-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.role-card{display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fff;border:1px solid var(--paper3);border-radius:10px;cursor:pointer;transition:all .14s}
.role-card:hover,.role-card.active{border-color:var(--navy);background:var(--navy-pale)}
.role-card.active{box-shadow:0 0 0 3px rgba(15,42,110,.09)}
.role-icon{width:34px;height:34px;border-radius:8px;background:var(--paper2);display:flex;align-items:center;justify-content:center;color:var(--ink3);flex-shrink:0}
.role-card.active .role-icon{background:rgba(15,42,110,.12);color:var(--navy)}
.role-card.active .role-name{color:var(--navy)}
.btn-login{width:100%;height:50px;background:var(--navy);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:500;font-family:var(--fs);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px}
.btn-login:hover{background:var(--navy2)}
@media(max-width:768px){.login-shell{grid-template-columns:1fr}.login-left{display:none}}
</style>
</head>
<body style="margin:0">
<div class="login-shell">
  <div class="login-left">
    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,var(--gold) 25%,var(--gold2) 55%,transparent)"></div>
    <div style="position:absolute;inset:0;pointer-events:none;opacity:.03">
      <svg viewBox="0 0 600 800" style="width:100%;height:100%">
        @php for($r=0;$r<12;$r++){for($c=0;$c<9;$c++){ @endphp
          <rect x="{{ $c*72 }}" y="{{ $r*72 }}" width="60" height="60" rx="5" fill="none" stroke="white" stroke-width=".5"/>
        @php }} @endphp
      </svg>
    </div>
    <div style="position:relative;z-index:1" class="au au1">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:52px;padding-bottom:32px;border-bottom:1px solid rgba(255,255,255,.1)">
        <div style="width:64px;height:64px;border-radius:50%;background:#fff;padding:4px;border:2px solid rgba(184,146,42,.5);flex-shrink:0;display:flex;align-items:center;justify-content:center;">
          <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK Labschool" style="width:56px;height:56px;object-fit:contain;border-radius:50%;">
        </div>
        <div>
          <div style="font-size:9px;color:var(--gold2);text-transform:uppercase;letter-spacing:2.5px;font-weight:500;margin-bottom:4px">Sistem Inventaris</div>
          <div style="font-size:16px;font-weight:600;color:#fff">SMK Labschool Unesa 1</div>
          <div style="font-size:11.5px;color:rgba(255,255,255,.4);margin-top:2px;font-weight:300">Surabaya, Jawa Timur</div>
        </div>
      </div>
      <h1 style="font-family:var(--ff);font-size:54px;font-weight:300;color:#fff;line-height:1.05;letter-spacing:-1.5px;margin-bottom:20px">
        Sistem<br>Inventaris<br><span style="font-style:italic;color:#A8BEF0">Sekolah</span>
      </h1>
      <p style="font-size:14px;color:rgba(255,255,255,.44);line-height:1.8;font-weight:300;max-width:380px">
        Platform pengelolaan aset dan inventaris resmi SMK Labschool Unesa 1 Surabaya yang terintegrasi, akurat, dan mudah digunakan.
      </p>
      <div style="width:44px;height:1px;background:rgba(255,255,255,.15);margin:32px 0"></div>
      <div style="display:flex;flex-direction:column;gap:14px">
        @foreach([['Manajemen Barang','Data aset lengkap dengan QR Code unik'],['Transaksi Real-time','Pencatatan masuk, keluar dan perpindahan barang'],['Laporan Otomatis','Sinkronisasi langsung ke Google Sheets']] as $f)
        <div style="display:flex;align-items:flex-start;gap:12px">
          <div style="width:18px;height:18px;border-radius:50%;background:rgba(201,148,26,.15);border:1px solid rgba(201,148,26,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
            <div style="width:5px;height:5px;border-radius:50%;background:var(--gold2)"></div>
          </div>
          <div style="font-size:13px;color:rgba(255,255,255,.44);line-height:1.6;font-weight:300">
            <strong style="color:rgba(255,255,255,.82);font-weight:500">{{ $f[0] }}</strong><br>{{ $f[1] }}
          </div>
        </div>
        @endforeach
      </div>
    </div>
    <div style="position:relative;z-index:1;border-top:1px solid rgba(255,255,255,.08);padding-top:22px">
      <span style="font-size:10.5px;color:rgba(255,255,255,.22);font-weight:300">&copy; {{ date('Y') }} SMK Labschool Unesa 1 Surabaya</span>
    </div>
  </div>

  <div class="login-right">
    <div class="au au1">
      <div style="display:flex;align-items:center;gap:10px;font-size:10px;color:var(--ink4);text-transform:uppercase;letter-spacing:2.5px;font-weight:500;margin-bottom:14px">
        <div style="width:24px;height:1px;background:var(--gold);flex-shrink:0"></div>
        Portal Masuk
      </div>
      <h2 style="font-family:var(--ff);font-size:38px;font-weight:400;color:var(--ink);line-height:1.1;letter-spacing:-.5px;margin-bottom:8px">
        Masuk ke<br><em style="font-style:italic;color:var(--navy)">Sistem Inventaris</em>
      </h2>
      <p style="font-size:13px;color:var(--ink3);font-weight:300;line-height:1.65;margin-bottom:36px">
        Gunakan akun yang telah diberikan oleh administrator sekolah.
      </p>
    </div>

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div style="display:flex;flex-direction:column;gap:22px" class="au au2">
        @if($errors->any())
        <div class="alert alert-error">
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v3.5M7.5 10v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
          {{ $errors->first() }}
        </div>
        @endif

        <div>
          <label style="font-size:12px;font-weight:500;color:var(--ink2);display:block;margin-bottom:7px">Masuk sebagai</label>
          <div class="role-grid">
            <div class="role-card {{ old('role','admin')==='admin'?'active':'' }}" onclick="selectRole(this,'admin')">
              <div class="role-icon"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1.5L12.5 4v4.5C12.5 11.2 9.5 13 7 13.5 4.5 13 1.5 11.2 1.5 8.5V4z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M4.5 7.2l2 2 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
              <div><div class="role-name">Administrator</div><div style="font-size:10.5px;color:var(--ink4);margin-top:1px">Akses penuh sistem</div></div>
            </div>
            <div class="role-card {{ old('role')==='user'?'active':'' }}" onclick="selectRole(this,'user')">
              <div class="role-icon"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="5.5" stroke="currentColor" stroke-width="1.3"/><circle cx="7" cy="5.5" r="1.8" stroke="currentColor" stroke-width="1.3"/><path d="M3.4 11.5a4 4 0 0 1 7.2 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
              <div><div class="role-name">User</div><div style="font-size:10.5px;color:var(--ink4);margin-top:1px">Kelola dan transaksi</div></div>
            </div>
          </div>
          <input type="hidden" name="role" id="roleInput" value="{{ old('role','admin') }}">
        </div>

        <div>
          <label style="font-size:12px;font-weight:500;color:var(--ink2);display:block;margin-bottom:7px">Username</label>
          <div class="input-icon">
            <div class="icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="5" r="2.8" stroke="currentColor" stroke-width="1.3"/><path d="M2 13.5c0-2.8 2.5-5 5.5-5s5.5 2.2 5.5 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
          </div>
        </div>

        <div>
          <label style="font-size:12px;font-weight:500;color:var(--ink2);display:block;margin-bottom:7px">Password</label>
          <div class="input-icon">
            <div class="icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="3" y="6.5" width="9" height="7" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M5.5 6.5V4.5a2.5 2.5 0 0 1 5 0v2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="7.5" cy="10" r="1" fill="currentColor"/></svg></div>
            <input type="password" name="password" id="pwInput" class="form-control has-toggle" placeholder="Masukkan password" required>
            <button type="button" class="toggle-pw" onclick="togglePw()"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M1 7.5C2 4 4.5 2 7.5 2s5.5 2 6.5 5.5C13 11 10.5 13 7.5 13S2 11 1 7.5Z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="2" stroke="currentColor" stroke-width="1.3"/></svg></button>
          </div>
          <div style="font-size:11px;color:var(--ink4);font-weight:300;margin-top:5px">Lupa password? Hubungi administrator sekolah.</div>
        </div>

        <button type="submit" class="btn-login">
          Masuk ke Sistem
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 7h8M7.5 3.5L11 7l-3.5 3.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </form>

    <div style="margin-top:36px;padding-top:24px;border-top:1px solid var(--paper3);display:flex;align-items:center;justify-content:space-between" class="au au3">
      <div style="display:flex;align-items:center;gap:8px">
        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK" style="width:28px;height:28px;object-fit:contain;border-radius:50%;background:#fff;padding:2px;">
        <span style="font-size:12px;color:var(--ink3)">Inventaris SMK Labschool</span>
      </div>
      <div>
        <span style="font-size:11.5px;color:var(--ink4);cursor:pointer;margin-left:16px">Panduan</span>
        <span style="font-size:11.5px;color:var(--ink4);cursor:pointer;margin-left:16px">Bantuan</span>
      </div>
    </div>
  </div>
</div>
<script>
function selectRole(el,role){
  document.getElementById('roleInput').value=role;
  document.querySelectorAll('.role-card').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');
}
function togglePw(){
  const i=document.getElementById('pwInput');
  i.type=i.type==='password'?'text':'password';
}
</script>
</body>
</html>