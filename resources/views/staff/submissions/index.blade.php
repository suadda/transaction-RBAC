<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Riwayat Pengajuan</h1>
        <a href="{{ route('staff.dashboard') }}" class="btn btn-primary btn-sm">+ Buat Pengajuan</a>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>No. Pengajuan</th><th>Tanggal</th><th>Kategori</th><th>Nominal</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $s)
                        <tr>
                            <td class="fw-medium">{{ $s->submission_no }}</td>
                            <td>{{ $s->date->format('d/m/Y') }}</td>
                            <td>{{ $s->category->name }}</td>
                            <td>Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                            <td>@include('partials.status-badge', ['status' => $s->status])</td>
                            <td><a href="{{ route('staff.submissions.show', $s) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
