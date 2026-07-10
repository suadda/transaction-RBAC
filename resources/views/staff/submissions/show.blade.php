<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Detail {{ $submission->submission_no }}</h1>
        <a href="{{ route('staff.submissions.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Informasi Pengajuan</h2>
                    <dl class="row mb-0">
                        <dt class="col-4">Tanggal</dt><dd class="col-8">{{ $submission->date->format('d/m/Y') }}</dd>
                        <dt class="col-4">Kategori</dt><dd class="col-8">{{ $submission->category->name }}</dd>
                        <dt class="col-4">Nominal</dt><dd class="col-8">Rp {{ number_format($submission->amount, 0, ',', '.') }}</dd>
                        <dt class="col-4">Status</dt><dd class="col-8">@include('partials.status-badge', ['status' => $submission->status])</dd>
                        <dt class="col-4">Deskripsi</dt><dd class="col-8">{{ $submission->description }}</dd>
                        @if ($submission->attachment_path)
                            <dt class="col-4">Lampiran</dt>
                            <dd class="col-8"><a href="{{ Storage::url($submission->attachment_path) }}" target="_blank">Lihat dokumen</a></dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-muted mb-3">Riwayat Approval</h2>
                    @forelse ($submission->approvals as $a)
                        <div class="border-start border-3 ps-3 pb-3 {{ $a->status === 'Approved' ? 'border-success' : 'border-danger' }}">
                            <div class="fw-medium">{{ $a->role }} — {{ $a->status }}</div>
                            <div class="small text-muted">oleh {{ $a->user->name }} • {{ $a->created_at->format('d/m/Y H:i') }}</div>
                            @if ($a->comment)<div class="small fst-italic">"{{ $a->comment }}"</div>@endif
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada tindakan approval.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
