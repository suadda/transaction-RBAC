<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Menunggu Pembayaran</h1>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pengajuan</th><th>Pengaju</th><th>Kategori</th>
                        <th>Nominal</th><th>Sisa Budget</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $s)
                        <tr>
                            <td class="fw-medium">{{ $s->submission_no }}</td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ $s->category->name }}</td>
                            <td>Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($s->category->remainingBudget(), 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('finance.pay', $s) }}" method="POST" onsubmit="return confirm('Proses pembayaran?')">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Proses Bayar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada transaksi menunggu pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
