<x-guest-layout wide>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Buat akun baru</h1>
        <p class="mt-1 text-sm text-gray-500">Lengkapi data berikut. Semua kolom wajib diisi.</p>
    </div>

    @php
        $input = 'mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
    @endphp

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        {{-- Data pribadi --}}
        <fieldset class="space-y-4">
            <legend class="text-xs font-semibold uppercase tracking-wider text-gray-400">Data pribadi</legend>

            <div>
                <x-input-label for="name" value="Nama Lengkap" />
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="{{ $input }}">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="nip" value="NIP" />
                    <input id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" value="{{ old('nip') }}" required
                           placeholder="18 digit angka" class="{{ $input }}">
                    <x-input-error :messages="$errors->get('nip')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="no_hp" value="No. HP" />
                    <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" required
                           placeholder="08xxxxxxxxxx" autocomplete="tel" class="{{ $input }}">
                    <x-input-error :messages="$errors->get('no_hp')" class="mt-1" />
                </div>
            </div>
        </fieldset>

        {{-- Unit kerja --}}
        <fieldset class="space-y-4">
            <legend class="text-xs font-semibold uppercase tracking-wider text-gray-400">Unit kerja</legend>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="jabatan" value="Jabatan" />
                    <input id="jabatan" name="jabatan" type="text" value="{{ old('jabatan') }}" required
                           placeholder="Contoh: Staf Pelaksana" class="{{ $input }}">
                    <x-input-error :messages="$errors->get('jabatan')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="sekbid_id" value="Sekbid" />
                    <select id="sekbid_id" name="sekbid_id" required class="{{ $input }}">
                        <option value="">-- Pilih Sekbid --</option>
                        @foreach ($sekbids as $sekbid)
                            <option value="{{ $sekbid->id }}" @selected(old('sekbid_id') == $sekbid->id)>{{ $sekbid->nama }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sekbid_id')" class="mt-1" />
                </div>
            </div>
        </fieldset>

        {{-- Akun --}}
        <fieldset class="space-y-4">
            <legend class="text-xs font-semibold uppercase tracking-wider text-gray-400">Akun</legend>

            <div>
                <x-input-label for="email" value="Email" />
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                       placeholder="nama@email.com" class="{{ $input }}">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-password-input id="password" name="password" autocomplete="new-password" class="mt-1" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Ulangi Password" />
                    <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="mt-1" required />
                </div>
            </div>
            <p class="text-xs text-gray-500">Minimal 8 karakter.</p>
        </fieldset>

        <button type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-800">Masuk</a>
    </p>
</x-guest-layout>