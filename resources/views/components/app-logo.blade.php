@php
    $file = 'WhatsApp Image 2026-01-21 at 10.06.29.png';
    $path = public_path("images/$file");
@endphp

@if (file_exists($path))
    <img src="{{ asset("images/$file") }}?v={{ filemtime($path) }}"
         alt="Logo Diskominfo" {{ $attributes->merge(['class' => 'w-auto']) }}>
@endif