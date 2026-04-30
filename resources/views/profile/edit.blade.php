<x-app-layout bottom-nav-active="settings">
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">プロフィール</h2>
    </x-slot>

    <div class="space-y-6">
        <div class="card-surface p-5 sm:p-6">
            <div class="flex items-center gap-4 mb-6">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-100 to-brand-200 text-brand-800 text-xl font-bold shadow-sm ring-2 ring-white">{{ $user->displayInitial() }}</span>
                <div>
                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
            </div>
            @if ($joinedGroups->isNotEmpty())
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">参加中のグループ</h3>
                    <ul class="space-y-2">
                        @foreach ($joinedGroups as $g)
                            @php $role = $g->memberRole($user); @endphp
                            <li class="flex items-center justify-between text-sm">
                                <a href="{{ route('groups.show', $g) }}" class="font-medium text-brand-700 hover:text-brand-900 hover:underline">{{ $g->name }}</a>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg {{ $role === 'leader' ? 'bg-brand-100 text-brand-800' : 'bg-accent-100 text-accent-800' }}">
                                    {{ $role === 'leader' ? 'リーダー' : 'メンバー' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="card-surface p-5 sm:p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="card-surface p-5 sm:p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="card-surface p-5 sm:p-6 border-red-100/80">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
