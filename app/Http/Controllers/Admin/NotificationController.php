<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use App\Notifications\AdminRequestSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->where('type', AdminRequestSubmitted::class)
            ->latest()
            ->paginate(15);

        return view('Admin.Notifications.index', compact('notifications'));
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        $adminNotification = $request->user()
            ->notifications()
            ->where('type', AdminRequestSubmitted::class)
            ->findOrFail($notification);

        $adminNotification->markAsRead();

        $destination = match ($adminNotification->data['resource'] ?? null) {
            AdminRequestSubmitted::RESOURCE_PEMINJAMAN => Peminjaman::query()
                ->whereKey($adminNotification->data['record_id'])
                ->exists()
                ? route('admin.peminjaman.show', $adminNotification->data['record_id'])
                : route('admin.peminjaman.index'),
            AdminRequestSubmitted::RESOURCE_LAPORAN => LaporanKerusakan::query()
                ->whereKey($adminNotification->data['record_id'])
                ->exists()
                ? route('admin.laporan.show', $adminNotification->data['record_id'])
                : route('admin.laporan.index'),
            default => route('admin.notifications.index'),
        };

        return redirect()->to($destination);
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->unreadNotifications()
            ->where('type', AdminRequestSubmitted::class)
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi sudah ditandai dibaca.');
    }
}
