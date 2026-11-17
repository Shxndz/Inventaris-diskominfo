<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barangs = Barang::with(['kategori', 'ruangan'])
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w
                ->where('nama', 'like', "%{$v}%")
                ->orWhere('kode_barang', 'like', "%{$v}%")))
            ->when($request->kategori, fn ($q, $v) => $q->where('kategori_barang_id', $v))
            ->when($request->ruangan, fn ($q, $v) => $q->where('ruangan_id', $v))
            ->when($request->kondisi, fn ($q, $v) => $q->where('kondisi', $v))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.barang.index', [
            'barangs'   => $barangs,
            'kategoris' => KategoriBarang::orderBy('nama')->get(),
            'ruangans'  => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.barang.create', $this->formData(new Barang(['kondisi' => 'baik', 'jumlah' => 1])));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        Barang::create($data);

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        return view('admin.barang.edit', $this->formData($barang));
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate($this->rules($barang->id));

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $barang->update($data);

        return redirect()->route('admin.barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->peminjamans()->exists() || $barang->laporanKerusakans()->exists()) {
            return back()->with('error', 'Barang tidak bisa dihapus karena sudah memiliki riwayat peminjaman atau laporan kerusakan.');
        }

        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }
        $barang->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }

    private function formData(Barang $barang): array
    {
        return [
            'barang'    => $barang,
            'kategoris' => KategoriBarang::orderBy('nama')->get(),
            'ruangans'  => Ruangan::orderBy('nama')->get(),
        ];
    }

    private function rules(?int $id = null): array
    {
        return [
            'kode_barang'        => ['required', 'string', 'max:50', Rule::unique('barangs', 'kode_barang')->ignore($id)],
            'nama'               => ['required', 'string', 'max:150'],
            'kategori_barang_id' => ['required', 'exists:kategori_barangs,id'],
            'ruangan_id'         => ['nullable', 'exists:ruangans,id'],
            'kondisi'            => ['required', 'in:baik,rusak,diperbaiki'],
            'jumlah'             => ['required', 'integer', 'min:1', 'max:9999'],
            'foto'               => ['nullable', 'image', 'max:2048'],
        ];
    }
}