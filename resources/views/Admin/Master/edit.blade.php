<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">Ubah {{ $judul }}</h1>
    </x-slot>

    <div class="grid items-start gap-6 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50/70 to-white px-6 py-5 sm:px-8">
                <h2 class="text-base font-semibold text-slate-800">Perbarui {{ strtolower($judul) }}</h2>
                <p class="mt-1 text-sm text-slate-500">Simpan perubahan setelah memeriksa kembali informasinya.</p>
            </div>

            <form method="POST" action="{{ route($prefix . '.update', $item->id) }}" class="space-y-5 p-6 sm:p-8">
                @csrf
                @method('PUT')

                @foreach ($fields as $name => $f)
                    <div>
                        <x-input-label :for="$name" :value="$f['label']" />
                        <x-text-input :id="$name" :name="$name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white"
                                      :value="old($name, $item->{$name})" :required="$f['required']" />
                        <x-input-error :messages="$errors->get($name)" class="mt-1" />
                    </div>
                @endforeach

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Simpan Perubahan</button>
                    <a href="{{ route($prefix . '.index') }}" class="rounded-xl px-4 py-3 text-center text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900">Batal</a>
                </div>
            </form>
        </div>

        <aside class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 p-6 text-white shadow-lg shadow-slate-900/10">
            <h2 class="text-base font-semibold">Perubahan data</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-300">Perubahan pada data {{ strtolower($judul) }} akan langsung digunakan saat admin mengelola inventaris.</p>
            <div class="mt-5 rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-sm font-semibold text-indigo-200">Sebelum menyimpan</p>
                <p class="mt-1 text-sm leading-relaxed text-slate-300">Pastikan ejaan dan informasi sudah benar agar data tetap konsisten.</p>
            </div>
        </aside>
    </div>
</x-app-layout>