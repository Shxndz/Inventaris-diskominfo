<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-white sm:text-2xl">Ajukan Peminjaman</h1>
            <p class="mt-1 text-sm text-slate-300">Lengkapi detail barang dan jadwal peminjaman.</p>
        </div>
    </x-slot>

    <div class="grid items-start gap-6 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 bg-gradient-to-r from-blue-50/70 to-white px-6 py-5 sm:px-8">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm3 10h3m-3 3h7" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Detail pengajuan</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Isi barang, lokasi, dan jadwal peminjaman.</p>
                    </div>
                </div>
            </div>

        <form method="POST" action="{{ route('peminjaman.store') }}" class="space-y-5 p-6 sm:p-8">
            @csrf

            <div>
                <x-input-label for="barang_id" value="Barang yang dipinjam" />
                <select id="barang_id" name="barang_id" required
                        class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                    <option value="">-- Pilih barang --</option>
                    @foreach ($barangs as $b)
                        <option value="{{ $b->id }}" @selected(old('barang_id') == $b->id)>
                            {{ $b->nama }} ({{ $b->kode_barang }}), stok {{ $b->jumlah }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('barang_id')" class="mt-1" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="ruangan_id" value="Dipakai di ruangan" />
                    <select id="ruangan_id" name="ruangan_id" required
                            class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                        <option value="">-- Pilih ruangan --</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" @selected(old('ruangan_id') == $r->id)>{{ $r->nama }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('ruangan_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="jumlah" value="Jumlah" />
                    <x-text-input id="jumlah" name="jumlah" type="number" min="1" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white" :value="old('jumlah', 1)" required />
                    <x-input-error :messages="$errors->get('jumlah')" class="mt-1" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="tgl_pinjam" value="Mulai dipakai" />
                    <x-text-input id="tgl_pinjam" name="tgl_pinjam" type="datetime-local" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white" :value="old('tgl_pinjam')" required />
                    <x-input-error :messages="$errors->get('tgl_pinjam')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="tgl_kembali" value="Dikembalikan" />
                    <x-text-input id="tgl_kembali" name="tgl_kembali" type="datetime-local" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white" :value="old('tgl_kembali')" required />
                    <x-input-error :messages="$errors->get('tgl_kembali')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="keperluan" value="Keperluan" />
                <textarea id="keperluan" name="keperluan" rows="3" required
                          class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                          placeholder="Contoh: Rapat koordinasi sekbid">{{ old('keperluan') }}</textarea>
                <x-input-error :messages="$errors->get('keperluan')" class="mt-1" />
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Kirim Pengajuan</button>
                <a href="{{ route('peminjaman.index') }}" class="rounded-xl px-4 py-3 text-center text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900">Batal</a>
            </div>
        </form>
        </div>

        <aside class="space-y-4">
            <section class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 p-6 text-white shadow-lg shadow-slate-900/10">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-blue-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </span>
                <h2 class="mt-4 text-base font-semibold">Agar pengajuan lancar</h2>
                <ul class="mt-4 space-y-3 text-sm leading-relaxed text-slate-300">
                    <li class="flex gap-3"><span class="font-semibold text-blue-300">01</span><span>Pastikan barang dan jumlah sesuai kebutuhan.</span></li>
                    <li class="flex gap-3"><span class="font-semibold text-blue-300">02</span><span>Periksa kembali jadwal mulai dan pengembalian.</span></li>
                    <li class="flex gap-3"><span class="font-semibold text-blue-300">03</span><span>Tulis keperluan peminjaman dengan jelas.</span></li>
                </ul>
            </section>
            <section class="rounded-2xl border border-blue-100 bg-blue-50/70 p-5">
                <p class="text-sm font-semibold text-blue-900">Informasi persetujuan</p>
                <p class="mt-2 text-sm leading-relaxed text-blue-800">Pengajuan akan ditinjau admin. Pantau perkembangannya melalui menu Peminjaman Saya.</p>
            </section>
        </aside>
    </div>
</x-app-layout>