<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Menunggu Pembayaran (Finance)</h2>
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
                            <th class="px-4 py-3">Sisa Budget</th>
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
                                <td class="px-4 py-3">Rp {{ number_format($s->category->remainingBudget(), 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('finance.pay', $s) }}" method="POST" onsubmit="return confirm('Proses pembayaran?')">
                                        @csrf
                                        <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded">Proses Bayar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada transaksi menunggu pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
