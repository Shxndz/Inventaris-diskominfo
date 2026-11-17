<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-white sm:text-2xl">Buat Laporan Kerusakan</h1>
            <p class="mt-1 text-sm text-slate-300">Jelaskan kondisi barang agar laporan dapat ditindaklanjuti.</p>
        </div>
    </x-slot>

    <div class="grid items-start gap-6 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 bg-gradient-to-r from-red-50/70 to-white px-6 py-5 sm:px-8">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Detail kerusakan</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Berikan informasi yang membantu admin menindaklanjuti.</p>
                    </div>
                </div>
            </div>

        <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-5 p-6 sm:p-8">
            @csrf

            <div>
                <x-input-label for="barang_id" value="Barang yang rusak" />
                <select id="barang_id" name="barang_id" required
                        class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
                    <option value="">-- Pilih barang --</option>
                    @foreach ($barangs as $b)
                        <option value="{{ $b->id }}" @selected(old('barang_id', request('barang_id')) == $b->id)>
                            {{ $b->nama }} ({{ $b->kode_barang }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('barang_id')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="ruangan_id" value="Lokasi barang (ruangan)" />
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
                <x-input-label for="deskripsi" value="Deskripsi kerusakan" />
                <textarea id="deskripsi" name="deskripsi" rows="4" required
                          class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500"
                          placeholder="Contoh: Lampu proyektor mati dan muncul bunyi berdecit saat dinyalakan">{{ old('deskripsi') }}</textarea>
                <x-input-error :messages="$errors->get('deskripsi')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="foto" value="Foto kerusakan (opsional, maks. 2 MB)" />
                <input id="foto" name="foto" type="file" accept="image/*"
                       class="mt-1 block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-red-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-red-700 transition hover:file:bg-red-100">
                <x-input-error :messages="$errors->get('foto')" class="mt-1" />
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Kirim Laporan</button>
                <a href="{{ route('laporan.index') }}" class="rounded-xl px-4 py-3 text-center text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900">Batal</a>
            </div>
        </form>
        </div>

        <aside class="space-y-4">
            <section class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-rose-950 p-6 text-white shadow-lg shadow-slate-900/10">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-red-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </span>
                <h2 class="mt-4 text-base font-semibold">Tips membuat laporan</h2>
                <ul class="mt-4 space-y-3 text-sm leading-relaxed text-slate-300">
                    <li class="flex gap-3"><span class="font-semibold text-red-300">01</span><span>Pilih barang dan lokasi yang tepat.</span></li>
                    <li class="flex gap-3"><span class="font-semibold text-red-300">02</span><span>Jelaskan gejala kerusakan secara spesifik.</span></li>
                    <li class="flex gap-3"><span class="font-semibold text-red-300">03</span><span>Tambahkan foto jika dapat membantu pemeriksaan.</span></li>
                </ul>
            </section>
            <section class="rounded-2xl border border-red-100 bg-red-50/70 p-5">
                <p class="text-sm font-semibold text-red-900">Catatan foto</p>
                <p class="mt-2 text-sm leading-relaxed text-red-800">Foto bersifat opsional dan ukuran file maksimal 2 MB.</p>
            </section>
        </aside>
    </div>
</x-app-layout>
