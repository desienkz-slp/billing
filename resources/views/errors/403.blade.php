@extends('layouts.app')
@section('title', 'Akses Ditolak')

@section('content')
<div style="display:flex;align-items:center;justify-content:center;min-height:60vh;">
    <div style="text-align:center;max-width:420px;">
        <div style="font-size:80px;margin-bottom:16px;opacity:0.15;">🔒</div>
        <h1 style="font-size:48px;font-weight:800;color:var(--accent);margin-bottom:8px;">403</h1>
        <h2 style="font-size:20px;font-weight:600;margin-bottom:12px;">Akses Ditolak</h2>
        <p style="color:var(--text-muted);font-size:14px;line-height:1.6;margin-bottom:24px;">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator untuk mendapatkan akses.' }}
        </p>
        <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:var(--accent);color:#fff;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;transition:all .2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
