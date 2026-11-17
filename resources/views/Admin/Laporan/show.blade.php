<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
            <a href="{{ route('admin.laporan.index') }}" class="text-sm text-slate-300 hover:text-white">&larr; Kembali</a>
            <h1 class="text-xl font-semibold text-white sm:text-2xl">Detail Laporan Kerusakan</h1>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium text-gray-900">Informasi Laporan</h2>
                    <x-status-badge :status="$laporan->status" />
                </div>

                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Barang</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->barang->nama }} ({{ $laporan->barang->kode_barang }})</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Kondisi barang saat ini</dt>
                        <dd><x-status-badge :status="$laporan->barang->kondisi" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Ruangan lokasi kerusakan</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->ruangan->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tanggal lapor</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->created_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Deskripsi kerusakan</dt>
                        <dd class="whitespace-pre-line text-gray-900">{{ $laporan->deskripsi }}</dd>
                    </div>
                    @if ($laporan->foto_url)
                    <div class="sm:col-span-2">
                        <dt class="mb-1 text-gray-500">Foto</dt>
                        <dd>
                            <a href="{{ $laporan->foto_url }}" target="_blank">
                                <img src="{{ $laporan->foto_url }}" alt="Foto kerusakan" class="max-h-64 rounded-md border border-gray-200 object-contain">
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if ($laporan->catatan_admin)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Catatan admin</dt>
                        <dd class="text-gray-900">{{ $laporan->catatan_admin }}</dd>
                    </div>
                    @endif
                    @if ($laporan->penangan)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Ditangani oleh</dt>
                        <dd class="text-gray-900">{{ $laporan->penangan->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm">
                <h2 class="mb-4 font-medium text-gray-900">Data Pelapor</h2>
                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">NIP</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->user->nip }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jabatan</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->user->jabatan }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Sekbid</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->user->sekbid->nama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">No. HP</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->user->no_hp }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-medium text-gray-900">{{ $laporan->user->email }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Ubah status --}}
        {{-- Ubah status --}}
        <div class="lg:col-span-1">
            @if ($laporan->status === 'selesai')
            <div class="space-y-3 rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm">
                <h2 class="font-medium text-green-900">Laporan Selesai</h2>
                <p class="text-sm leading-relaxed text-emerald-800">
                    Laporan ini sudah selesai ditangani pada
                    {{ $laporan->updated_at->translatedFormat('d F Y, H:i') }}
                    dan tidak bisa diubah lagi.
                </p>
                <p class="text-xs leading-relaxed text-emerald-700">
                    Jika barang rusak kembali, minta pegawai membuat laporan baru.
                </p>
            </div>
            @else
            <div class="rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm">
                <h2 class="mb-4 font-medium text-gray-900">Perbarui Status</h2>

                <form method="POST" action="{{ route('admin.laporan.status', $laporan) }}" class="space-y-4"
                    onsubmit="if (this.status.value === 'selesai') { return confirm('Setelah ditandai selesai, laporan tidak bisa diubah lagi. Lanjutkan?'); }">
                    @csrf @method('PATCH')

                    <div>
                        <x-input-label for="status" value="Status penanganan" />
                        <select id="status" name="status" required
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                            @foreach (['dilaporkan' => 'Dilaporkan (belum ditangani)', 'diperbaiki' => 'Sedang diperbaiki', 'selesai' => 'Selesai diperbaiki'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('status', $laporan->status) === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="catatan_admin" value="Catatan (opsional)" />
                        <textarea id="catatan_admin" name="catatan_admin" rows="3"
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                            placeholder="Contoh: Dikirim ke service center, estimasi 3 hari">{{ old('catatan_admin', $laporan->catatan_admin) }}</textarea>
                        <x-input-error :messages="$errors->get('catatan_admin')" class="mt-1" />
                    </div>

                    <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Simpan Status
                    </button>
                </form>

                <p class="mt-4 text-xs text-gray-500">Kondisi barang akan ikut diperbarui otomatis sesuai status.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>