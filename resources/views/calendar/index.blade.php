<x-app-layout bottom-nav-active="calendar">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">個人カレンダー</h1>
                <p class="text-xs text-slate-500 mt-0.5">休暇・予定（ガント逆算に反映）</p>
            </div>
            <div class="flex items-center gap-1 rounded-2xl bg-white/80 px-1 py-1 shadow-sm ring-1 ring-slate-200/60">
                <a href="{{ route('calendar.index', ['month' => $monthStart->copy()->subMonth()->format('Y-m')]) }}" class="rounded-xl px-3 py-1.5 text-sm font-bold text-brand-600 hover:bg-brand-50">←</a>
                <span class="px-2 text-sm font-bold text-slate-800 tabular-nums">{{ $monthStart->format('Y年n月') }}</span>
                <a href="{{ route('calendar.index', ['month' => $monthStart->copy()->addMonth()->format('Y-m')]) }}" class="rounded-xl px-3 py-1.5 text-sm font-bold text-brand-600 hover:bg-brand-50">→</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl bg-accent-50 text-accent-800 px-4 py-3 text-sm font-medium border border-accent-100">{{ session('status') }}</div>
        @endif

        <div class="flex flex-wrap gap-4 text-[11px] font-semibold text-slate-600">
            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-md bg-amber-200 ring-1 ring-amber-300/50"></span> 休み・予定</span>
            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-md bg-sky-300 ring-1 ring-sky-400/40"></span> タスク関連</span>
        </div>

        <div class="card-surface p-3">
            <div class="grid grid-cols-7 gap-1 text-center text-[11px]">
                @foreach (['日','月','火','水','木','金','土'] as $w)
                    <div class="py-2 font-bold text-slate-400">{{ $w }}</div>
                @endforeach
                @php
                    $first = $monthStart->copy()->startOfMonth();
                    $startWeekDay = $first->dayOfWeek;
                    $daysInMonth = $monthStart->daysInMonth;
                @endphp
                @for ($i = 0; $i < $startWeekDay; $i++)
                    <div></div>
                @endfor
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $date = $monthStart->copy()->day($d);
                        $key = $date->toDateString();
                        $daySchedules = $schedulesByDate->get($key, collect());
                        $hasTask = $daySchedules->contains(fn ($s) => $s->type === 'task_related');
                        $hasOther = $daySchedules->isNotEmpty() && ! $hasTask;
                    @endphp
                    <div class="min-h-[3.25rem] rounded-xl border p-1.5 transition
                        {{ $hasTask ? 'bg-sky-50 border-sky-200/60' : ($hasOther ? 'bg-amber-50/80 border-amber-200/50' : 'bg-white/60 border-slate-100') }}">
                        <span class="text-slate-900 font-bold text-sm">{{ $d }}</span>
                        @if ($daySchedules->isNotEmpty())
                            <div class="text-[10px] font-semibold text-slate-500 mt-0.5">{{ $daySchedules->count() }}件</div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <div class="card-surface p-4">
            <h2 class="text-sm font-bold text-slate-800 mb-3">この月の予定</h2>
            @php
                $flat = $schedulesByDate->flatten()->sortBy('on_date');
            @endphp
            @if ($flat->isEmpty())
                <p class="text-sm text-slate-500">予定はありません。</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($flat as $s)
                        <li class="py-3 flex items-center justify-between gap-2 text-sm">
                            <div>
                                <span class="font-bold text-slate-900">{{ $s->on_date->format('n/j') }}</span>
                                <span class="text-xs font-medium text-slate-400 ml-2">{{ $s->type }}</span>
                                @if ($s->title)
                                    <span class="block text-slate-600 text-xs mt-0.5">{{ $s->title }}</span>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('calendar.destroy', $s) }}" onsubmit="return confirm('削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-700">削除</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card-surface p-4 border-brand-100/50">
            <h2 class="text-sm font-bold text-slate-800 mb-3">予定を追加</h2>
            <form method="POST" action="{{ route('calendar.store') }}" class="space-y-3">
                @csrf
                <input type="date" name="on_date" required value="{{ old('on_date', now()->format('Y-m-d')) }}" class="input-field !text-sm" />
                <select name="type" class="input-field !text-sm">
                    <option value="off">休み</option>
                    <option value="personal">個人予定</option>
                    <option value="holiday">祝日相当</option>
                    <option value="task_related">タスク関連</option>
                </select>
                <input type="text" name="title" placeholder="メモ（任意）" value="{{ old('title') }}" class="input-field !text-sm" />
                <button type="submit" class="btn-primary w-full !py-2.5">予定を追加</button>
            </form>
        </div>
    </div>
</x-app-layout>
