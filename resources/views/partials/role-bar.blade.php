{{-- Shared "logged in as" indicator + working logout, and JS globals for
     role-based UI gating. Include once, at the bottom of the sidebar
     (bottom-left) on each main page. --}}
<div class="p-4 border-t border-white/10 bg-black/10 flex items-center justify-between text-sm font-semibold shrink-0">
    <div class="truncate text-xs text-blue-100">
        <span class="w-2 h-2 rounded-full bg-[#10B981] inline-block mr-1.5 align-middle"></span>
        <span class="font-bold text-white align-middle">{{ \App\Support\Roles::currentName() }}</span>
        <span class="ml-1 px-1.5 py-0.5 rounded bg-white/10 text-blue-100 font-bold uppercase text-[10px] align-middle">{{ \App\Support\Roles::label() }}</span>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="hover:text-red-300 text-white/80 transition-colors flex items-center gap-1 py-1 px-2 rounded hover:bg-white/5 text-xs font-semibold">
            Logout
        </button>
    </form>
</div>

<script>
    // Global role context for front-end button gating.
    // The server enforces every permission independently — this is only
    // used to disable/gray-out buttons the current role can't use.
    window.APP_ROLE = @json(\App\Support\Roles::current());
    window.APP_USER_NAME = @json(\App\Support\Roles::currentName());
    window.APP_PERMISSIONS = @json(config('roles.permissions'));

    window.canDo = function (permission) {
        if (!window.APP_ROLE) return false;
        const allowed = window.APP_PERMISSIONS[permission];
        // Unlisted permission => open to any logged-in role.
        if (!allowed) return true;
        return allowed.includes(window.APP_ROLE);
    };

    // Takes a <button ...>Label</button> HTML string built via a JS template
    // literal and, if the current role can't do `permission`, injects a
    // disabled attribute + tooltip + grayed-out class into it.
    // The server-side "permission" middleware is what actually blocks the
    // request either way — this only affects what the button looks like.
    window.gatedBtn = function (html, permission) {
        if (window.canDo(permission)) return html;
        return html
            .replace('<button ', '<button disabled title="Requires Manager access" ')
            .replace(/class="/, 'class="opacity-40 cursor-not-allowed pointer-events-none ');
    };

    // Applies disabled styling + a tooltip to an element when the current
    // role lacks `permission`. Call after you build/insert the element.
    window.applyPermissionGate = function (el, permission) {
        if (!el) return;
        if (!window.canDo(permission)) {
            el.disabled = true;
            el.classList.add('opacity-40', 'cursor-not-allowed');
            el.classList.remove('hover:bg-emeraldGreen/90', 'hover:bg-red-600', 'hover:bg-blue-800', 'hover:bg-blue-700');
            el.title = 'Requires Manager access';
            el.onclick = null;
        }
    };
</script>
