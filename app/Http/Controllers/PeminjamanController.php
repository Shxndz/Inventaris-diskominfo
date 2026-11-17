<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Notifications\AdminRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $peminjamans = Peminjaman::with(['barang', 'ruangan'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        return view('peminjaman.create', [
            'barangs'  => Barang::where('kondisi', 'baik')->orderBy('nama')->get(),
            'ruangans' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id'   => ['required', 'exists:barangs,id'],
            'ruangan_id'  => ['required', 'exists:ruangans,id'],
            'jumlah'      => ['required', 'integer', 'min:1'],
            'tgl_pinjam'  => ['required', 'date', 'after_or_equal:now'],
            'tgl_kembali' => ['required', 'date', 'after:tgl_pinjam'],
            'keperluan'   => ['required', 'string', 'max:500'],
        ], [
            'tgl_pinjam.after_or_equal' => 'Waktu pinjam tidak boleh di masa lalu.',
            'tgl_kembali.after'         => 'Waktu kembali harus setelah waktu pinjam.',
        ]);

        $barang = Barang::findOrFail($data['barang_id']);

        if ($barang->kondisi !== 'baik') {
            throw ValidationException::withMessages([
                'barang_id' => 'Barang ini sedang tidak dalam kondisi baik dan tidak bisa dipinjam.',
            ]);
        }

        $sisa = $barang->stokTersedia($data['tgl_pinjam'], $data['tgl_kembali']);
        if ($data['jumlah'] > $sisa) {
            throw ValidationException::withMessages([
                'jumlah' => "Stok pada jadwal tersebut tidak mencukupi (tersedia: {$sisa}).",
            ]);
        }

        $peminjaman = Peminjaman::create($data + [
            'user_id' => $request->user()->id,
            'status'  => 'pending',
        ]);

        User::where('role', 'admin')->get()->each(
            fn (User $admin) => $admin->notify(new AdminRequestSubmitted(
                resource: AdminRequestSubmitted::RESOURCE_PEMINJAMAN,
                recordId: $peminjaman->id,
                title: 'Pengajuan peminjaman baru',
                message: "{$request->user()->name} mengajukan peminjaman {$barang->nama}.",
            )),
        );

        return redirect()->route('peminjaman.index')
            ->with('success', 'Pengajuan berhasil dikirim. Silakan tunggu persetujuan admin.');
    }

    public function destroy(Request $request, Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === $request->user()->id, 403);

        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan yang masih menunggu yang bisa dibatalkan.');
        }

        $peminjaman->delete();

        return back()->with('success', 'Pengajuan dibatalkan.');
    }
}