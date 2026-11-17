<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white sm:text-2xl">Peminjaman Saya</h1>
                <p class="mt-1 text-sm text-slate-300">Pantau status pengajuan dan jadwal peminjaman barang Anda.</p>
            </div>
            <a href="{{ route('peminjaman.create') }}"
               class="inline-flex w-fit items-center justify-center rounded-md bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">+ Ajukan Peminjaman</a>
        </div>
    </x-slot>

    <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-gradient-to-r from-slate-50 to-white text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
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
                            <p class="font-semibold text-slate-800">{{ $p->barang->nama }}</p>
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
                            @if ($p->catatan_admin)
                                <p class="mt-2 max-w-xs text-xs leading-relaxed text-slate-500">Catatan admin: {{ $p->catatan_admin }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if ($p->status === 'pending')
                                <form method="POST" action="{{ route('peminjaman.destroy', $p) }}"
                                      onsubmit="return confirm('Batalkan pengajuan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-100">Batalkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8">
                            <div class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Anda belum pernah mengajukan peminjaman.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $peminjamans->links() }}</div>
</x-app-layout>