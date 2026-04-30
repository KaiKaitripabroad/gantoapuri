<nav x-data="{ open: false }" class="border-b border-white/40 bg-white/70 backdrop-blur-md">
    <div class="max-w-lg mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-14 items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold text-slate-800 tracking-tight">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">G</span>
                <span>{{ config('app.name', 'GroupTask') }}</span>
            </a>

            <div class="hidden sm:flex sm:items-center sm:gap-1">
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-white/80 hover:text-brand-700' }}">ホーム</a>
                <a href="{{ route('gantt.hub') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-white/80 hover:text-brand-700">ガント</a>
                <a href="{{ route('calendar.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-white/80 hover:text-brand-700">カレンダー</a>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-brand-100 bg-brand-50/80 py-1 pl-1 pr-3 text-sm font-medium text-brand-900 shadow-sm transition hover:bg-brand-100/80">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white text-xs font-semibold text-brand-700 shadow-sm">{{ Auth::user()->displayInitial() }}</span>
                            <span class="max-w-[7rem] truncate">{{ Auth::user()->name }}</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">プロフィール</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">ログアウト</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button type="button" @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-white/80 hover:text-slate-800 sm:hidden">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/50 bg-white/90 backdrop-blur-md">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">ホーム</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('gantt.hub')">ガント</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('calendar.index')">カレンダー</x-responsive-nav-link>
        </div>
        <div class="border-t border-slate-100 px-4 py-3">
            <div class="text-sm font-medium text-slate-800">{{ Auth::user()->name }}</div>
            <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
            <div class="mt-2 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">プロフィール</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">ログアウト</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
