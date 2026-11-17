<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Lupa password?</h1>
        <p class="mt-1 text-sm text-gray-500">Masukkan email Anda untuk menerima token reset password.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-md bg-green-50 px-3 py-2 text-sm text-green-700" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <input id="email" name="email" type="email" value="{{ old('email', $passwordResetEmail) }}" required autofocus
                   class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Kirim Token
        </button>
    </form>

    @if ($passwordResetEmail)
        <div class="mt-6 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
            Masukkan token yang dikirim ke {{ $passwordResetEmail }}.
        </div>

        <form method="POST" action="{{ route('password.token.verify') }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <x-input-label for="token" value="Token Reset Password" />
                <input id="token" name="token" type="text" required autofocus autocomplete="off" spellcheck="false"
                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <x-input-error :messages="$errors->get('token')" class="mt-1" />
            </div>

            <button type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Verifikasi Token
            </button>
        </form>
    @endif

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-800">&larr; Kembali ke halaman masuk</a>
    </p>
</x-guest-layout>