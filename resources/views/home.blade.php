<x-app-layout bottom-nav-active="home">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">ホーム</h1>
                <p class="text-xs text-slate-500 mt-0.5">参加グループと招待</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-100 to-brand-200/80 text-brand-800 font-bold text-sm shadow-sm ring-2 ring-white">
                {{ auth()->user()->displayInitial() }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if (session('status'))
            <div class="rounded-2xl bg-accent-50 text-accent-800 px-4 py-3 text-sm font-medium border border-accent-100 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <section>
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">参加中のグループ</h2>
            @if ($groups->isEmpty())
                <div class="card-surface border-dashed border-slate-200 p-8 text-center">
                    <p class="text-slate-600 text-sm leading-relaxed">まだグループに参加していません。<br class="hidden sm:block" />下のボタンから作成するか、招待を承認してください。</p>
                </div>
            @else
                <ul class="space-y-3">
                    @foreach ($groups as $g)
                        @php
                            $role = $g->memberRole(auth()->user());
                            $progress = $g->progressPercent();
                        @endphp
                        <li>
                            <a href="{{ route('groups.show', $g) }}" class="card-surface block p-4 transition hover:shadow-card-hover hover:border-brand-100">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-semibold text-slate-900">{{ $g->name }}</span>
                                    <span class="shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-lg {{ $role === 'leader' ? 'bg-brand-100 text-brand-800' : 'bg-accent-100 text-accent-800' }}">
                                        {{ $role === 'leader' ? 'リーダー' : 'メンバー' }}
                                    </span>
                                </div>
                                <div class="mt-3 h-2 rounded-full bg-slate-100 overflow-hidden ring-1 ring-slate-200/50">
                                    <div class="h-full rounded-full bg-gradient-to-r from-accent-400 to-accent-500 shadow-sm" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">
                                    期日 {{ $g->deadline->format('n/j') }} ・ 進捗 <span class="font-semibold text-slate-700">{{ $progress }}%</span>
                                </p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section>
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">招待通知</h2>
            @if ($invitations->isEmpty())
                <p class="text-slate-500 text-sm">新しい招待はありません。</p>
            @else
                <ul class="space-y-3">
                    @foreach ($invitations as $inv)
                        <li class="card-surface p-4">
                            <p class="text-slate-900 font-semibold">「{{ $inv->group->name }}」への招待</p>
                            <p class="text-xs text-slate-500 mt-1">リーダー: {{ $inv->group->leader->name }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <a href="{{ route('invitations.show', $inv->token) }}" class="btn-primary !py-2 !px-4 !text-sm">
                                    詳細・承認
                                </a>
                                <form method="POST" action="{{ route('invitations.decline', $inv->token) }}">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-slate-500 hover:text-red-600 px-3 py-2 rounded-lg hover:bg-red-50 transition">辞退</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <a href="{{ route('groups.create') }}" class="btn-secondary w-full gap-2 border-accent-200 bg-gradient-to-r from-accent-50 to-white hover:from-accent-100/80 font-semibold text-accent-900">
            <span class="text-lg leading-none font-light">+</span>
            グループを作成
        </a>
    </div>
</x-app-layout>
