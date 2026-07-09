<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Antrian Approval ({{ $role }})</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">No. Pengajuan</th>
                            <th class="px-4 py-3">Pengaju</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Nominal</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($submissions as $s)
                            <tr class="border-b">
                                <td class="px-4 py-3 font-medium">{{ $s->submission_no }}</td>
                                <td class="px-4 py-3">{{ $s->user->name }}</td>
                                <td class="px-4 py-3">{{ $s->category->name }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($s->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('approval.show', $s) }}" class="text-blue-600 hover:underline">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada pengajuan yang menunggu persetujuan Anda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
