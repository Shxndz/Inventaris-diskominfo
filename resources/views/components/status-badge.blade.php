@props(['status'])

@php
    $map = [
        'pending'      => ['Menunggu',     'bg-yellow-100 text-yellow-800'],
        'disetujui'    => ['Disetujui',    'bg-green-100 text-green-800'],
        'ditolak'      => ['Ditolak',      'bg-red-100 text-red-800'],
        'dikembalikan' => ['Dikembalikan', 'bg-gray-100 text-gray-700'],
        'dilaporkan'   => ['Dilaporkan',   'bg-red-100 text-red-800'],
        'diperbaiki'   => ['Diperbaiki',   'bg-blue-100 text-blue-800'],
        'selesai'      => ['Selesai',      'bg-green-100 text-green-800'],
        'baik'         => ['Baik',         'bg-green-100 text-green-800'],
        'rusak'        => ['Rusak',        'bg-red-100 text-red-800'],
    ];
    [$label, $class] = $map[$status] ?? [ucfirst($status), 'bg-gray-100 text-gray-700'];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset ring-current/10 {{ $class }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $label }}
</span>