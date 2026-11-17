<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">Profil Saya</h1>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="rounded-lg border border-gray-100 bg-white p-6 shadow-sm">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</x-app-layout>