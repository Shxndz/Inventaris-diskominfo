<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset token request view.
     */
    public function create(Request $request): View
    {
        return view('auth.forgot-password', [
            'passwordResetEmail' => $request->session()->get('password_reset_email'),
        ]);
    }

    /**
     * Send a password reset token to the requested email address.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $request->session()->forget(['password_reset_email', 'password_reset']);

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return back()->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Pengiriman email belum dikonfigurasi. Atur MAIL_MAILER dan konfigurasi layanan email terlebih dahulu.',
                ]);
        }

        if (
            config('mail.default') === 'smtp'
            && (blank(config('mail.mailers.smtp.username')) || blank(config('mail.mailers.smtp.password')))
        ) {
            return back()->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Pengiriman email belum siap. Isi MAIL_USERNAME dan MAIL_PASSWORD pada konfigurasi SMTP.',
                ]);
        }

        $status = Password::sendResetLink(['email' => $data['email']]);

        if ($status === Password::RESET_LINK_SENT) {
            $request->session()->put('password_reset_email', $data['email']);

            return back()->with('status', 'Jika email terdaftar, token reset password telah dikirim.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    /**
     * Verify the token sent to the requested email address.
     */
    public function verifyToken(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! is_string($email)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Minta token reset password terlebih dahulu.']);
        }

        $data = $request->validate([
            'token' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Password::broker()->tokenExists($user, $data['token'])) {
            return back()->withErrors(['token' => 'Token tidak valid atau sudah kedaluwarsa.']);
        }

        $request->session()->put('password_reset', [
            'email' => $email,
            'token' => $data['token'],
        ]);
        $request->session()->forget('password_reset_email');

        return redirect()->route('password.reset');
    }
}
