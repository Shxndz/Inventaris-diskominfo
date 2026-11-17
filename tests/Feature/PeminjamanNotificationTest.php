<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use App\Notifications\AdminRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_loan_submission_creates_a_database_notification_for_each_admin(): void
    {
        $this->travelTo(now()->startOfDay());

        $firstAdmin = $this->createAdmin();
        $secondAdmin = $this->createAdmin();
        $borrower = User::factory()->create();
        $room = Ruangan::create(['nama' => 'Ruang Uji']);
        $category = KategoriBarang::create(['nama' => 'Elektronik Uji']);
        $item = Barang::create([
            'kode_barang' => 'TEST-001',
            'nama' => 'Laptop Uji',
            'kategori_barang_id' => $category->id,
            'ruangan_id' => $room->id,
            'kondisi' => 'baik',
            'jumlah' => 2,
        ]);

        $response = $this->actingAs($borrower)->post(route('peminjaman.store'), [
            'barang_id' => $item->id,
            'ruangan_id' => $room->id,
            'jumlah' => 1,
            'tgl_pinjam' => now()->addDay()->format('Y-m-d H:i:s'),
            'tgl_kembali' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'keperluan' => 'Keperluan pengujian',
        ]);

        $response->assertRedirect(route('peminjaman.index', absolute: false));
        $this->assertDatabaseHas('peminjamans', [
            'user_id' => $borrower->id,
            'barang_id' => $item->id,
            'status' => 'pending',
        ]);

        $firstNotification = $firstAdmin->notifications()->firstOrFail();
        $secondNotification = $secondAdmin->notifications()->firstOrFail();
        $this->assertSame(AdminRequestSubmitted::class, $firstNotification->type);
        $this->assertSame(AdminRequestSubmitted::RESOURCE_PEMINJAMAN, $firstNotification->data['resource']);
        $this->assertSame($firstNotification->data, $secondNotification->data);
        $this->assertSame(0, $borrower->notifications()->count());
        $this->assertSame(1, $firstAdmin->notifications()->count());
        $this->assertSame(1, $secondAdmin->notifications()->count());
    }

    public function test_invalid_loan_submission_does_not_create_admin_notifications(): void
    {
        $this->createAdmin();
        $borrower = User::factory()->create();

        $response = $this->actingAs($borrower)->post(route('peminjaman.store'), []);

        $response->assertSessionHasErrors([
            'barang_id',
            'ruangan_id',
            'jumlah',
            'tgl_pinjam',
            'tgl_kembali',
            'keperluan',
        ]);
        $this->assertDatabaseCount('peminjamans', 0);
        $this->assertDatabaseCount('notifications', 0);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }
}
