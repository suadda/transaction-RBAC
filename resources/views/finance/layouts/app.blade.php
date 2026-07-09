<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Pengajuan') }}</title>

    <!-- Bootstrap 5 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    @php $u = auth()->user(); $role = optional($u?->role)->name; @endphp

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">Pengeluaran</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto">
                    @if ($role === 'Staff')
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.dashboard') }}">Buat Pengajuan</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.submissions.index') }}">Riwayat</a></li>
                    @elseif (in_array($role, ['SPV', 'Manager', 'Direktur']))
                        <li class="nav-item"><a class="nav-link" href="{{ route('approval.index') }}">Antrian Approval</a></li>
                    @elseif ($role === 'Finance')
                        <li class="nav-item"><a class="nav-link" href="{{ route('finance.index') }}">Pembayaran</a></li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            {{ $u?->name }} <span class="badge bg-secondary ms-1">{{ $role }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="bg-white border-bottom">
            <div class="container py-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="container py-4">
        {{ $slot }}
    </main>

    <!-- Bootstrap JS bundle (untuk dropdown & navbar toggle) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
