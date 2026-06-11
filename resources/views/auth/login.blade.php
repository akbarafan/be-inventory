<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk — Inventaris SMK Labschool</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:var(--fs);background:var(--paper);display:flex;min-height:100vh}
.login-side{width:400px;background:var(--navy);display:flex;flex-direction:column;justify-content:space-between;padding:44px 40px;position:relative;flex-shrink:0}
.login-main{flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:48px 32px}
.login-card{width:100%;max-width:400px}
.brand-block{display:flex;align-items:center;gap:14px;margin-bottom:48px}
.brand-logo{width:52px;height:52px;border-radius:50%;background:#fff;padding:3px;flex-shrink:0;display:flex;align-items:center;justify-content:center}
.brand-logo img{width:46px;height:46px;object-fit:contain;border-radius:50%}
.brand-name{font-size:13px;color:#fff;font-weight:600;line-height:1.3}
.brand-sub{font-size:10.5px;color:rgba(255,255,255,.35);margin-top:1px}
.hero-text{margin-bottom:0}
.hero-text h1{font-size:30px;font-weight:700;color:#fff;line-height:1.2;letter-spacing:-.3px;margin-bottom:10px}
.hero-text p{font-size:13px;color:rgba(255,255,255,.4);line-height:1.7;max-width:340px}
.login-side-footer{font-size:10.5px;color:rgba(255,255,255,.18);padding-top:20px;border-top:1px solid rgba(255,255,255,.07)}
.form-header{margin-bottom:32px}
.form-header h2{font-size:24px;font-weight:600;color:var(--ink);margin-bottom:6px}
.form-header p{font-size:13px;color:var(--ink4)}
.role-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.role-card{display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 12px;background:#fff;border:1.5px solid var(--paper3);border-radius:10px;cursor:pointer;transition:all .15s}
.role-card:hover{border-color:var(--ink3)}
.role-card.active{border-color:var(--navy);background:#f3f5fa}
.role-icon{width:36px;height:36px;border-radius:8px;background:var(--paper2);display:flex;align-items:center;justify-content:center;color:var(--ink3);flex-shrink:0}
.role-card.active .role-icon{background:rgba(15,42,110,.1);color:var(--navy)}
.role-card.active .role-name{color:var(--navy)}
.role-name{font-size:12.5px;font-weight:600;color:var(--ink)}
.role-desc{font-size:10.5px;color:var(--ink4);text-align:center;line-height:1.3}
.input-group{margin-bottom:20px}
.input-group label{font-size:12px;font-weight:500;color:var(--ink2);display:block;margin-bottom:6px}
.input-icon{position:relative}
.input-icon .icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--ink4);display:flex;pointer-events:none}
.input-icon .form-control{padding-left:38px}
.input-icon .toggle-pw{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--ink4);padding:6px;display:flex}
.btn-login{width:100%;height:46px;background:var(--navy);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;font-family:var(--fs);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s}
.btn-login:hover{background:var(--navy2)}
.btn-login:active{transform:scale(.99)}
.form-footer{margin-top:28px;padding-top:20px;border-top:1px solid var(--paper3);display:flex;align-items:center;justify-content:space-between}
.form-footer-left{display:flex;align-items:center;gap:8px}
.form-footer-left img{width:24px;height:24px;object-fit:contain;border-radius:50%}
.form-footer-left span{font-size:11.5px;color:var(--ink4)}
.form-footer-links a{font-size:11.5px;color:var(--ink4);text-decoration:none;margin-left:14px}
.form-footer-links a:hover{color:var(--navy)}
@media(max-width:768px){
  .login-side{display:none}
  .login-main{padding:32px 20px}
}
</style>
</head>
<body>
<div class="login-side">
  <div>
    <div class="brand-block">
      <div class="brand-logo">
        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK Labschool">
      </div>
      <div>
        <div class="brand-name">SMK Labschool Unesa 1</div>
        <div class="brand-sub">Surabaya, Jawa Timur</div>
      </div>
    </div>
    <div class="hero-text">
      <h1>Sistem Inventaris Sekolah</h1>
      <p>Platform pengelolaan aset dan inventaris yang terintegrasi untuk mendukung administrasi sekolah secara efisien.</p>
    </div>
  </div>
  <div class="login-side-footer">&copy; {{ date('Y') }} SMK Labschool Unesa 1 Surabaya</div>
</div>

<div class="login-main">
  <div class="login-card">
    <div class="form-header">
      <h2>Selamat datang</h2>
      <p>Masuk menggunakan akun yang telah diberikan oleh administrator.</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}">
      @csrf

      @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:20px">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v3.5M7.5 10v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
        {{ $errors->first() }}
      </div>
      @endif

      <div class="input-group">
        <label>Masuk sebagai</label>
        <div class="role-grid">
          <div class="role-card {{ old('role','admin')==='admin'?'active':'' }}" onclick="selectRole(this,'admin')">
            <div class="role-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M7.5 1.5L13 4v4.5C13 11.7 9.5 14 7.5 14.5 5.5 14 2 11.7 2 8.5V4z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M5 8.2l2.5 2.5 3-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <div class="role-name">Administrator</div>
            <div class="role-desc">Akses penuh sistem</div>
          </div>
          <div class="role-card {{ old('role')==='user'?'active':'' }}" onclick="selectRole(this,'user')">
            <div class="role-icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="5.5" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="5.5" r="2.3" stroke="currentColor" stroke-width="1.3"/><path d="M2.5 13a5.5 5.5 0 0 1 10 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
            <div class="role-name">User</div>
            <div class="role-desc">Kelola dan transaksi</div>
          </div>
        </div>
        <input type="hidden" name="role" id="roleInput" value="{{ old('role','admin') }}">
      </div>

      <div class="input-group">
        <label>Username</label>
        <div class="input-icon">
          <div class="icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="5" r="3" stroke="currentColor" stroke-width="1.3"/><path d="M1.5 13.5a6 6 0 0 1 12 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
          <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
        </div>
      </div>

      <div class="input-group">
        <label>Password</label>
        <div class="input-icon">
          <div class="icon"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="3" y="6.5" width="9" height="7" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M5.5 6.5V4.5a2.5 2.5 0 0 1 5 0v2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="7.5" cy="9.5" r="1" fill="currentColor"/></svg></div>
          <input type="password" name="password" id="pwInput" class="form-control has-toggle" placeholder="Masukkan password" required>
          <button type="button" class="toggle-pw" onclick="togglePw()"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M1 7.5C2 4 4.5 2 7.5 2s5.5 2 6.5 5.5C13 11 10.5 13 7.5 13S2 11 1 7.5Z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="7.5" r="2" stroke="currentColor" stroke-width="1.3"/></svg></button>
        </div>
        <div style="font-size:11px;color:var(--ink4);margin-top:4px">Lupa password? Hubungi administrator.</div>
      </div>

      <button type="submit" class="btn-login">
        Masuk
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 7h8M7.5 3.5L11 7l-3.5 3.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>
    </form>

    <div class="form-footer">
      <div class="form-footer-left">
        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK">
        <span>Inventaris SMK Labschool</span>
      </div>
      <div class="form-footer-links">
        <a href="#">Panduan</a>
        <a href="#">Bantuan</a>
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