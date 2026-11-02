<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Siswa — INVENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-xl shadow p-8 text-center space-y-3">
        <h1 class="text-xl font-bold text-slate-900">Halo, {{ auth()->user()->nama }} 👋</h1>
        <p class="text-slate-500 text-sm">
            Login berhasil sebagai <b>{{ auth()->user()->tipe_user ?? 'user' }}</b>
            ({{ auth()->user()->role->nama_role }}).
            Dashboard siswa lengkap akan dibangun di tahap berikutnya.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="mt-4 px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Keluar</button>
        </form>
    </div>
</body>
</html>
