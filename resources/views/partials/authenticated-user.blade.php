<div class="flex min-w-0 items-center gap-2 text-xs text-blue-100">
    <span class="h-2 w-2 shrink-0 rounded-full bg-[#10B981]"></span>
    <span class="truncate font-bold text-white" title="{{ auth()->user()->email }}">
        {{ auth()->user()->name }}
    </span>
</div>
<form method="POST" action="{{ route('logout') }}" class="shrink-0">
    @csrf
    <button
        type="submit"
        class="flex items-center gap-1 rounded px-2 py-1 text-xs text-white/80 transition-colors hover:bg-white/5 hover:text-red-300"
    >
        Logout
    </button>
</form>
