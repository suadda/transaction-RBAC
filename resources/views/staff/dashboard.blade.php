<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Form Pengajuan Transaksi</h1>
        <a href="{{ route('staff.submissions.index') }}" class="btn btn-outline-secondary btn-sm">Riwayat</a>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('staff.submissions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="text" id="amount_view" class="form-control" placeholder="Contoh: 1.000.000" required>
                            <input type="hidden" name="amount" id="amount_real" value="{{ old('amount') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="4" class="form-control" required>{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lampiran Dokumen (Opsional)</label>
                            <input type="file" name="attachment_path" class="form-control">
                            <div class="form-text">PDF/JPG/PNG, maksimal 5 MB.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const amountView = document.getElementById('amount_view');
        const amountReal = document.getElementById('amount_real');
        amountView.addEventListener('input', function () {
            let value = this.value.replace(/[^0-9]/g, '');
            amountReal.value = value;
            this.value = value !== '' ? new Intl.NumberFormat('id-ID').format(value) : '';
        });
    </script>
    @endpush
</x-app-layout>
