<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\Sekbid;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
{
    return view('auth.register', [
        'sekbids' => Sekbid::orderBy('nama')->get(),
    ]);
}

public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name'      => ['required', 'string', 'max:255'],
        'nip'       => ['required', 'digits:18', 'unique:users,nip'],
        'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
        'no_hp'     => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
        'jabatan'   => ['required', 'string', 'max:100'],
        'sekbid_id' => ['required', 'exists:sekbids,id'],
        'password'  => ['required', 'confirmed', Rules\Password::defaults()],
    ], [
        'nip.digits'  => 'NIP harus terdiri dari 18 digit angka.',
        'no_hp.regex' => 'Format nomor HP tidak valid (contoh: 081234567890).',
    ]);

    $user = User::create([
        'name'      => $request->name,
        'nip'       => $request->nip,
        'email'     => $request->email,
        'no_hp'     => $request->no_hp,
        'jabatan'   => $request->jabatan,
        'sekbid_id' => $request->sekbid_id,
        'password'  => Hash::make($request->password),
    ]);

    event(new Registered($user));
    Auth::login($user);

    return redirect(route('dashboard', absolute: false));
}
}
