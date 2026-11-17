<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\LaporanKerusakan;
use App\Models\Ruangan;
use App\Models\User;
use App\Notifications\AdminRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_damage_report_creates_a_database_notification_for_each_admin(): void
    {
        $admin = $this->createAdmin();
        $secondAdmin = $this->createAdmin();
        $reporter = User::factory()->create();
        $room = Ruangan::create(['nama' => 'Ruang Uji']);
        $category = KategoriBarang::create(['nama' => 'Elektronik Uji']);
        $item = Barang::create([
            'kode_barang' => 'TEST-002',
            'nama' => 'Monitor Uji',
            'kategori_barang_id' => $category->id,
            'ruangan_id' => $room->id,
            'kondisi' => 'baik',
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($reporter)->post(route('laporan.store'), [
            'barang_id' => $item->id,
            'ruangan_id' => $room->id,
            'deskripsi' => 'Layar monitor berkedip saat digunakan.',
        ]);

        $response->assertRedirect(route('laporan.index', absolute: false));
        $this->assertDatabaseHas('laporan_kerusakans', [
            'user_id' => $reporter->id,
            'barang_id' => $item->id,
            'status' => 'dilaporkan',
        ]);

        $notification = $admin->notifications()->firstOrFail();
        $secondNotification = $secondAdmin->notifications()->firstOrFail();
        $this->assertSame(AdminRequestSubmitted::class, $notification->type);
        $this->assertSame(AdminRequestSubmitted::RESOURCE_LAPORAN, $notification->data['resource']);
        $this->assertSame($notification->data, $secondNotification->data);
        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame(1, $secondAdmin->notifications()->count());
        $this->assertSame(0, $reporter->notifications()->count());
    }

    public function test_duplicate_damage_report_does_not_create_another_admin_notification(): void
    {
        $admin = $this->createAdmin();
        $reporter = User::factory()->create();
        $room = Ruangan::create(['nama' => 'Ruang Uji']);
        $category = KategoriBarang::create(['nama' => 'Elektronik Uji']);
        $item = Barang::create([
            'kode_barang' => 'TEST-003',
            'nama' => 'Printer Uji',
            'kategori_barang_id' => $category->id,
            'ruangan_id' => $room->id,
            'kondisi' => 'rusak',
            'jumlah' => 1,
        ]);
        LaporanKerusakan::create([
            'user_id' => $reporter->id,
            'barang_id' => $item->id,
            'ruangan_id' => $room->id,
            'deskripsi' => 'Laporan kerusakan yang masih diproses.',
            'status' => 'dilaporkan',
        ]);

        $response = $this->actingAs($reporter)->post(route('laporan.store'), [
            'barang_id' => $item->id,
            'ruangan_id' => $room->id,
            'deskripsi' => 'Laporan kerusakan duplikat.',
        ]);

        $response->assertSessionHasErrors('barang_id');
        $this->assertSame(0, $admin->notifications()->count());
        $this->assertDatabaseCount('laporan_kerusakans', 1);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }
}
