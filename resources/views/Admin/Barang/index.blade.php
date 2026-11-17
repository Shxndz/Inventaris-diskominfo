<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold text-white">Data Barang</h1>
            <a href="{{ route('admin.barang.create') }}"
               class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Tambah Barang</a>
        </div>
    </x-slot>

    {{-- Filter --}}
    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / kode..."
               class="rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500 lg:col-span-2">

        <select name="kategori" class="rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            <option value="">Semua kategori</option>
            @foreach ($kategoris as $k)
                <option value="{{ $k->id }}" @selected(request('kategori') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>

        <select name="ruangan" class="rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            <option value="">Semua ruangan</option>
            @foreach ($ruangans as $r)
                <option value="{{ $r->id }}" @selected(request('ruangan') == $r->id)>{{ $r->nama }}</option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <select name="kondisi" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                <option value="">Semua kondisi</option>
                @foreach (['baik' => 'Baik', 'rusak' => 'Rusak', 'diperbaiki' => 'Diperbaiki'] as $v => $l)
                    <option value="{{ $v }}" @selected(request('kondisi') == $v)>{{ $l }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white transition-colors hover:bg-slate-800">Cari</button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-white text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Barang</th>
                    <th class="whitespace-nowrap px-5 py-4">Kategori</th>
                    <th class="whitespace-nowrap px-5 py-4">Ruangan</th>
                    <th class="whitespace-nowrap px-5 py-4">Jumlah</th>
                    <th class="whitespace-nowrap px-5 py-4">Kondisi</th>
                    <th class="whitespace-nowrap px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($barangs as $b)
                    <tr class="transition-colors hover:bg-slate-50/80">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if ($b->foto_url)
                                    <img src="{{ $b->foto_url }}" alt="" class="h-11 w-11 rounded-xl object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-xs font-semibold text-slate-400">N/A</div>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $b->nama }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $b->kode_barang }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $b->kategori->nama }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $b->ruangan->nama ?? '-' }}</td>
                        <td class="px-5 py-4 font-medium text-slate-700">{{ $b->jumlah }}</td>
                        <td class="px-5 py-4"><x-status-badge :status="$b->kondisi" /></td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a href="{{ route('admin.barang.edit', $b) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-colors hover:bg-indigo-50 hover:text-indigo-800">Ubah</a>
                            <form method="POST" action="{{ route('admin.barang.destroy', $b) }}" class="ml-3 inline"
                                  onsubmit="return confirm('Hapus barang ini?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8">
                            <div class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada data barang.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $barangs->links() }}</div>
</x-app-layout>