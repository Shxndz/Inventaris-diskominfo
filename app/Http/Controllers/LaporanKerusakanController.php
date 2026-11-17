<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LaporanKerusakan;
use App\Models\Ruangan;
use App\Models\User;
use App\Notifications\AdminRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LaporanKerusakanController extends Controller
{
    public function index(Request $request)
    {
        $laporans = LaporanKerusakan::with(['barang', 'ruangan'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('Laporan.index', compact('laporans'));
    }

    public function create(Request $request)
    {
        return view('Laporan.create', [
            'barangs' => Barang::orderBy('nama')->get(),
            'ruangans' => Ruangan::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'ruangan_id' => ['required', 'exists:ruangans,id'],
            'deskripsi' => ['required', 'string', 'min:10', 'max:1000'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ], [
            'deskripsi.min' => 'Jelaskan kerusakan minimal 10 karakter agar mudah ditindaklanjuti.',
        ]);

        // Cegah laporan ganda untuk barang yang sama selama belum selesai
        $sudahAda = LaporanKerusakan::where('user_id', $request->user()->id)
            ->where('barang_id', $data['barang_id'])
            ->where('ruangan_id', $data['ruangan_id'])
            ->where('status', '!=', 'selesai')
            ->exists();

        if ($sudahAda) {
            throw ValidationException::withMessages([
                'barang_id' => 'Anda sudah melaporkan barang ini di ruangan tersebut dan laporannya masih diproses.',
            ]);
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        $barang = Barang::findOrFail($data['barang_id']);
        $laporan = LaporanKerusakan::create($data + [
            'user_id' => $request->user()->id,
            'status' => 'dilaporkan',
        ]);

        User::where('role', 'admin')->get()->each(
            fn (User $admin) => $admin->notify(new AdminRequestSubmitted(
                resource: AdminRequestSubmitted::RESOURCE_LAPORAN,
                recordId: $laporan->id,
                title: 'Laporan kerusakan baru',
                message: "{$request->user()->name} melaporkan kerusakan {$barang->nama}.",
            )),
        );

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dikirim. Admin akan segera menindaklanjuti.');
    }
}
