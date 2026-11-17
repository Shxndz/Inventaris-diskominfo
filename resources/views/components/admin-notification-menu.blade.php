<div x-data="{ notificationsOpen: false }" @click.outside="notificationsOpen = false" class="relative shrink-0">
    <button type="button"
        @click.stop="notificationsOpen = !notificationsOpen"
        :aria-expanded="notificationsOpen.toString()"
        aria-label="Notifikasi admin"
        class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-100 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-4.5-5.8V4a1.5 1.5 0 0 0-3 0v1.2A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0v1a3 3 0 0 1-6 0v-1m6 0H9" />
        </svg>
        @if ($unreadCount > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full border-2 border-slate-900 bg-rose-500 px-1 text-[10px] font-bold text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="notificationsOpen" x-cloak
        class="absolute right-0 z-50 mt-3 w-[calc(100vw-2rem)] max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl shadow-slate-950/20 sm:w-96">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Notifikasi</h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    {{ $unreadCount > 0 ? $unreadCount . ' belum dibaca' : 'Semua sudah dibaca' }}
                </p>
            </div>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-indigo-700 hover:text-indigo-900">Tandai dibaca</button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse ($notifications as $notification)
                <form method="POST" action="{{ route('admin.notifications.open', $notification->id) }}">
                    @csrf
                    <button type="submit" class="flex w-full gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50 {{ is_null($notification->read_at) ? 'bg-indigo-50/50' : 'bg-white' }}">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ is_null($notification->read_at) ? 'bg-indigo-600' : 'bg-transparent' }}" aria-hidden="true"></span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-slate-800">{{ $notification->data['title'] }}</span>
                            <span class="mt-1 block text-xs leading-relaxed text-slate-600">{{ $notification->data['message'] }}</span>
                            <span class="mt-1.5 block text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                    </button>
                </form>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-500">Belum ada notifikasi baru.</p>
            @endforelse
        </div>

        <a href="{{ route('admin.notifications.index') }}" class="block bg-slate-50 px-4 py-3 text-center text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">
            Lihat semua notifikasi
        </a>
    </div>
</div>
