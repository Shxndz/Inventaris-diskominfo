<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
        <p class="mt-1 text-sm text-gray-600">Perbarui data pribadi dan unit kerja Anda.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="nip" value="NIP" />
            <x-text-input id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" class="mt-1 block w-full" :value="old('nip', $user->nip)" required />
            <x-input-error :messages="$errors->get('nip')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="jabatan" value="Jabatan" />
                <x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full" :value="old('jabatan', $user->jabatan)" required />
                <x-input-error :messages="$errors->get('jabatan')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="sekbid_id" value="Sekbid" />
                <select id="sekbid_id" name="sekbid_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih Sekbid --</option>
                    @foreach ($sekbids as $sekbid)
                        <option value="{{ $sekbid->id }}" @selected(old('sekbid_id', $user->sekbid_id) == $sekbid->id)>{{ $sekbid->nama }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('sekbid_id')" class="mt-1" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="no_hp" value="No. HP" />
                <x-text-input id="no_hp" name="no_hp" type="tel" class="mt-1 block w-full" :value="old('no_hp', $user->no_hp)" required />
                <x-input-error :messages="$errors->get('no_hp')" class="mt-1" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm text-green-600">Perubahan tersimpan.</p>
            @endif
        </div>
    </form>
</section>