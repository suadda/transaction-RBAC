@php
    $map = [
        'Draft' => 'bg-secondary',
        'Submitted' => 'bg-secondary',
        'Waiting SPV Approval' => 'bg-warning text-dark',
        'Waiting Manager Approval' => 'bg-warning text-dark',
        'Waiting Director Approval' => 'bg-warning text-dark',
        'Waiting Finance' => 'bg-info text-dark',
        'Paid' => 'bg-success',
        'Rejected' => 'bg-danger',
    ];
    $cls = $map[$status] ?? 'bg-secondary';
@endphp
<span class="badge {{ $cls }}">{{ $status }}</span>
