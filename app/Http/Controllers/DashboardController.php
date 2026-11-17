<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return $user->isAdmin()
            ? view('dashboard', $this->dataAdmin())
            : view('dashboard', $this->dataUser($user->id));
    }

    private function dataAdmin(): array
    {
        $stats = [
            ['label' => 'Total Barang',           'value' => Barang::count(),
             'url' => route('admin.barang.index'), 'warna' => 'text-gray-900'],
            ['label' => 'Peminjaman Menunggu',    'value' => Peminjaman::where('status', 'pending')->count(),
             'url' => route('admin.peminjaman.index', ['status' => 'pending']), 'warna' => 'text-yellow-600'],
            ['label' => 'Laporan Kerusakan Baru', 'value' => LaporanKerusakan::where('status', 'dilaporkan')->count(),
             'url' => route('admin.laporan.index', ['status' => 'dilaporkan']), 'warna' => 'text-red-600'],
            ['label' => 'Sedang Diperbaiki',      'value' => LaporanKerusakan::where('status', 'diperbaiki')->count(),
             'url' => route('admin.laporan.index', ['status' => 'diperbaiki']), 'warna' => 'text-blue-600'],
        ];

        $pengajuanBaru = Peminjaman::with(['user.sekbid', 'barang'])
            ->where('status', 'pending')
            ->oldest()
            ->limit(5)
            ->get();

        // Sudah lewat batas, atau akan jatuh tempo dalam 24 jam
        $jatuhTempo = Peminjaman::with(['user', 'barang'])
            ->where('status', 'disetujui')
            ->where('tgl_kembali', '<=', now()->addDay())
            ->orderBy('tgl_kembali')
            ->limit(5)
            ->get();

        $laporanAktif = LaporanKerusakan::with(['barang', 'ruangan'])
            ->where('status', '!=', 'selesai')
            ->orderByRaw("FIELD(status, 'dilaporkan', 'diperbaiki')")
            ->latest()
            ->limit(5)
            ->get();

        // Grafik: jumlah pengajuan per bulan (6 bulan terakhir)
        $grafik = collect(range(5, 0))->map(function ($mundur) {
            $bulan = now()->startOfMonth()->subMonths($mundur);

            return [
                'label' => $bulan->translatedFormat('M'),
                'total' => Peminjaman::whereBetween('created_at', [$bulan, $bulan->copy()->endOfMonth()])->count(),
            ];
        });

        $kondisi = Barang::selectRaw('kondisi, count(*) as total')
            ->groupBy('kondisi')
            ->pluck('total', 'kondisi');

        return compact('stats', 'pengajuanBaru', 'jatuhTempo', 'laporanAktif', 'grafik', 'kondisi');
    }

    private function dataUser(int $userId): array
    {
        $stats = [
            ['label' => 'Pengajuan Menunggu',  'value' => Peminjaman::where('user_id', $userId)->where('status', 'pending')->count(),
             'url' => route('peminjaman.index'), 'warna' => 'text-yellow-600'],
            ['label' => 'Peminjaman Berjalan', 'value' => Peminjaman::where('user_id', $userId)->where('status', 'disetujui')->count(),
             'url' => route('peminjaman.index'), 'warna' => 'text-green-600'],
            ['label' => 'Laporan Diproses',    'value' => LaporanKerusakan::where('user_id', $userId)->where('status', '!=', 'selesai')->count(),
             'url' => route('laporan.index'), 'warna' => 'text-blue-600'],
        ];

        $berjalan = Peminjaman::with(['barang', 'ruangan'])
            ->where('user_id', $userId)
            ->where('status', 'disetujui')
            ->orderBy('tgl_kembali')
            ->limit(5)
            ->get();

        $pengajuanTerakhir = Peminjaman::with('barang')
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        $laporanAktif = LaporanKerusakan::with(['barang', 'ruangan'])
            ->where('user_id', $userId)
            ->where('status', '!=', 'selesai')
            ->latest()
            ->limit(5)
            ->get();

        return compact('stats', 'berjalan', 'pengajuanTerakhir', 'laporanAktif');
    }
}