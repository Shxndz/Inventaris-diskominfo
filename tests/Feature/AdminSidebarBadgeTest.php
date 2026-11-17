<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_shows_counts_for_new_requests_only(): void
    {
        $admin = $this->createAdmin();
        $requester = User::factory()->create();
        $ruangan = Ruangan::create(['nama' => 'Ruang Uji']);
        $kategori = KategoriBarang::create(['nama' => 'Elektronik Uji']);
        $barang = Barang::create([
            'kode_barang' => 'TEST-001',
            'nama' => 'Laptop Uji',
            'kategori_barang_id' => $kategori->id,
            'ruangan_id' => $ruangan->id,
            'kondisi' => 'baik',
            'jumlah' => 2,
        ]);

        $this->createPeminjaman($requester, $barang, $ruangan, 'pending');
        $this->createPeminjaman($requester, $barang, $ruangan, 'pending');
        $this->createPeminjaman($requester, $barang, $ruangan, 'disetujui');
        $this->createLaporan($requester, $barang, $ruangan, 'dilaporkan');
        $this->createLaporan($requester, $barang, $ruangan, 'dilaporkan');
        $this->createLaporan($requester, $barang, $ruangan, 'diperbaiki');

        $response = $this->actingAs($admin)->get(route('admin.peminjaman.index'));

        $response->assertOk()
            ->assertSee('aria-label="2 pengajuan peminjaman baru"', false)
            ->assertSee('aria-label="2 laporan kerusakan baru"', false);
    }

    public function test_admin_sidebar_hides_badges_when_no_requests_need_review(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.peminjaman.index'));

        $response->assertOk()
            ->assertDontSee('pengajuan peminjaman baru', false)
            ->assertDontSee('laporan kerusakan baru', false);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }

    private function createPeminjaman(User $user, Barang $barang, Ruangan $ruangan, string $status): Peminjaman
    {
        return Peminjaman::create([
            'user_id' => $user->id,
            'barang_id' => $barang->id,
            'ruangan_id' => $ruangan->id,
            'jumlah' => 1,
            'tgl_pinjam' => now()->addDay(),
            'tgl_kembali' => now()->addDays(2),
            'keperluan' => 'Keperluan pengujian',
            'status' => $status,
        ]);
    }

    private function createLaporan(User $user, Barang $barang, Ruangan $ruangan, string $status): LaporanKerusakan
    {
        return LaporanKerusakan::create([
            'user_id' => $user->id,
            'barang_id' => $barang->id,
            'ruangan_id' => $ruangan->id,
            'deskripsi' => 'Laporan kerusakan untuk pengujian.',
            'status' => $status,
        ]);
    }
}
