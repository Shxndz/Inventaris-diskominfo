@props(['title', 'link' => null, 'linkText' => 'Lihat semua'])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md']) }}>
    <header class="flex items-center justify-between gap-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-5 py-4">
        <h2 class="border-l-2 border-indigo-500 pl-3 text-sm font-semibold text-slate-800">{{ $title }}</h2>
        @if ($link)
            <a href="{{ $link }}" class="shrink-0 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-colors hover:bg-indigo-100 hover:text-indigo-800">{{ $linkText }} &rarr;</a>
        @endif
    </header>
    <div>{{ $slot }}</div>
</section>