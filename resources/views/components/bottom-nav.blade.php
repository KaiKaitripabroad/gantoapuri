@props(['active' => ''])

<nav {{ $attributes->merge(['class' => 'fixed bottom-0 inset-x-0 z-50 px-3 pb-3 pt-1 sm:hidden']) }}>
    <div class="mx-auto max-w-lg rounded-2xl border border-white/70 bg-white/85 backdrop-blur-lg shadow-nav">
        <div class="flex justify-around items-stretch h-14 text-[11px] font-medium">
            <a href="{{ route('dashboard') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 rounded-xl transition {{ $active === 'home' ? 'text-brand-600 bg-brand-50/80' : 'text-slate-500 hover:text-brand-600' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>ホーム</span>
            </a>
            <a href="{{ route('gantt.hub') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 rounded-xl transition {{ $active === 'gantt' ? 'text-brand-600 bg-brand-50/80' : 'text-slate-500 hover:text-brand-600' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>ガント</span>
            </a>
            <a href="{{ route('calendar.index') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 rounded-xl transition {{ $active === 'calendar' ? 'text-brand-600 bg-brand-50/80' : 'text-slate-500 hover:text-brand-600' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>カレンダー</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex flex-1 flex-col items-center justify-center gap-0.5 rounded-xl transition {{ $active === 'settings' ? 'text-brand-600 bg-brand-50/80' : 'text-slate-500 hover:text-brand-600' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>設定</span>
            </a>
        </div>
    </div>
</nav>
