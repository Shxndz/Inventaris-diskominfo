@props(['id', 'name', 'autocomplete' => 'current-password'])

<div x-data="{ show: false }" class="relative">
    <input :type="show ? 'text' : 'password'" id="{{ $id }}" name="{{ $name }}" autocomplete="{{ $autocomplete }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-300 pr-24 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}>
    <button type="button" @click="show = !show" tabindex="-1"
            class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-gray-500 hover:text-gray-800"
            x-text="show ? 'Sembunyikan' : 'Tampilkan'"></button>
</div>