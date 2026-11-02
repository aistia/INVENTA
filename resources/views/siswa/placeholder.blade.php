@extends('layouts.siswa')

@section('title', $title)

@section('content')
<div class="mb-6">
    <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">INVENTA // LAB RPL</p>
    <h1 class="font-display font-bold text-2xl text-slate-900">{{ $title }}</h1>
</div>

<div class="bg-white rounded-2xl p-10 shadow-sm text-center">
    <div class="w-14 h-14 mx-auto rounded-2xl bg-violet-100 flex items-center justify-center text-violet-600 mb-4">
        <span class="material-symbols-outlined text-[26px]">construction</span>
    </div>
    <h2 class="font-semibold text-slate-900 mb-1">Halaman "{{ $title }}" Sedang Dikerjakan</h2>
    <p class="text-sm text-slate-500 max-w-md mx-auto">
        Fitur ini akan dibangun di tahap berikutnya.
    </p>
</div>
@endsection