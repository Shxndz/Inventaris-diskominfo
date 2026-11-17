@php
    $wide = $attributes->has('wide');

    $bg = file_exists(public_path('images/image.png')) ? 'image.png' : null;
    $bgUrl = $bg ? asset("images/$bg") . '?v=' . filemtime(public_path("images/$bg")) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Inventaris Diskominfo') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-800">
<div class="relative isolate min-h-screen lg:grid lg:grid-cols-2">

    @if ($bgUrl)
        <img src="{{ $bgUrl }}" alt="" aria-hidden="true" class="auth-background-enter absolute inset-0 h-full w-full object-cover lg:hidden">
    @endif
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950/75 via-slate-900/65 to-slate-950/80 lg:hidden"></div>

    <div class="auth-enter auth-enter-brand absolute left-4 top-4 z-20 flex items-center gap-2 rounded-xl border border-white/15 bg-slate-950/35 p-2 text-white shadow-lg backdrop-blur-sm lg:hidden">
        <img src="{{ asset('images/WhatsApp%20Image%202026-11-16%20at%2010.06.44.png') }}"
             alt="Logo Kabupaten Bogor" class="h-10 w-auto object-contain">
        <x-app-logo class="h-10 w-auto object-contain" />
        <p class="hidden max-w-32 text-xs font-medium leading-snug text-white sm:block">
            Dinas Komunikasi dan Informatika
        </p>
    </div>

    {{-- Panel gambar (desktop) --}}
    <aside class="relative hidden flex-col justify-between overflow-hidden bg-slate-900 p-8 text-slate-200 lg:sticky lg:top-0 lg:flex lg:h-screen">

        @if ($bgUrl)
            <img src="{{ $bgUrl }}" alt="" aria-hidden="true" class="auth-background-enter absolute inset-0 h-full w-full object-cover">
        @endif
        {{-- Lapisan gelap agar teks tetap terbaca --}}
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/60 to-slate-900/40"></div>

        <div class="auth-enter auth-enter-brand relative -ml-4 -mt-4 flex items-center gap-3">
            <img src="{{ asset('images/WhatsApp%20Image%202026-11-16%20at%2010.06.44.png') }}"
                 alt="Logo Kabupaten Bogor" class="h-12 w-auto object-contain drop-shadow-sm">
            <x-app-logo class="h-12 drop-shadow-md" />
            <p class="max-w-xs text-xs font-medium leading-snug text-slate-200">
                Dinas Komunikasi dan Informatika
            </p>
        </div>

        <div class="relative flex w-full flex-col items-center gap-6">
            <div class="auth-enter auth-enter-copy text-center">
                <p class="text-3xl font-semibold leading-snug text-white">Inventaris</p>
            </div>

            <div class="auth-enter auth-enter-form relative mx-auto w-full max-w-md space-y-8 rounded-2xl border border-white/15 bg-slate-950/75 p-6 shadow-2xl backdrop-blur-sm">
                <h2 class="text-3xl font-semibold leading-snug text-white">
                    Pinjam barang dan laporkan kerusakan, semuanya dalam satu tempat.
                </h2>
                <ul class="auth-feature-list space-y-4 text-sm">
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-semibold text-white">1</span>
                        <span>Ajukan peminjaman barang untuk ruangan yang Anda butuhkan.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-semibold text-white">2</span>
                        <span>Pantau status persetujuan langsung dari akun Anda.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-semibold text-white">3</span>
                        <span>Laporkan barang rusak dan lihat perkembangan perbaikannya.</span>
                    </li>
                </ul>
            </div>
        </div>

        <p class="relative text-xs text-slate-400">&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika</p>
    </aside>

    {{-- Form --}}
    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 pb-8 pt-24 sm:px-8 lg:min-h-0 lg:bg-gray-50 lg:py-10">
        <div class="w-full {{ $wide ? 'max-w-xl' : 'max-w-md' }}">
            <p class="auth-enter auth-enter-copy mb-5 text-center text-3xl font-semibold leading-snug text-white drop-shadow-lg lg:hidden">
                Inventaris
            </p>
            <div class="auth-enter auth-enter-form auth-form-card rounded-2xl border border-gray-200 bg-white/95 p-6 shadow-xl backdrop-blur-sm sm:p-9 lg:bg-white">
                {{ $slot }}
            </div>
        </div>
    </main>
</div>
</body>
</html>