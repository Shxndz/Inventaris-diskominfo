<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
            <a href="{{ route('admin.peminjaman.index') }}" class="text-sm text-slate-300 hover:text-white">&larr; Kembali</a>
            <h1 class="text-xl font-semibold text-white sm:text-2xl">Detail Peminjaman</h1>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Detail --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-medium text-gray-900">Informasi Pengajuan</h2>
                    <x-status-badge :status="$peminjaman->status" />
                </div>

                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Barang</dt>
                        <dd class="font-medium text-gray-900">{{ $peminjaman->barang->nama }} ({{ $peminjaman->barang->kode_barang }})</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jumlah dipinjam</dt>
                        <dd class="font-medium text-gray-900">{{ $peminjaman->jumlah }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Dipakai di ruangan</dt>
                        <dd class="font-medium text-gray-900">{{ $peminjaman->ruangan->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Kondisi barang saat ini</dt>
                        <dd><x-status-badge :status="$peminjaman->barang->kondisi" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Mulai dipakai</dt>
                        <dd class="font-medium text-gray-900">{{ $peminjaman->tgl_pinjam->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Dikembalikan (rencana)</dt>
                        <dd class="font-medium text-gray-900">{{ $peminjaman->tgl_kembali->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                    @if ($peminjaman->tgl_dikembalikan)
                        <div>
                            <dt class="text-gray-500">Dikembalikan (aktual)</dt>
                            <dd class="font-medium text-gray-900">{{ $peminjaman->tgl_dikembalikan->translatedFormat('d F Y, H:i') }}</dd>
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Keperluan</dt>
                        <dd class="text-gray-900">{{ $peminjaman->keperluan }}</dd>
                    </div>
                    @if ($peminjaman->catatan_admin)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">Catatan admin</dt>
                            <dd class="text-gray-900">{{ $peminjaman->catatan_admin }}</dd>
                        </div>
                    @endif
                    @if ($peminjaman->penyetuju)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">Diproses oleh</dt>
                            <dd class="text-gray-900">{{ $peminjaman->penyetuju->name }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm">
                <h2 class="mb-4 font-medium text-gray-900">Data Peminjam</h2>
                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Nama</dt><dd class="font-medium text-gray-900">{{ $peminjaman->user->name }}</dd></div>
                    <div><dt class="text-gray-500">NIP</dt><dd class="font-medium text-gray-900">{{ $peminjaman->user->nip }}</dd></div>
                    <div><dt class="text-gray-500">Jabatan</dt><dd class="font-medium text-gray-900">{{ $peminjaman->user->jabatan }}</dd></div>
                    <div><dt class="text-gray-500">Sekbid</dt><dd class="font-medium text-gray-900">{{ $peminjaman->user->sekbid->nama ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">No. HP</dt><dd class="font-medium text-gray-900">{{ $peminjaman->user->no_hp }}</dd></div>
                    <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-900">{{ $peminjaman->user->email }}</dd></div>
                </dl>
            </div>
        </div>

        {{-- Aksi --}}
        <div class="lg:col-span-1">
            @if ($peminjaman->status === 'pending')
                <div class="space-y-5 rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm">
                    <h2 class="font-medium text-gray-900">Keputusan</h2>

                    <p class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        Stok tersedia pada jadwal ini:
                        <span class="font-semibold {{ $sisaStok >= $peminjaman->jumlah ? 'text-green-700' : 'text-red-700' }}">{{ $sisaStok }}</span>
                        (diminta {{ $peminjaman->jumlah }})
                    </p>

                    <form method="POST" action="{{ route('admin.peminjaman.setujui', $peminjaman) }}" class="space-y-3">
                        @csrf @method('PATCH')
                        <div>
                            <x-input-label for="catatan_setuju" value="Catatan (opsional)" />
                            <textarea id="catatan_setuju" name="catatan_admin" rows="2"
                                      class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                      placeholder="Contoh: Ambil di ruang Sekretariat"></textarea>
                        </div>
                        <button class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            Setujui
                        </button>
                    </form>

                    <hr class="border-slate-100">

                    <form method="POST" action="{{ route('admin.peminjaman.tolak', $peminjaman) }}" class="space-y-3">
                        @csrf @method('PATCH')
                        <div>
                            <x-input-label for="catatan_tolak" value="Alasan penolakan" />
                            <textarea id="catatan_tolak" name="catatan_admin" rows="2" required
                                      class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                                      placeholder="Wajib diisi agar pemohon tahu alasannya">{{ old('catatan_admin') }}</textarea>
                            <x-input-error :messages="$errors->get('catatan_admin')" class="mt-1" />
                        </div>
                        <button class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Tolak
                        </button>
                    </form>
                </div>

            @elseif ($peminjaman->status === 'disetujui')
                <div class="space-y-3 rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm">
                    <h2 class="font-medium text-gray-900">Pengembalian</h2>
                    <p class="text-sm text-gray-600">Klik tombol di bawah setelah barang diterima kembali dalam kondisi baik.</p>
                    <form method="POST" action="{{ route('admin.peminjaman.kembalikan', $peminjaman) }}"
                          onsubmit="return confirm('Tandai barang sudah dikembalikan?')">
                        @csrf @method('PATCH')
                        <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Tandai Sudah Dikembalikan
                        </button>
                    </form>
                    <p class="text-xs text-gray-500">Jika barang dikembalikan dalam keadaan rusak, minta pegawai membuat laporan kerusakan.</p>
                </div>

            @else
                <div class="rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white to-slate-50 p-6 text-sm text-slate-600 shadow-sm">
                    Pengajuan ini sudah selesai diproses.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>