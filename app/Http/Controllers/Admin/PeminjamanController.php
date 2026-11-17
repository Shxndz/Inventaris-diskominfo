<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $peminjamans = Peminjaman::with(['user.sekbid', 'barang', 'ruangan'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w
                ->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$v}%"))
                ->orWhereHas('barang', fn ($b) => $b->where('nama', 'like', "%{$v}%"))))
            ->orderByRaw("FIELD(status, 'pending', 'disetujui', 'ditolak', 'dikembalikan')")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jumlah = Peminjaman::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.peminjaman.index', compact('peminjamans', 'jumlah'));
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user.sekbid', 'barang.ruangan', 'ruangan', 'penyetuju']);

        $sisaStok = $peminjaman->status === 'pending'
            ? $peminjaman->barang->stokTersedia($peminjaman->tgl_pinjam, $peminjaman->tgl_kembali, $peminjaman->id)
            : null;

        return view('admin.peminjaman.show', compact('peminjaman', 'sisaStok'));
    }

    public function setujui(Request $request, Peminjaman $peminjaman)
    {
        $request->validate(['catatan_admin' => ['nullable', 'string', 'max:500']]);

        $error = DB::transaction(function () use ($request, $peminjaman) {
            $p = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);

            if ($p->status !== 'pending') {
                return 'Pengajuan ini sudah diproses sebelumnya.';
            }

            // Kunci baris barang agar dua persetujuan bersamaan tidak saling menimpa
            $barang = Barang::lockForUpdate()->findOrFail($p->barang_id);

            if ($barang->kondisi !== 'baik') {
                return 'Barang sedang tidak dalam kondisi baik, pengajuan tidak bisa disetujui.';
            }

            $sisa = $barang->stokTersedia($p->tgl_pinjam, $p->tgl_kembali, $p->id);
            if ($p->jumlah > $sisa) {
                return "Stok pada jadwal tersebut tidak mencukupi (tersedia: {$sisa}).";
            }

            $p->update([
                'status'         => 'disetujui',
                'catatan_admin'  => $request->catatan_admin,
                'disetujui_oleh' => $request->user()->id,
            ]);

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        return redirect()->route('admin.peminjaman.index')->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        $request->validate(
            ['catatan_admin' => ['required', 'string', 'max:500']],
            ['catatan_admin.required' => 'Alasan penolakan wajib diisi.']
        );

        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $peminjaman->update([
            'status'         => 'ditolak',
            'catatan_admin'  => $request->catatan_admin,
            'disetujui_oleh' => $request->user()->id,
        ]);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Pengajuan ditolak.');
    }

    public function kembalikan(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'disetujui') {
            return back()->with('error', 'Hanya peminjaman yang disetujui yang bisa ditandai dikembalikan.');
        }

        $peminjaman->update([
            'status'           => 'dikembalikan',
            'tgl_dikembalikan' => now(),
        ]);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Barang ditandai sudah dikembalikan.');
    }
}