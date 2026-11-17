<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-white sm:text-2xl">Laporan Kerusakan</h1>
            <p class="mt-1 text-sm text-slate-300">Tinjau laporan dan perbarui status penanganan kerusakan.</p>
        </div>
    </x-slot>

    @php
        $tabs = [
            ''           => 'Semua',
            'dilaporkan' => 'Dilaporkan',
            'diperbaiki' => 'Diperbaiki',
            'selesai'    => 'Selesai',
        ];
    @endphp
    <div class="mb-5 flex flex-col gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
            @foreach ($tabs as $value => $label)
                @php $aktif = (string) request('status') === (string) $value; @endphp
                <a href="{{ route('admin.laporan.index', array_filter(['status' => $value, 'q' => request('q')])) }}"
                   class="rounded-full px-4 py-2 text-sm font-semibold transition-colors {{ $aktif ? 'bg-slate-900 text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                    {{ $label }}
                    @if ($value !== '')
                        <span class="ml-1 text-xs opacity-70">{{ $jumlah[$value] ?? 0 }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <form method="GET" class="flex w-full gap-2 lg:w-auto">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pelapor atau barang..."
                   class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 lg:w-72">
            <button class="rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white transition-colors hover:bg-slate-800">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-white text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Barang</th>
                    <th class="whitespace-nowrap px-5 py-4">Ruangan</th>
                    <th class="whitespace-nowrap px-5 py-4">Pelapor</th>
                    <th class="whitespace-nowrap px-5 py-4">Tanggal</th>
                    <th class="whitespace-nowrap px-5 py-4">Status</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($laporans as $l)
                    <tr class="transition-colors hover:bg-slate-50/80">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">{{ $l->barang->nama }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $l->barang->kode_barang }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $l->ruangan->nama }}</td>
                        <td class="px-5 py-4 text-slate-700">
                            {{ $l->user->name }}
                            <p class="mt-1 text-xs text-slate-500">{{ $l->user->sekbid->nama ?? '-' }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $l->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-4"><x-status-badge :status="$l->status" /></td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a href="{{ route('admin.laporan.show', $l) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-colors hover:bg-indigo-50 hover:text-indigo-800">
                                {{ $l->status === 'selesai' ? 'Detail' : 'Tinjau' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8">
                            <div class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Tidak ada laporan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $laporans->links() }}</div>
</x-app-layout>