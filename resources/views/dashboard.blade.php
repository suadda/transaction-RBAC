<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Dashboard Statistik</h1>
    </x-slot>

    {{-- Kartu ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Pengajuan</div>
                    <div class="h3 mb-0">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-muted small">Dalam Proses</div>
                    <div class="h3 mb-0">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100 border-start border-success border-4">
                <div class="card-body">
                    <div class="text-muted small">Sudah Dibayar</div>
                    <div class="h3 mb-0">{{ $stats['paid'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="text-muted small">Ditolak</div>
                    <div class="h3 mb-0">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Total nominal Paid + rincian per status --}}
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-muted small">Total Nominal Dibayar</div>
                    <div class="h4 mb-0 text-success">Rp {{ number_format($stats['paid_amount'], 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white fw-medium">Pengajuan per Status</div>
                <ul class="list-group list-group-flush">
                    @forelse ($byStatus as $status => $total)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @include('partials.status-badge', ['status' => $status])
                            <span class="badge bg-secondary rounded-pill">{{ $total }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Sisa budget per kategori --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-medium">Penggunaan Budget per Kategori</div>
                <div class="card-body">
                    @forelse ($categories as $c)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small">
                                <span class="fw-medium">{{ $c['name'] }}</span>
                                <span class="text-muted">
                                    Sisa Rp {{ number_format($c['remaining'], 0, ',', '.') }}
                                    / Rp {{ number_format($c['total'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="progress mt-1" style="height: 8px;">
                                <div class="progress-bar {{ $c['percent'] >= 90 ? 'bg-danger' : ($c['percent'] >= 70 ? 'bg-warning' : 'bg-success') }}"
                                     style="width: {{ $c['percent'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada kategori.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
