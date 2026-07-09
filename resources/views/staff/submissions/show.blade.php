<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detail Pengajuan {{ $submission->submission_no }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Tanggal</dt><dd>{{ $submission->date->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">Kategori</dt><dd>{{ $submission->category->name }}</dd></div>
                    <div><dt class="text-gray-500">Nominal</dt><dd>Rp {{ number_format($submission->amount, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd>@include('partials.status-badge', ['status' => $submission->status])</dd></div>
                    <div class="col-span-2"><dt class="text-gray-500">Deskripsi</dt><dd>{{ $submission->description }}</dd></div>
                    @if ($submission->attachment_path)
                        <div class="col-span-2">
                            <dt class="text-gray-500">Lampiran</dt>
                            <dd><a href="{{ Storage::url($submission->attachment_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat dokumen</a></dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Riwayat Approval</h3>
                @forelse ($submission->approvals as $a)
                    <div class="border-l-2 pl-4 py-2 {{ $a->status === 'Approved' ? 'border-green-400' : 'border-red-400' }}">
                        <div class="text-sm font-medium">{{ $a->role }} — {{ $a->status }}</div>
                        <div class="text-xs text-gray-500">oleh {{ $a->user->name }} • {{ $a->created_at->format('d/m/Y H:i') }}</div>
                        @if ($a->comment)<div class="text-sm mt-1">"{{ $a->comment }}"</div>@endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Belum ada tindakan approval.</p>
                @endforelse
            </div>

            <a href="{{ route('staff.submissions.index') }}" class="text-blue-600 hover:underline">&larr; Kembali</a>
        </div>
    </div>
</x-app-layout>
