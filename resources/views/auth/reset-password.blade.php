<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Buat password baru</h1>
        <p class="mt-1 text-sm text-gray-500">Masukkan password baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="Password Baru" />
            <x-password-input id="password" name="password" autocomplete="new-password" class="mt-1" required autofocus />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Ulangi Password Baru" />
            <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="mt-1" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Simpan Password Baru
        </button>
    </form>
</x-guest-layout>