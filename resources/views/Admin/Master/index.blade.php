<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">Data {{ $judul }}</h1>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Form tambah --}}
        <div class="lg:col-span-1">
            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50/70 to-white px-5 py-4">
                    <h2 class="font-semibold text-slate-800">Tambah {{ $judul }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Masukkan data baru untuk daftar {{ strtolower($judul) }}.</p>
                </div>

                <form method="POST" action="{{ route($prefix . '.store') }}" class="space-y-4 p-5">
                    @csrf
                    @foreach ($fields as $name => $f)
                        <div>
                            <x-input-label :for="$name" :value="$f['label']" />
                            <x-text-input :id="$name" :name="$name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white"
                                          :value="old($name)" :required="$f['required']" />
                            <x-input-error :messages="$errors->get($name)" class="mt-1" />
                        </div>
                    @endforeach
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">Simpan Data</button>
                </form>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="lg:col-span-2">
            <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-gradient-to-r from-slate-50 to-white text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-4">#</th>
                            @foreach ($fields as $f)
                                <th class="whitespace-nowrap px-5 py-4">{{ $f['label'] }}</th>
                            @endforeach
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            <tr class="transition-colors hover:bg-slate-50/80">
                                <td class="px-5 py-4 text-slate-400">{{ $loop->iteration }}</td>
                                @foreach ($fields as $name => $f)
                                    <td class="px-5 py-4 font-medium text-slate-700">{{ $item->{$name} ?? '-' }}</td>
                                @endforeach
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route($prefix . '.edit', $item->id) }}"
                                       class="rounded-lg px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-colors hover:bg-indigo-50 hover:text-indigo-800">Ubah</a>
                                    <form method="POST" action="{{ route($prefix . '.destroy', $item->id) }}" class="ml-3 inline"
                                          onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50 hover:text-red-800">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($fields) + 2 }}" class="px-5 py-8">
                                    <div class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada data.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>