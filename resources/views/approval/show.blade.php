<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Review {{ $submission->submission_no }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Pengaju</dt><dd>{{ $submission->user->name }}</dd></div>
                    <div><dt class="text-gray-500">Tanggal</dt><dd>{{ $submission->date->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">Kategori</dt><dd>{{ $submission->category->name }}</dd></div>
                    <div><dt class="text-gray-500">Nominal</dt><dd>Rp {{ number_format($submission->amount, 0, ',', '.') }}</dd></div>
                    <div class="col-span-2"><dt class="text-gray-500">Deskripsi</dt><dd>{{ $submission->description }}</dd></div>
                    @if ($submission->attachment_path)
                        <div class="col-span-2"><dt class="text-gray-500">Lampiran</dt>
                            <dd><a href="{{ Storage::url($submission->attachment_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat dokumen</a></dd></div>
                    @endif
                </dl>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex gap-4">
                <form action="{{ route('approval.approve', $submission) }}" method="POST" class="flex-1">
                    @csrf
                    <textarea name="comment" rows="2" placeholder="Catatan (opsional)" class="w-full border-gray-300 rounded-md text-sm mb-2"></textarea>
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded">Approve</button>
                </form>
                <form action="{{ route('approval.reject', $submission) }}" method="POST" class="flex-1">
                    @csrf
                    <textarea name="comment" rows="2" placeholder="Alasan penolakan (wajib)" class="w-full border-gray-300 rounded-md text-sm mb-2"></textarea>
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded">Reject</button>
                </form>
            </div>

            <a href="{{ route('approval.index') }}" class="text-blue-600 hover:underline">&larr; Kembali ke antrian</a>
        </div>
    </div>
</x-app-layout>
