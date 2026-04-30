<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Invitation;
use App\Services\BackwardScheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function __construct(
        protected BackwardScheduleService $backwardSchedule
    ) {}

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'deadline' => ['required', 'date'],
            'invite_emails' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        $emails = collect(preg_split('/[\s,;]+/', (string) ($validated['invite_emails'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn (string $e) => strtolower(trim($e)))
            ->filter(fn (string $e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->reject(fn (string $e) => $e === strtolower($user->email))
            ->values();

        $group = DB::transaction(function () use ($validated, $user, $emails) {
            $group = Group::create([
                'name' => $validated['name'],
                'deadline' => $validated['deadline'],
                'leader_id' => $user->id,
            ]);

            $group->members()->attach($user->id, [
                'role' => 'leader',
                'joined_at' => now(),
            ]);

            foreach ($emails as $email) {
                Invitation::create([
                    'group_id' => $group->id,
                    'email' => $email,
                    'token' => Invitation::generateToken(),
                    'status' => 'pending',
                    'invited_by' => $user->id,
                    'expires_at' => now()->addDays(14),
                ]);
            }

            return $group;
        });

        return redirect()
            ->route('groups.show', $group)
            ->with('status', 'グループを作成しました。');
    }

    public function show(Request $request, Group $group): View
    {
        $this->authorize('view', $group);

        $group->load([
            'leader',
            'members',
            'tasks.assignee',
            'invitations' => fn ($q) => $q->where('status', 'pending')->orderBy('email'),
        ]);

        $tab = $request->query('tab', 'details');
        if (! in_array($tab, ['details', 'gantt', 'settings'], true)) {
            $tab = 'details';
        }

        $tasksOrdered = $group->tasks()->with('assignee')->orderBy('sort_order')->orderBy('id')->get();

        return view('groups.show', [
            'group' => $group,
            'tab' => $tab,
            'tasksOrdered' => $tasksOrdered,
            'progress' => $group->progressPercent(),
        ]);
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('dissolve', $group);

        $group->dissolved_at = now();
        $group->save();

        return redirect()
            ->route('dashboard')
            ->with('status', 'グループを解散しました。');
    }

    public function invite(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('invite', $group);

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($validated['email']);

        if ($email === strtolower($request->user()->email)) {
            return back()->withErrors(['email' => '自分自身は招待できません。']);
        }

        if ($group->members()->whereRaw('LOWER(users.email) = ?', [$email])->exists()) {
            return back()->withErrors(['email' => '既にメンバーです。']);
        }

        $existing = Invitation::query()
            ->where('group_id', $group->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('status', '既に招待済みです。');
        }

        Invitation::create([
            'group_id' => $group->id,
            'email' => $email,
            'token' => Invitation::generateToken(),
            'status' => 'pending',
            'invited_by' => $request->user()->id,
            'expires_at' => now()->addDays(14),
        ]);

        return back()->with('status', '招待を送信しました（メール連携は未設定のため、招待リンクを共有してください）。');
    }
}
