<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Notifications\AdminRequestSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_notification_page_renders_notifications_and_escapes_user_content(): void
    {
        $admin = $this->createAdmin();
        $admin->notify(new AdminRequestSubmitted(
            resource: AdminRequestSubmitted::RESOURCE_PEMINJAMAN,
            recordId: 25,
            title: 'Pengajuan peminjaman baru',
            message: '<script>alert(1)</script> mengajukan peminjaman Laptop.',
        ));

        $response = $this->actingAs($admin)->get(route('admin.notifications.index'));

        $response->assertOk()
            ->assertSee('Pengajuan peminjaman baru')
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('1 belum dibaca');
    }

    public function test_opening_a_loan_notification_marks_it_read_and_opens_the_existing_request(): void
    {
        $admin = $this->createAdmin();
        [$barang, $ruangan] = $this->createBarang();
        $peminjaman = Peminjaman::create([
            'user_id' => User::factory()->create()->id,
            'barang_id' => $barang->id,
            'ruangan_id' => $ruangan->id,
            'jumlah' => 1,
            'tgl_pinjam' => now()->addDay(),
            'tgl_kembali' => now()->addDays(2),
            'keperluan' => 'Keperluan pengujian',
            'status' => 'pending',
        ]);
        $notification = $this->createNotification(
            $admin,
            AdminRequestSubmitted::RESOURCE_PEMINJAMAN,
            $peminjaman->id,
        );

        $response = $this->actingAs($admin)->post(route('admin.notifications.open', $notification->id));

        $response->assertRedirect(route('admin.peminjaman.show', $peminjaman, absolute: false));
        $this->get(route('admin.peminjaman.show', $peminjaman))->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_opening_a_report_notification_marks_it_read_and_opens_the_existing_report(): void
    {
        $admin = $this->createAdmin();
        [$barang, $ruangan] = $this->createBarang();
        $laporan = LaporanKerusakan::create([
            'user_id' => User::factory()->create()->id,
            'barang_id' => $barang->id,
            'ruangan_id' => $ruangan->id,
            'deskripsi' => 'Deskripsi laporan untuk pengujian.',
            'status' => 'dilaporkan',
        ]);
        $notification = $this->createNotification(
            $admin,
            AdminRequestSubmitted::RESOURCE_LAPORAN,
            $laporan->id,
        );

        $response = $this->actingAs($admin)->post(route('admin.notifications.open', $notification->id));

        $response->assertRedirect(route('admin.laporan.show', $laporan, absolute: false));
        $this->get(route('admin.laporan.show', $laporan))->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_opening_a_stale_loan_notification_redirects_to_the_loan_list(): void
    {
        $admin = $this->createAdmin();
        $notification = $this->createNotification($admin, AdminRequestSubmitted::RESOURCE_PEMINJAMAN, 99999);

        $response = $this->actingAs($admin)->post(route('admin.notifications.open', $notification->id));

        $response->assertRedirect(route('admin.peminjaman.index', absolute: false));
        $this->get(route('admin.peminjaman.index'))->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_opening_a_stale_report_notification_redirects_to_the_report_list(): void
    {
        $admin = $this->createAdmin();
        $notification = $this->createNotification($admin, AdminRequestSubmitted::RESOURCE_LAPORAN, 99999);

        $response = $this->actingAs($admin)->post(route('admin.notifications.open', $notification->id));

        $response->assertRedirect(route('admin.laporan.index', absolute: false));
        $this->get(route('admin.laporan.index'))->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_cannot_open_another_admins_notification(): void
    {
        $notificationOwner = $this->createAdmin();
        $otherAdmin = $this->createAdmin();
        $notification = $this->createNotification($notificationOwner, AdminRequestSubmitted::RESOURCE_LAPORAN, 37);

        $response = $this->actingAs($otherAdmin)->post(route('admin.notifications.open', $notification->id));

        $response->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_non_admin_cannot_view_admin_notifications(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.notifications.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_mark_all_notifications_as_read(): void
    {
        $admin = $this->createAdmin();
        $this->createNotification($admin, AdminRequestSubmitted::RESOURCE_PEMINJAMAN, 25);
        $this->createNotification($admin, AdminRequestSubmitted::RESOURCE_LAPORAN, 37);

        $response = $this->actingAs($admin)
            ->from(route('admin.notifications.index', absolute: false))
            ->post(route('admin.notifications.read-all'));

        $response->assertRedirect(route('admin.notifications.index', absolute: false));
        $this->assertSame(0, $admin->unreadNotifications()->count());
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }

    private function createNotification(User $admin, string $resource, int $recordId): DatabaseNotification
    {
        $admin->notify(new AdminRequestSubmitted(
            resource: $resource,
            recordId: $recordId,
            title: 'Notifikasi uji',
            message: 'Aktivitas uji.',
        ));

        return $admin->notifications()->latest()->firstOrFail();
    }

    /**
     * @return array{Barang, Ruangan}
     */
    private function createBarang(): array
    {
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

        return [$barang, $ruangan];
    }
}
