<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white sm:text-2xl">Laporan Kerusakan</h1>
                <p class="mt-1 text-sm text-slate-300">Pantau laporan kerusakan barang dan status penanganannya.</p>
            </div>
            <a href="{{ route('laporan.create') }}"
               class="inline-flex w-fit items-center justify-center rounded-md bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700">+ Buat Laporan</a>
        </div>
    </x-slot>

    <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-white text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="whitespace-nowrap px-5 py-4">Barang</th>
                    <th class="whitespace-nowrap px-5 py-4">Ruangan</th>
                    <th class="whitespace-nowrap px-5 py-4">Deskripsi</th>
                    <th class="whitespace-nowrap px-5 py-4">Dilaporkan</th>
                    <th class="whitespace-nowrap px-5 py-4">Status</th>
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
                        <td class="max-w-xs px-5 py-4 leading-relaxed text-slate-600">
                            {{ \Illuminate\Support\Str::limit($l->deskripsi, 80) }}
                            @if ($l->foto_url)
                                <a href="{{ $l->foto_url }}" target="_blank" class="ml-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">Lihat foto</a>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $l->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <x-status-badge :status="$l->status" />
                            @if ($l->catatan_admin)
                                <p class="mt-2 max-w-xs text-xs leading-relaxed text-slate-500">Catatan admin: {{ $l->catatan_admin }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8">
                            <div class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Anda belum pernah membuat laporan.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $laporans->links() }}</div>
</x-app-layout>