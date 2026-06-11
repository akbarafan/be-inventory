<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Dashboard') — Inventaris SMK Labschool</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stack('styles')
</head>
<body>
<div class="app-shell">

  {{-- SIDEBAR --}}
  <aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sb-brand">
      <div class="sb-mark">
        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK Labschool">
      </div>
      <div>
        <div class="sb-brand-name">Inventaris</div>
        <div class="sb-brand-sub">SMK Labschool Unesa 1</div>
      </div>
    </a>
    <nav class="sb-nav">
      <div class="sb-section">Utama</div>
      <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="1" y="1" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.3"/><rect x="8.5" y="1" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.3"/><rect x="1" y="8.5" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.3"/><rect x="8.5" y="8.5" width="5.5" height="5.5" rx="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
        <span class="sb-link-text">Dashboard</span>
      </a>

      <div class="sb-section">Inventaris</div>
      <a href="{{ route('barang.index') }}" class="sb-link {{ request()->routeIs('barang.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M13 5L7.5 2 2 5v5L7.5 13l5.5-3z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M7.5 2v11M2 5l5.5 3 5.5-3" stroke="currentColor" stroke-width="1.3"/></svg>
        <span class="sb-link-text">Data Barang</span>
      </a>
      <a href="{{ route('kategori.index') }}" class="sb-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="8.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1.5" y="8.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="8.5" y="8.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
        <span class="sb-link-text">Kategori</span>
      </a>
      <a href="{{ route('lokasi.index') }}" class="sb-link {{ request()->routeIs('lokasi.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M1.5 3.5l4-2 5 2 4-2v11l-4 2-5-2-4 2z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M5.5 1.5v11M10.5 3.5v11" stroke="currentColor" stroke-width="1.3"/></svg>
        <span class="sb-link-text">Lokasi</span>
      </a>

      <div class="sb-section">Aktivitas</div>
      <a href="{{ route('transaksi.index') }}" class="sb-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M4 5.5L2 3.5l2-2M2 3.5h10M11 9.5l2 2-2 2M13 11.5H3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span class="sb-link-text">Transaksi</span>
      </a>
      <a href="{{ route('scan.camera') }}" class="sb-link {{ request()->routeIs('scan.camera') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="1.5" y="1.5" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="9" y="1.5" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1.5" y="9" width="4.5" height="4.5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="2.8" y="2.8" width="2" height="2" fill="currentColor"/><rect x="10.2" y="2.8" width="2" height="2" fill="currentColor"/><rect x="2.8" y="10.2" width="2" height="2" fill="currentColor"/><rect x="9" y="9" width="2" height="2" fill="currentColor"/><rect x="13" y="9" width="2" height="2" fill="currentColor"/><rect x="9" y="13" width="2" height="2" fill="currentColor"/><rect x="11" y="11" width="2" height="2" fill="currentColor"/></svg>
        <span class="sb-link-text">Scan QR</span>
      </a>
      <a href="{{ route('scan.logs') }}" class="sb-link {{ request()->routeIs('scan.logs') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4.5v3.5l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
        <span class="sb-link-text">Riwayat Scan</span>
      </a>

      <div class="sb-section">Laporan</div>
      <a href="{{ route('laporan.index') }}" class="sb-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="2" y="9" width="3" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="6" y="6" width="3" height="8" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="10" y="3" width="3" height="11" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
        <span class="sb-link-text">Laporan</span>
      </a>
    </nav>
  </aside>

  {{-- CONTENT --}}
  <div class="content-area">
    <header class="topbar">
      <button onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer;color:var(--ink3);display:flex;padding:0;margin-right:4px;">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M2.5 5h13M2.5 9h13M2.5 13h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
      </button>
      <div class="topbar-title">
        <h1>@yield('page-title','Dashboard')</h1>
        <p>@yield('page-sub','')</p>
      </div>
      @stack('topbar-actions')
      <button class="notif-btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 2a5 5 0 0 0-5 5v3l-1.5 1.5h13L13 10V7a5 5 0 0 0-5-5z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M6.5 13.5a1.5 1.5 0 0 0 3 0" stroke="currentColor" stroke-width="1.4"/></svg>
        <div class="notif-dot"></div>
      </button>
      <div class="user-chip" onclick="document.getElementById('userMenu').classList.toggle('hidden')" style="position:relative;">
        <div class="user-av">{{ strtoupper(substr(Auth::user()->name,0,2)) }}</div>
        <span class="user-name">{{ Auth::user()->name }}</span>
        <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><path d="M2.5 4l3 3 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <div id="userMenu" class="hidden" style="position:absolute;top:calc(100% + 8px);right:0;background:#fff;border:1px solid var(--paper3);border-radius:9px;padding:6px;min-width:160px;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:100;">
          <div style="padding:8px 12px 10px;border-bottom:1px solid var(--paper3);margin-bottom:4px;">
            <div style="font-size:12.5px;font-weight:500;color:var(--ink);">{{ Auth::user()->name }}</div>
            <div style="font-size:11px;color:var(--ink4);">{{ Auth::user()->email }}</div>
          </div>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="width:100%;text-align:left;background:none;border:none;padding:7px 12px;border-radius:6px;cursor:pointer;font-size:12.5px;color:var(--red);font-family:var(--fs);display:flex;align-items:center;gap:7px;">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M5.5 7h7M9.5 4.5L12 7l-2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 2H3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
              Keluar
            </button>
          </form>
        </div>
      </div>
    </header>
    <main class="page-body">
      @if(session('success'))
      <div class="p-page" style="padding-bottom:0">
        <div class="alert alert-success">
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M5 7.5l2 2 3-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
          {{ session('success') }}
        </div>
      </div>
      @endif
      @if(session('info'))
      <div class="p-page" style="padding-bottom:0">
        <div class="alert alert-info">{{ session('info') }}</div>
      </div>
      @endif
      @yield('content')
    </main>
  </div>
</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('collapsed');
}
// Close user menu on outside click
document.addEventListener('click', function(e) {
  const chip = e.target.closest('.user-chip');
  const menu = document.getElementById('userMenu');
  if (!chip) menu.classList.add('hidden');
});
</script>
@stack('scripts')
</body>
</html>
