<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-white sm:text-2xl">Pengajuan Peminjaman</h1>
            <p class="mt-1 text-sm text-slate-300">Tinjau dan kelola pengajuan peminjaman barang.</p>
        </div>
    </x-slot>

    {{-- Tab status --}}
    @php
        $tabs = [
            ''             => 'Semua',
            'pending'      => 'Menunggu',
            'disetujui'    => 'Disetujui',
            'ditolak'      => 'Ditolak',
            'dikembalikan' => 'Dikembalikan',
        ];
    @endphp
    <div class="mb-5 flex flex-col gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
            @foreach ($tabs as $value => $label)
                @php $aktif = (string) request('status') === (string) $value; @endphp
                <a href="{{ route('admin.peminjaman.index', array_filter(['status' => $value, 'q' => request('q')])) }}"
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama peminjam atau barang..."
                   class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 lg:w-72">
            <button class="rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white transition-colors hover:bg-slate-800">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-white text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Peminjam</th>
                    <th class="whitespace-nowrap px-5 py-4">Barang</th>
                    <th class="whitespace-nowrap px-5 py-4">Ruangan</th>
                    <th class="whitespace-nowrap px-5 py-4">Jadwal</th>
                    <th class="whitespace-nowrap px-5 py-4">Status</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($peminjamans as $p)
                    <tr class="transition-colors hover:bg-slate-50/80">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">{{ $p->user->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $p->user->sekbid->nama ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-700">
                            {{ $p->barang->nama }}
                            <p class="mt-1 text-xs text-slate-500">Jumlah: {{ $p->jumlah }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $p->ruangan->nama }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                            {{ $p->tgl_pinjam->translatedFormat('d M Y H:i') }}<br>
                            <span class="text-xs text-slate-400">s/d</span>
                            {{ $p->tgl_kembali->translatedFormat('d M Y H:i') }}
                        </td>
                        <td class="px-5 py-4">
                            <x-status-badge :status="$p->status" />
                            @if ($p->status === 'disetujui' && $p->tgl_kembali->isPast())
                                <p class="mt-2 text-xs font-semibold text-red-600">Melewati batas waktu</p>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a href="{{ route('admin.peminjaman.show', $p) }}"
                               class="rounded-lg px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-colors hover:bg-indigo-50 hover:text-indigo-800">
                                {{ $p->status === 'pending' ? 'Tinjau' : 'Detail' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8">
                            <div class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Tidak ada pengajuan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $peminjamans->links() }}</div>
</x-app-layout>