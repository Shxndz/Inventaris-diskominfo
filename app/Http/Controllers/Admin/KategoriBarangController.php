<?php

namespace App\Http\Controllers\Admin;

use App\Models\KategoriBarang;
use Illuminate\Validation\Rule;

class KategoriBarangController extends MasterController
{
    protected function model(): string  { return KategoriBarang::class; }
    protected function judul(): string  { return 'Kategori Barang'; }
    protected function prefix(): string { return 'admin.kategori'; }

    protected function fields(): array
    {
        return ['nama' => ['label' => 'Nama Kategori', 'required' => true]];
    }

    protected function rules(?int $id = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kategori_barangs', 'nama')->ignore($id)],
        ];
    }
}