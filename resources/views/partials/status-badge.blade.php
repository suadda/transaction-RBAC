@php
    $map = [
        'Draft' => 'bg-gray-100 text-gray-700',
        'Submitted' => 'bg-gray-100 text-gray-700',
        'Waiting SPV Approval' => 'bg-yellow-100 text-yellow-800',
        'Waiting Manager Approval' => 'bg-yellow-100 text-yellow-800',
        'Waiting Director Approval' => 'bg-yellow-100 text-yellow-800',
        'Waiting Finance' => 'bg-blue-100 text-blue-800',
        'Paid' => 'bg-green-100 text-green-800',
        'Rejected' => 'bg-red-100 text-red-800',
    ];
    $cls = $map[$status] ?? 'bg-gray-100 text-gray-700';
@endphp
<span class="inline-block px-2 py-1 text-xs font-semibold rounded {{ $cls }}">{{ $status }}</span>
