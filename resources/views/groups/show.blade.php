@php
    $bn = $tab === 'gantt' ? 'gantt' : 'home';
@endphp
<x-app-layout :bottom-nav-active="$bn">
    <x-slot name="header">
        <div class="space-y-2">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $group->name }}</h1>
            <p class="text-sm text-slate-500">期日 <span class="font-medium text-slate-700">{{ $group->deadline->format('Y/m/d') }}</span></p>
            <div class="flex items-center gap-3 pt-1">
                <div class="flex-1 h-2.5 rounded-full bg-slate-100 overflow-hidden ring-1 ring-slate-200/60 max-w-xs">
                    <div class="h-full rounded-full bg-gradient-to-r from-accent-400 to-accent-500" style="width: {{ $progress }}%"></div>
                </div>
                <span class="text-xs font-bold text-brand-700 tabular-nums">{{ $progress }}%</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="rounded-2xl bg-accent-50 text-accent-800 px-4 py-3 text-sm font-medium border border-accent-100">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-2 items-center">
            @foreach ($group->members as $m)
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-sm font-bold text-white shadow-md
                    {{ $loop->index % 3 === 0 ? 'bg-gradient-to-br from-brand-500 to-brand-700' : ($loop->index % 3 === 1 ? 'bg-gradient-to-br from-sky-400 to-sky-600' : 'bg-gradient-to-br from-accent-400 to-accent-600') }}"
                    title="{{ $m->name }}">{{ $m->displayInitial() }}</span>
            @endforeach
            @can('invite', $group)
                <a href="{{ route('groups.show', ['group' => $group, 'tab' => 'settings']) }}" class="inline-flex h-10 px-3 items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 text-xs font-semibold text-slate-600 hover:border-brand-300 hover:bg-brand-50/50 hover:text-brand-700 transition">
                    + 招待
                </a>
            @endcan
        </div>

        <nav class="flex gap-1 p-1 rounded-2xl bg-slate-200/40 backdrop-blur-sm">
            <a href="{{ route('groups.show', ['group' => $group, 'tab' => 'details']) }}"
                class="flex-1 text-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ $tab === 'details' ? 'tab-pill-active' : 'tab-pill-idle' }}">詳細</a>
            <a href="{{ route('groups.show', ['group' => $group, 'tab' => 'gantt']) }}"
                class="flex-1 text-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ $tab === 'gantt' ? 'tab-pill-active' : 'tab-pill-idle' }}">ガント</a>
            <a href="{{ route('groups.show', ['group' => $group, 'tab' => 'settings']) }}"
                class="flex-1 text-center px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ $tab === 'settings' ? 'tab-pill-active' : 'tab-pill-idle' }}">設定</a>
        </nav>

        @if ($tab === 'details')
            <div class="space-y-4 pt-1">
                <ul class="space-y-3">
                    @foreach ($tasksOrdered as $task)
                        @php
                            $st = match ($task->status) {
                                'done' => ['完了', 'bg-accent-100 text-accent-800 ring-1 ring-accent-200/50'],
                                'in_progress' => ['進行', 'bg-sky-100 text-sky-800 ring-1 ring-sky-200/50'],
                                default => ['未着手', 'bg-slate-100 text-slate-700 ring-1 ring-slate-200/50'],
                            };
                        @endphp
                        <li class="card-surface p-4">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-semibold text-slate-900">{{ $task->title }}</span>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg {{ $st[1] }}">{{ $st[0] }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">
                                担当: {{ $task->assignee?->name ?? '未割当' }} ・ {{ $task->estimated_hours }}h
                                @if ($task->start_date && $task->end_date)
                                    ・ {{ $task->start_date->format('n/j') }}〜{{ $task->end_date->format('n/j') }}
                                @endif
                            </p>
                            <form method="POST" action="{{ route('groups.tasks.update', [$group, $task]) }}" class="mt-3 space-y-2 border-t border-slate-100 pt-3">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <input type="text" name="title" value="{{ $task->title }}" class="input-field !text-sm" />
                                    <select name="assignee_id" class="input-field !text-sm">
                                        <option value="">未割当</option>
                                        @foreach ($group->members as $m)
                                            <option value="{{ $m->id }}" @selected($task->assignee_id === $m->id)>{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" step="0.25" min="0.25" name="estimated_hours" value="{{ $task->estimated_hours }}" class="input-field !text-sm" />
                                    <select name="status" class="input-field !text-sm">
                                        <option value="not_started" @selected($task->status === 'not_started')>未着手</option>
                                        <option value="in_progress" @selected($task->status === 'in_progress')>進行</option>
                                        <option value="done" @selected($task->status === 'done')>完了</option>
                                    </select>
                                </div>
                                <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 transition">更新</button>
                            </form>
                            <form method="POST" action="{{ route('groups.tasks.destroy', [$group, $task]) }}" class="mt-2" onsubmit="return confirm('このタスクを削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">削除</button>
                            </form>
                        </li>
                    @endforeach
                </ul>

                @if (auth()->user()->can('create', [\App\Models\Task::class, $group]))
                    <div class="card-surface p-4 border-brand-100/80">
                        <h3 class="text-sm font-bold text-slate-800 mb-3">タスクを追加</h3>
                        <form method="POST" action="{{ route('groups.tasks.store', $group) }}" class="space-y-3">
                            @csrf
                            <input type="text" name="title" placeholder="タイトル" required value="{{ old('title') }}" class="input-field !text-sm" />
                            <div class="grid grid-cols-2 gap-2">
                                <select name="assignee_id" class="input-field !text-sm">
                                    <option value="">未割当</option>
                                    @foreach ($group->members as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="estimated_hours" step="0.25" min="0.25" value="1" class="input-field !text-sm" />
                            </div>
                            <select name="status" class="input-field !text-sm">
                                <option value="not_started">未着手</option>
                                <option value="in_progress">進行</option>
                                <option value="done">完了</option>
                            </select>
                            <button type="submit" class="btn-secondary w-full !py-2.5 border-accent-200 bg-accent-50/80 font-semibold text-accent-900 hover:bg-accent-100/80">+ タスクを追加</button>
                        </form>
                    </div>
                @endif
            </div>
        @elseif ($tab === 'gantt')
            <div class="card-surface p-4">
                @include('groups.partials.gantt', ['group' => $group, 'tasksOrdered' => $tasksOrdered])
            </div>
        @else
            <div class="space-y-4 pt-1">
                @can('invite', $group)
                    <div class="card-surface p-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-3">メンバー招待</h3>
                        <form method="POST" action="{{ route('groups.invitations.store', $group) }}" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input type="email" name="email" placeholder="メールアドレス" required class="input-field flex-1 !text-sm" />
                            <button type="submit" class="btn-primary !py-2.5 whitespace-nowrap">招待</button>
                        </form>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="card-surface p-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-3">招待一覧</h3>
                        @if ($group->invitations->isEmpty())
                            <p class="text-sm text-slate-500">保留中の招待はありません。</p>
                        @else
                            <ul class="divide-y divide-slate-100">
                                @foreach ($group->invitations as $inv)
                                    <li class="flex flex-wrap items-center justify-between gap-2 py-3 text-sm">
                                        <span class="font-medium text-slate-800">{{ $inv->email }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">招待済</span>
                                            <button type="button" class="text-brand-600 text-xs font-semibold hover:text-brand-800" data-copy="{{ url('/invitations/'.$inv->token) }}">リンクをコピー</button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endcan

                @can('dissolve', $group)
                    <form method="POST" action="{{ route('groups.destroy', $group) }}" class="rounded-2xl border-2 border-red-200 bg-red-50/80 p-4 backdrop-blur-sm" onsubmit="return confirm('グループを解散します。よろしいですか？');">
                        @csrf
                        @method('DELETE')
                        <p class="text-sm text-red-900 mb-3 leading-relaxed">リーダーのみ実行できます。解散後はメンバー全員がアクセスできなくなります。</p>
                        <button type="submit" class="w-full rounded-xl border-2 border-red-400 bg-white py-2.5 text-sm font-bold text-red-700 shadow-sm hover:bg-red-50 transition">グループを解散</button>
                    </form>
                @endcan
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-copy]').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const t = btn.getAttribute('data-copy');
                    try {
                        await navigator.clipboard.writeText(t);
                        btn.textContent = 'コピーしました';
                        setTimeout(() => { btn.textContent = 'リンクをコピー'; }, 2000);
                    } catch (e) {
                        prompt('リンクをコピーしてください:', t);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
