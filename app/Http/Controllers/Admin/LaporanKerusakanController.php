<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\LaporanKerusakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKerusakanController extends Controller
{
    public function index(Request $request)
    {
        $laporans = LaporanKerusakan::with(['user.sekbid', 'barang', 'ruangan'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->q, fn($q, $v) => $q->where(fn($w) => $w
                ->whereHas('user', fn($u) => $u->where('name', 'like', "%{$v}%"))
                ->orWhereHas('barang', fn($b) => $b->where('nama', 'like', "%{$v}%"))))
            ->orderByRaw("FIELD(status, 'dilaporkan', 'diperbaiki', 'selesai')")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jumlah = LaporanKerusakan::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('Admin.Laporan.index', compact('laporans', 'jumlah'));
    }

    public function show(LaporanKerusakan $laporan)
    {
        $laporan->load(['user.sekbid', 'barang.ruangan', 'ruangan', 'penangan']);

        return view('Admin.Laporan.show', compact('laporan'));
    }

    public function updateStatus(Request $request, LaporanKerusakan $laporan)
    {
        $data = $request->validate([
            'status'        => ['required', 'in:dilaporkan,diperbaiki,selesai'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $error = DB::transaction(function () use ($data, $laporan, $request) {
            // Ambil ulang dengan kunci baris supaya status terbaru yang dicek
            $l = LaporanKerusakan::lockForUpdate()->findOrFail($laporan->id);

            if ($l->status === 'selesai') {
                return 'Laporan ini sudah selesai ditangani dan tidak bisa diubah lagi.';
            }

            $l->update($data + ['ditangani_oleh' => $request->user()->id]);

            $barang = Barang::lockForUpdate()->findOrFail($l->barang_id);

            // Masih ada laporan lain yang belum selesai untuk barang yang sama?
            $adaLaporanLain = LaporanKerusakan::where('barang_id', $barang->id)
                ->where('id', '!=', $l->id)
                ->where('status', '!=', 'selesai')
                ->exists();

            $barang->kondisi = match ($data['status']) {
                'dilaporkan' => 'rusak',
                'diperbaiki' => 'diperbaiki',
                'selesai'    => $adaLaporanLain ? 'rusak' : 'baik',
            };
            $barang->save();

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return redirect()->route('admin.laporan.show', $laporan)
            ->with('success', 'Status laporan berhasil diperbarui.');
    }
}
