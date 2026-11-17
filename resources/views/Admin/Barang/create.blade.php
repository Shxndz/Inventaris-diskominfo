<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">Tambah Barang</h1>
    </x-slot>

    <div class="grid items-start gap-6 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50/70 to-white px-6 py-5 sm:px-8">
                <h2 class="text-base font-semibold text-slate-800">Detail barang</h2>
                <p class="mt-1 text-sm text-slate-500">Lengkapi informasi barang yang akan dikelola.</p>
            </div>

            <form method="POST" action="{{ route('admin.barang.store') }}" enctype="multipart/form-data" class="space-y-6 p-6 sm:p-8">
                @csrf
                @include('Admin.Barang.form')
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Simpan Barang</button>
                    <a href="{{ route('admin.barang.index') }}" class="rounded-xl px-4 py-3 text-center text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900">Batal</a>
                </div>
            </form>
        </div>

        <aside class="space-y-4">
            <section class="rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 p-6 text-white shadow-lg shadow-slate-900/10">
                <h2 class="text-base font-semibold">Panduan data barang</h2>
                <ul class="mt-4 space-y-3 text-sm leading-relaxed text-slate-300">
                    <li class="flex gap-3"><span class="font-semibold text-indigo-300">01</span><span>Gunakan kode barang yang unik dan mudah dicari.</span></li>
                    <li class="flex gap-3"><span class="font-semibold text-indigo-300">02</span><span>Pastikan jumlah dan kondisi sesuai inventaris.</span></li>
                    <li class="flex gap-3"><span class="font-semibold text-indigo-300">03</span><span>Foto membantu mengenali barang dengan cepat.</span></li>
                </ul>
            </section>
            <section class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-5">
                <p class="text-sm font-semibold text-indigo-900">Foto barang</p>
                <p class="mt-2 text-sm leading-relaxed text-indigo-800">Foto bersifat opsional. Ukuran file maksimal 2 MB.</p>
            </section>
        </aside>
    </div>
</x-app-layout>