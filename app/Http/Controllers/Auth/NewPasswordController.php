<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $credentials = $this->resetCredentials($request);
        $user = $credentials
            ? User::query()->where('email', $credentials['email'])->first()
            : null;

        if (! $credentials || ! $user || ! Password::broker()->tokenExists($user, $credentials['token'])) {
            $request->session()->forget('password_reset');

            return redirect()->route('password.request')
                ->withErrors(['email' => 'Verifikasi token reset password terlebih dahulu.']);
        }

        return view('auth.reset-password');
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $this->resetCredentials($request);

        if (! $credentials) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Verifikasi token reset password terlebih dahulu.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            [
                'email' => $credentials['email'],
                'token' => $credentials['token'],
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
            ],
            function (User $user) use ($request): void {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $request->session()->forget(['password_reset', 'password_reset_email']);

            return redirect()->route('login')->with('status', __($status));
        }

        $request->session()->forget(['password_reset', 'password_reset_email']);

        return redirect()->route('password.request')
            ->withErrors(['email' => __($status)]);
    }

    /**
     * @return array{email: string, token: string}|null
     */
    private function resetCredentials(Request $request): ?array
    {
        $credentials = $request->session()->get('password_reset');

        if (
            ! is_array($credentials)
            || ! is_string($credentials['email'] ?? null)
            || ! is_string($credentials['token'] ?? null)
        ) {
            return null;
        }

        return [
            'email' => $credentials['email'],
            'token' => $credentials['token'],
        ];
    }
}
