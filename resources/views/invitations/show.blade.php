@guest
<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">招待を受け取りました</h1>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-5 space-y-3 mb-6">
        <p class="font-bold text-lg text-slate-900">{{ $invitation->group->name }}</p>
        <p class="text-sm text-slate-600">リーダー: <span class="font-medium text-slate-800">{{ $invitation->group->leader->name }}</span></p>
        <p class="text-sm text-slate-600">期日: <span class="font-medium text-slate-800">{{ $invitation->group->deadline->format('Y/m/d') }}</span></p>
        <div class="flex flex-wrap gap-2 pt-2">
            @foreach ($invitation->group->members->take(8) as $m)
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white text-xs font-bold shadow-sm">{{ $m->displayInitial() }}</span>
            @endforeach
        </div>
        <p class="text-xs text-slate-500 pt-2 leading-relaxed">参加するとガントチャートとタスクが表示されます。</p>
    </div>
    <p class="text-sm text-slate-600 text-center mb-4">承認するにはログインまたは新規登録が必要です。</p>
    <div class="flex flex-col gap-3">
        <a href="{{ route('login') }}" class="btn-primary w-full">ログインして承認</a>
        <a href="{{ route('register') }}" class="btn-secondary w-full font-semibold">新規登録</a>
    </div>
    <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}" class="mt-6 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-red-600 transition">辞退する</button>
    </form>
</x-guest-layout>
@else
<x-app-layout bottom-nav-active="home">
    <x-slot name="header">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">招待の確認</h1>
    </x-slot>
    <div class="space-y-4">
        @if ($errors->any())
            <div class="rounded-2xl bg-red-50 text-red-800 px-4 py-3 text-sm font-medium border border-red-100">{{ $errors->first() }}</div>
        @endif
        <div class="card-surface p-5 space-y-2">
            <p class="font-bold text-lg text-slate-900">{{ $invitation->group->name }}</p>
            <p class="text-sm text-slate-600">リーダー: {{ $invitation->group->leader->name }}</p>
            <p class="text-sm text-slate-600">期日: {{ $invitation->group->deadline->format('Y/m/d') }}</p>
        </div>
        <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
            @csrf
            <button type="submit" class="btn-primary w-full">参加を承認する</button>
        </form>
        <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}">
            @csrf
            <button type="submit" class="w-full py-2 text-sm font-medium text-slate-500 hover:text-red-600">辞退する</button>
        </form>
    </div>
</x-app-layout>
@endguest
