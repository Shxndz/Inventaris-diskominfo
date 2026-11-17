<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ruangan;

class RuanganController extends MasterController
{
    protected function model(): string  { return Ruangan::class; }
    protected function judul(): string  { return 'Ruangan'; }
    protected function prefix(): string { return 'admin.ruangan'; }

    protected function fields(): array
    {
        return [
            'nama'   => ['label' => 'Nama Ruangan',        'required' => true],
            'lokasi' => ['label' => 'Lokasi (mis. Lantai 2)', 'required' => false],
        ];
    }

    protected function rules(?int $id = null): array
    {
        return [
            'nama'   => ['required', 'string', 'max:150'],
            'lokasi' => ['nullable', 'string', 'max:150'],
        ];
    }
}