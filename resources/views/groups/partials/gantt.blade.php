@php
    $withDates = $tasksOrdered->filter(fn ($t) => $t->start_date && $t->end_date);
    $startBound = $withDates->min('start_date') ?? $group->deadline->copy()->subWeeks(2);
    $endBound = $withDates->max('end_date') ?? $group->deadline;
    if ($endBound->lt($group->deadline)) {
        $endBound = $group->deadline;
    }
    if ($startBound->gt($endBound)) {
        $startBound = $group->deadline->copy()->subWeeks(1);
    }
    $days = [];
    for ($d = $startBound->copy()->startOfDay(); $d->lte($endBound); $d->addDay()) {
        $days[] = $d->copy();
    }
    $dayCount = count($days);
    if ($dayCount === 0) {
        $days = [$group->deadline->copy()];
        $dayCount = 1;
    }
@endphp

<div class="space-y-4">
    <p class="text-xs text-slate-600 leading-relaxed rounded-xl bg-slate-50/80 px-3 py-2 border border-slate-100">
        期日 <strong class="text-brand-700">{{ $group->deadline->format('Y/m/d') }}</strong> から逆算し、工数と担当者の休暇（土日・カレンダー）を考慮してバーを配置しています。
    </p>

    <div class="flex flex-wrap gap-4 text-[11px] font-semibold text-slate-600">
        <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-md bg-gradient-to-br from-accent-400 to-accent-500 shadow-sm"></span> 完了</span>
        <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-md bg-gradient-to-br from-sky-400 to-sky-500 shadow-sm"></span> 進行</span>
        <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-md bg-slate-300 shadow-sm"></span> 未着手</span>
    </div>

    @if ($tasksOrdered->isEmpty())
        <p class="text-sm text-slate-500">タスクがありません。「詳細」タブから追加してください。</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white/50 shadow-inner">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="bg-slate-100/90 text-slate-600">
                        <th class="text-left p-3 sticky left-0 bg-slate-100/95 backdrop-blur-sm z-10 min-w-[120px] font-bold">タスク</th>
                        @foreach ($days as $d)
                            <th class="p-1.5 font-semibold text-center whitespace-nowrap {{ $d->isSameDay($group->deadline) ? 'bg-brand-100 text-brand-800' : '' }}">
                                {{ $d->format('n/j') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasksOrdered as $task)
                        @php
                            $barClass = match ($task->status) {
                                'done' => 'bg-gradient-to-r from-accent-400 to-accent-500 shadow-sm',
                                'in_progress' => 'bg-gradient-to-r from-sky-400 to-sky-500 shadow-sm',
                                default => 'bg-slate-300',
                            };
                        @endphp
                        <tr class="border-t border-slate-100">
                            <td class="p-3 align-middle sticky left-0 bg-white/95 backdrop-blur-sm z-10 font-semibold text-slate-800">
                                {{ \Illuminate\Support\Str::limit($task->title, 18) }}
                            </td>
                            @foreach ($days as $i => $d)
                                @php
                                    $inRange = $task->start_date && $task->end_date
                                        && $d->between($task->start_date->startOfDay(), $task->end_date->startOfDay());
                                @endphp
                                <td class="p-0 h-9 border-l border-slate-100/80 {{ $inRange ? '' : 'bg-slate-50/40' }}">
                                    @if ($inRange)
                                        <div class="h-6 my-1.5 mx-0.5 rounded-md {{ $barClass }}"></div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
