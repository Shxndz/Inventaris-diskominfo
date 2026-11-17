<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sekbid;
use Illuminate\Validation\Rule;

class SekbidController extends MasterController
{
    protected function model(): string  { return Sekbid::class; }
    protected function judul(): string  { return 'Sekbid'; }
    protected function prefix(): string { return 'admin.sekbid'; }

    protected function fields(): array
    {
        return ['nama' => ['label' => 'Nama Sekbid', 'required' => true]];
    }

    protected function rules(?int $id = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:150', Rule::unique('sekbids', 'nama')->ignore($id)],
        ];
    }
}