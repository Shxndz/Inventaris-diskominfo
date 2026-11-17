<?php

namespace App\Providers;

use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use App\Notifications\AdminRequestSubmitted;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Illuminate\Support\Facades\View as ViewFacade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $expiresInMinutes = config(
                'auth.passwords.' . config('auth.defaults.passwords') . '.expire',
            );

            return (new MailMessage)
                ->subject('Kode Reset Password')
                ->greeting('Halo, ' . $notifiable->name)
                ->line('Gunakan token berikut di halaman Lupa Password untuk melanjutkan:')
                ->line($token)
                ->line('Token ini berlaku selama ' . $expiresInMinutes . ' menit.')
                ->line('Jika Anda tidak meminta reset password, abaikan email ini.');
        });

        ViewFacade::composer('layouts.app', function (View $view): void {
            $user = auth()->user();

            if (! $user || ! $user->isAdmin()) {
                return;
            }

            $view->with([
                'adminPendingLoanCount' => Peminjaman::query()
                    ->where('status', 'pending')
                    ->count(),
                'adminNewReportCount' => LaporanKerusakan::query()
                    ->where('status', 'dilaporkan')
                    ->count(),
                'adminUnreadNotificationCount' => $user->unreadNotifications()
                    ->where('type', AdminRequestSubmitted::class)
                    ->count(),
                'adminRecentNotifications' => $user->notifications()
                    ->where('type', AdminRequestSubmitted::class)
                    ->latest()
                    ->limit(5)
                    ->get(),
            ]);
        });
    }
}
