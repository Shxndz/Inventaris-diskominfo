<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="kode_barang" value="Kode Barang" />
        <x-text-input id="kode_barang" name="kode_barang" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white" :value="old('kode_barang', $barang->kode_barang)" required />
        <x-input-error :messages="$errors->get('kode_barang')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="nama" value="Nama Barang" />
        <x-text-input id="nama" name="nama" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white" :value="old('nama', $barang->nama)" required />
        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="kategori_barang_id" value="Kategori" />
        <select id="kategori_barang_id" name="kategori_barang_id" required
                class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            <option value="">-- Pilih kategori --</option>
            @foreach ($kategoris as $k)
                <option value="{{ $k->id }}" @selected(old('kategori_barang_id', $barang->kategori_barang_id) == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('kategori_barang_id')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="ruangan_id" value="Lokasi Penyimpanan (Ruangan)" />
        <select id="ruangan_id" name="ruangan_id"
                class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            <option value="">-- Tidak ditentukan --</option>
            @foreach ($ruangans as $r)
                <option value="{{ $r->id }}" @selected(old('ruangan_id', $barang->ruangan_id) == $r->id)>{{ $r->nama }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('ruangan_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="jumlah" value="Jumlah" />
        <x-text-input id="jumlah" name="jumlah" type="number" min="1" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:bg-white" :value="old('jumlah', $barang->jumlah)" required />
        <x-input-error :messages="$errors->get('jumlah')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="kondisi" value="Kondisi" />
        <select id="kondisi" name="kondisi" required
                class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 shadow-sm transition focus:border-indigo-500 focus:bg-white focus:ring-indigo-500">
            @foreach (['baik' => 'Baik', 'rusak' => 'Rusak', 'diperbaiki' => 'Sedang Diperbaiki'] as $v => $l)
                <option value="{{ $v }}" @selected(old('kondisi', $barang->kondisi) == $v)>{{ $l }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('kondisi')" class="mt-1" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="foto" value="Foto Barang (opsional, maks. 2 MB)" />
        @if ($barang->foto_url)
            <img src="{{ $barang->foto_url }}" alt="" class="mb-2 mt-1 h-24 w-24 rounded-xl object-cover ring-1 ring-slate-200">
        @endif
        <input id="foto" name="foto" type="file" accept="image/*"
               class="mt-1 block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 transition hover:file:bg-indigo-100">
        <x-input-error :messages="$errors->get('foto')" class="mt-1" />
    </div>
</div>