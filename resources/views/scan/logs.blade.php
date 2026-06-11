@extends('layouts.app')
@section('title','Riwayat Scan')
@section('page-title','Riwayat Scan QR')
@section('page-sub','Log aktivitas scan QR Code')
@section('content')
<div class="p-page">
  <div class="card au">
    <div class="card-head"><span class="card-head-title">Log Scan Terbaru</span></div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>Waktu</th><th>Barang</th><th width="90">Kode</th><th>Petugas</th><th>Device</th><th width="110">IP</th></tr></thead>
        <tbody>
          @forelse($logs as $log)
          <tr>
            <td style="font-size:12px;color:var(--ink3);white-space:nowrap">{{ \Carbon\Carbon::parse($log->scanned_at)->format('d M Y H:i') }}</td>
            <td class="td-name">{{ $log->barang?->nama_barang ?? '-' }}</td>
            <td class="td-code">{{ $log->barang?->kode_barang ?? '-' }}</td>
            <td style="font-size:12.5px">{{ $log->user?->name ?? 'Tamu' }}</td>
            <td style="font-size:11px;color:var(--ink4);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Str::limit($log->device,40) }}</td>
            <td style="font-size:11px;font-family:monospace;color:var(--ink4)">{{ $log->ip_address }}</td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--ink4)">Belum ada riwayat scan</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($logs->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--paper3)">{{ $logs->links() }}</div>
    @endif
  </div>
</div>
@endsection
