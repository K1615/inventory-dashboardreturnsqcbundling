<div class="p-4 border-t border-white/10 bg-black/10 flex items-center justify-between gap-3 text-sm font-semibold">
    <div class="flex min-w-0 items-center gap-2 text-xs text-blue-100" title="{{ auth()->user()->email }}">
        <span class="h-2 w-2 shrink-0 rounded-full {{ $indicatorClass ?? 'bg-[#10B981]' }}"></span>
        <span class="truncate">{{ auth()->user()->name }}</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="hover:text-red-300 text-white/80 transition-colors flex items-center gap-1 py-1 px-2 rounded hover:bg-white/5 text-xs">
            Logout
        </button>
    </form>
</div>
