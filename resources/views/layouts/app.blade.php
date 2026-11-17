@php
$isAdmin = auth()->user()->isAdmin();

$groups = $isAdmin ? [
'Menu Utama' => [
'Dashboard' => 'dashboard',
'Peminjaman' => 'admin.peminjaman.index',
'Laporan Kerusakan' => 'admin.laporan.index',
],
'Data Master' => [
'Barang' => 'admin.barang.index',
'Ruangan' => 'admin.ruangan.index',
'Kategori Barang' => 'admin.kategori.index',
'Sekbid' => 'admin.sekbid.index',
],
'Akun' => [
'Profil Saya' => 'profile.edit',
],
] : [
'Menu Utama' => [
'Dashboard' => 'dashboard',
'Peminjaman Saya' => 'peminjaman.index',
'Laporan Kerusakan' => 'laporan.index',
],
'Akun' => [
'Profil Saya' => 'profile.edit',
],
];
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
    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div x-data="{ open: window.matchMedia('(min-width: 1024px)').matches }" @keydown.escape.window="open = false" class="min-h-screen">

        {{-- Overlay (mobile) --}}
        <div x-show="open" x-cloak @click="open = false" aria-hidden="true" class="fixed inset-0 z-30 bg-slate-950/60 backdrop-blur-[2px] lg:hidden"></div>

        {{-- Sidebar --}}
        <aside id="app-sidebar" x-cloak aria-label="Navigasi utama" class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-800 bg-slate-900 text-slate-200 shadow-2xl shadow-slate-950/30 transition-transform duration-300 ease-out"
            :class="open ? 'translate-x-0' : '-translate-x-full'">

            <div class="flex items-center justify-between gap-3 px-5 py-5">
                <div class="flex min-w-0 items-center gap-3">
                    <x-app-logo class="h-9 shrink-0 drop-shadow-md" />
                    <div class="min-w-0">
                        <p class="text-base font-semibold text-white">Inventaris</p>
                        <p class="text-xs text-slate-400">Diskominfo</p>
                    </div>
                </div>
                <button type="button" @click="open = false" aria-label="Tutup menu"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mx-3 h-px bg-slate-800" aria-hidden="true"></div>

            <nav aria-label="Menu sidebar" class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                @foreach ($groups as $judul => $items)
                @php $items = collect($items)->filter(fn ($r) => Route::has($r)); @endphp
                @if ($items->isNotEmpty())
                <div>
                    <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $judul }}</p>
                    <ul class="space-y-1">
                        @foreach ($items as $label => $route)
                        @php
                        $pattern = str_contains($route, '.')
                        ? \Illuminate\Support\Str::beforeLast($route, '.') . '.*'
                        : $route;
                        $active = request()->routeIs($pattern);
                        $badgeCount = $isAdmin ? match ($route) {
                            'admin.peminjaman.index' => $adminPendingLoanCount,
                            'admin.laporan.index' => $adminNewReportCount,
                            default => 0,
                        } : 0;
                        $badgeLabel = match ($route) {
                            'admin.peminjaman.index' => 'pengajuan peminjaman baru',
                            'admin.laporan.index' => 'laporan kerusakan baru',
                            default => '',
                        };
                        @endphp
                        <li>
                            <a href="{{ route($route) }}" @click="if (window.innerWidth < 1024) open = false" @if ($active) aria-current="page" @endif
                                class="flex min-h-11 items-center justify-between gap-3 rounded-r-xl border-l-2 px-3 py-2.5 text-sm font-medium transition-colors {{ $active ? 'border-indigo-400 bg-slate-800 text-white shadow-sm' : 'border-transparent text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <span>{{ $label }}</span>
                                @if ($badgeCount > 0)
                                    <span aria-label="{{ $badgeCount }} {{ $badgeLabel }}"
                                        class="inline-flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold leading-none text-white">
                                        {{ $badgeCount > 99 ? '99+' : $badgeCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @endforeach
            </nav>

            <div class="border-t border-slate-800 p-4">
                <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->sekbid?->nama ?? 'Admin' }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-slate-400 hover:text-white">Keluar</button>
                </form>
            </div>
        </aside>

        {{-- Konten --}}
        <div class="min-h-screen min-w-0 transition-[padding] duration-300" :class="open ? 'lg:pl-64' : 'lg:pl-0'">
            <header class="app-header-enter sticky top-0 z-20 isolate border-b border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-800 text-white shadow-lg shadow-slate-950/10">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-400/10 blur-3xl"></div>
                </div>
                <div class="relative z-10 flex items-center gap-3 px-4 py-5 sm:px-8">
                    <button type="button" x-show="!open" x-cloak @click="open = true" :aria-expanded="open.toString()" aria-controls="app-sidebar"
                        aria-label="Buka menu"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-200 transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="min-w-0 flex-1">{{ $header ?? '' }}</div>
                    @if ($isAdmin)
                        @include('components.admin-notification-menu', [
                            'unreadCount' => $adminUnreadNotificationCount,
                            'notifications' => $adminRecentNotifications,
                        ])
                    @endif
                </div>
            </header>

            <main class="app-page-enter min-w-0 flex-1 p-4 sm:p-8">
                @if (session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
                @endif
                @if (session('error'))
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
                @endif

                <div class="app-content-enter">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>