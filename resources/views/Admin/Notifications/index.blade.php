<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-white sm:text-2xl">Notifikasi</h1>
            <p class="mt-1 text-sm text-slate-300">Pengajuan peminjaman dan laporan kerusakan terbaru.</p>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 bg-gradient-to-r from-indigo-50/70 to-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="font-semibold text-slate-800">Aktivitas terbaru</h2>
                <p class="mt-1 text-sm text-slate-500">Notifikasi tersimpan di akun admin.</p>
            </div>
            @if ($notifications->contains(fn ($notification) => is_null($notification->read_at)))
                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($notifications as $notification)
                <form method="POST" action="{{ route('admin.notifications.open', $notification->id) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-start gap-4 px-5 py-5 text-left transition hover:bg-slate-50 sm:px-6 {{ is_null($notification->read_at) ? 'bg-indigo-50/40' : 'bg-white' }}">
                        <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ is_null($notification->read_at) ? 'bg-indigo-600' : 'bg-slate-200' }}" aria-hidden="true"></span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <span class="font-semibold text-slate-800">{{ $notification->data['title'] }}</span>
                                <span class="text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                            <span class="mt-1 block text-sm leading-relaxed text-slate-600">{{ $notification->data['message'] }}</span>
                            <span class="mt-3 inline-flex rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">Buka detail</span>
                        </span>
                    </button>
                </form>
            @empty
                <div class="px-5 py-10 text-center sm:px-6">
                    <p class="font-semibold text-slate-700">Belum ada notifikasi</p>
                    <p class="mt-1 text-sm text-slate-500">Notifikasi akan muncul saat user mengajukan peminjaman atau melaporkan kerusakan.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if ($notifications->hasPages())
        <div class="mt-5">{{ $notifications->links() }}</div>
    @endif
</x-app-layout>
