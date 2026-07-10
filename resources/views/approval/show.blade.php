<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Review {{ $submission->submission_no }}</h1>
        <a href="{{ route('approval.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Antrian</a>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4">Pengaju</dt><dd class="col-8">{{ $submission->user->name }}</dd>
                        <dt class="col-4">Tanggal</dt><dd class="col-8">{{ $submission->date->format('d/m/Y') }}</dd>
                        <dt class="col-4">Kategori</dt><dd class="col-8">{{ $submission->category->name }}</dd>
                        <dt class="col-4">Nominal</dt><dd class="col-8">Rp {{ number_format($submission->amount, 0, ',', '.') }}</dd>
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
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form action="{{ route('approval.approve', $submission) }}" method="POST">
                        @csrf
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="comment" rows="2" class="form-control mb-2"></textarea>
                        <button class="btn btn-success w-100">Approve</button>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('approval.reject', $submission) }}" method="POST">
                        @csrf
                        <label class="form-label">Alasan penolakan (wajib)</label>
                        <textarea name="comment" rows="2" class="form-control mb-2" required></textarea>
                        <button class="btn btn-danger w-100">Reject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
