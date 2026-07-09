<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Antrian Approval <span class="badge bg-secondary">{{ $role }}</span></h1>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>No. Pengajuan</th><th>Pengaju</th><th>Kategori</th><th>Nominal</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $s)
                        <tr>
                            <td class="fw-medium">{{ $s->submission_no }}</td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ $s->category->name }}</td>
                            <td>Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                            <td><a href="{{ route('approval.show', $s) }}" class="btn btn-sm btn-primary">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada pengajuan yang menunggu persetujuan Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
