<x-app-layout>
    @php $isAdmin = auth()->user()->isAdmin(); @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dashboard</p>
                <h1 class="mt-1 text-xl font-semibold text-white sm:text-2xl">Halo, {{ auth()->user()->name }} 👋</h1>
                <p class="mt-1 text-sm text-slate-300">
                    {{ now()->translatedFormat('l, d F Y') }}
                    @unless ($isAdmin)
                        &middot; {{ auth()->user()->sekbid?->nama }}
                    @endunless
                </p>
            </div>

            @unless ($isAdmin)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('peminjaman.create') }}"
                       class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-950/20 transition duration-200 hover:-translate-y-0.5 hover:bg-blue-500">Ajukan Peminjaman</a>
                    <a href="{{ route('laporan.create') }}"
                       class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-950/20 transition duration-200 hover:-translate-y-0.5 hover:bg-red-500">Laporkan Kerusakan</a>
                </div>
            @endunless
        </div>
    </x-slot>

    {{-- Kartu ringkasan --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 {{ $isAdmin ? 'xl:grid-cols-4' : 'lg:grid-cols-3' }}">
        @foreach ($stats as $s)
            <a href="{{ $s['url'] }}" class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-lg">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-500">{{ $s['label'] }}</p>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition-colors group-hover:bg-indigo-50 group-hover:text-indigo-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M8 15l4-4 4 4 5-6" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <p class="text-3xl font-semibold tracking-tight {{ $s['warna'] }}">{{ $s['value'] }}</p>
                    <span class="text-xs font-medium text-slate-400 transition-colors group-hover:text-indigo-600">Lihat detail &rarr;</span>
                </div>
            </a>
        @endforeach
    </div>

    @if ($isAdmin)
        {{-- ================= ADMIN ================= --}}
        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Pengajuan menunggu --}}
            <x-panel title="Pengajuan Menunggu Keputusan" :link="route('admin.peminjaman.index', ['status' => 'pending'])">
                <ul class="divide-y divide-gray-100">
                    @forelse ($pengajuanBaru as $p)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $p->barang->nama }} <span class="font-normal text-gray-500">×{{ $p->jumlah }}</span></p>
                                <p class="truncate text-xs text-gray-500">
                                    {{ $p->user->name }} &middot; {{ $p->user->sekbid->nama ?? '-' }} &middot; diajukan {{ $p->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <a href="{{ route('admin.peminjaman.show', $p) }}"
                               class="shrink-0 rounded-md bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100">Tinjau</a>
                        </li>
                    @empty
                        <li class="mx-4 my-3 rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Tidak ada pengajuan yang menunggu. 🎉</li>
                    @endforelse
                </ul>
            </x-panel>

            {{-- Jatuh tempo --}}
            <x-panel title="Jatuh Tempo & Terlambat" :link="route('admin.peminjaman.index', ['status' => 'disetujui'])">
                <ul class="divide-y divide-gray-100">
                    @forelse ($jatuhTempo as $p)
                        @php $lewat = $p->tgl_kembali->isPast(); @endphp
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $p->barang->nama }} <span class="font-normal text-gray-500">×{{ $p->jumlah }}</span></p>
                                <p class="truncate text-xs text-gray-500">{{ $p->user->name }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-xs font-medium {{ $lewat ? 'text-red-600' : 'text-yellow-600' }}">
                                    {{ $lewat ? 'Terlambat' : 'Jatuh tempo' }} {{ $p->tgl_kembali->diffForHumans() }}
                                </p>
                                <a href="{{ route('admin.peminjaman.show', $p) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Detail</a>
                            </div>
                        </li>
                    @empty
                        <li class="mx-4 my-3 rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Tidak ada peminjaman yang mendekati batas waktu.</li>
                    @endforelse
                </ul>
            </x-panel>

            {{-- Laporan aktif --}}
            <x-panel title="Laporan Kerusakan Aktif" :link="route('admin.laporan.index')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($laporanAktif as $l)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $l->barang->nama }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $l->ruangan->nama }} &middot; {{ $l->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <x-status-badge :status="$l->status" />
                                <a href="{{ route('admin.laporan.show', $l) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Tinjau</a>
                            </div>
                        </li>
                    @empty
                        <li class="mx-4 my-3 rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Tidak ada laporan kerusakan yang aktif.</li>
                    @endforelse
                </ul>
            </x-panel>

            {{-- Grafik + kondisi --}}
            <div class="space-y-6">
                <x-panel title="Pengajuan Peminjaman, 6 Bulan Terakhir">
                    @php $maks = max(1, $grafik->max('total')); @endphp
                    <div class="flex h-48 items-end gap-3 px-5 pb-4 pt-6">
                        @foreach ($grafik as $g)
                            <div class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                                <span class="text-xs font-medium text-gray-600">{{ $g['total'] }}</span>
                                  <div class="w-full max-w-[44px] rounded-t-md bg-indigo-500"
                                      @style(['height' => max(2, ($g['total'] / $maks) * 100) . '%'])></div>
                                <span class="text-xs text-gray-500">{{ $g['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-panel>

                <x-panel title="Kondisi Barang" :link="route('admin.barang.index')">
                    @php
                        $totalBarang = max(1, $kondisi->sum());
                        $segmen = [
                            'baik'       => ['Baik',       'bg-green-500'],
                            'diperbaiki' => ['Diperbaiki', 'bg-blue-500'],
                            'rusak'      => ['Rusak',      'bg-red-500'],
                        ];
                    @endphp
                    <div class="space-y-4 px-5 py-4">
                        <div class="flex h-3 overflow-hidden rounded-full bg-gray-100">
                            @foreach ($segmen as $key => [$label, $warna])
                                @php
                                    $percent = number_format((($kondisi[$key] ?? 0) / $totalBarang) * 100, 2, '.', '');
                                @endphp
                                <div class="{{ $warna }}" @style(['width' => $percent . '%'])></div>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                            @foreach ($segmen as $key => [$label, $warna])
                                <span class="flex items-center gap-2 text-gray-600">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $warna }}"></span>
                                    {{ $label }}: <span class="font-medium text-gray-900">{{ $kondisi[$key] ?? 0 }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </x-panel>
            </div>
        </div>

    @else
        {{-- ================= USER ================= --}}
        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Peminjaman berjalan --}}
            <x-panel title="Sedang Anda Pinjam" :link="route('peminjaman.index')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($berjalan as $p)
                        @php
                            $lewat = $p->tgl_kembali->isPast();
                            $segera = ! $lewat && $p->tgl_kembali->lte(now()->addDay());
                        @endphp
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $p->barang->nama }} <span class="font-normal text-gray-500">×{{ $p->jumlah }}</span></p>
                                <p class="truncate text-xs text-gray-500">{{ $p->ruangan->nama }}</p>
                            </div>
                            <p class="shrink-0 text-right text-xs font-medium {{ $lewat ? 'text-red-600' : ($segera ? 'text-yellow-600' : 'text-gray-500') }}">
                                {{ $lewat ? 'Terlambat dikembalikan' : 'Kembali' }}<br>{{ $p->tgl_kembali->translatedFormat('d M, H:i') }}
                            </p>
                        </li>
                    @empty
                        <li class="mx-4 my-3 rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Tidak ada barang yang sedang Anda pinjam.</li>
                    @endforelse
                </ul>
            </x-panel>

            {{-- Laporan aktif --}}
            <x-panel title="Laporan Kerusakan Anda yang Diproses" :link="route('laporan.index')">
                <ul class="divide-y divide-gray-100">
                    @forelse ($laporanAktif as $l)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $l->barang->nama }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $l->ruangan->nama }} &middot; {{ $l->created_at->diffForHumans() }}</p>
                            </div>
                            <x-status-badge :status="$l->status" />
                        </li>
                    @empty
                        <li class="mx-4 my-3 rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Tidak ada laporan yang sedang diproses.</li>
                    @endforelse
                </ul>
            </x-panel>

            {{-- Pengajuan terakhir --}}
            <x-panel title="Pengajuan Terakhir" :link="route('peminjaman.index')" class="lg:col-span-2">
                <ul class="divide-y divide-gray-100">
                    @forelse ($pengajuanTerakhir as $p)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $p->barang->nama }} <span class="font-normal text-gray-500">×{{ $p->jumlah }}</span></p>
                                <p class="truncate text-xs text-gray-500">
                                    {{ $p->tgl_pinjam->translatedFormat('d M Y H:i') }} &middot; diajukan {{ $p->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <x-status-badge :status="$p->status" />
                        </li>
                    @empty
                        <li class="mx-4 my-3 rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">
                            Belum ada pengajuan. <a href="{{ route('peminjaman.create') }}" class="font-medium text-indigo-600 hover:text-indigo-800">Ajukan peminjaman pertama Anda</a>.
                        </li>
                    @endforelse
                </ul>
            </x-panel>
        </div>
    @endif
</x-app-layout>