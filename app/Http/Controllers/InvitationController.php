<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = Invitation::query()
            ->where('token', $token)
            ->with(['group.leader', 'group.members'])
            ->firstOrFail();

        if (! $invitation->isPending()) {
            abort(410, 'この招待は無効です。');
        }

        return view('invitations.show', [
            'invitation' => $invitation,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::query()
            ->where('token', $token)
            ->with('group')
            ->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect()->route('dashboard')->withErrors(['token' => '招待の有効期限が切れているか、既に処理済みです。']);
        }

        $user = $request->user();
        if (strtolower($user->email) !== strtolower($invitation->email)) {
            return redirect()
                ->route('invitations.show', $token)
                ->withErrors(['email' => 'ログイン中のアカウントのメールアドレスが招待先と一致しません。']);
        }

        $group = $invitation->group;

        if ($group->isDissolved()) {
            return redirect()->route('dashboard')->withErrors(['token' => 'このグループは解散済みです。']);
        }

        if (! $group->members()->where('users.id', $user->id)->exists()) {
            $group->members()->attach($user->id, [
                'role' => 'member',
                'joined_at' => now(),
            ]);
        }

        $invitation->update(['status' => 'accepted']);

        Invitation::query()
            ->where('group_id', $group->id)
            ->whereRaw('LOWER(email) = ?', [strtolower($invitation->email)])
            ->where('id', '!=', $invitation->id)
            ->where('status', 'pending')
            ->update(['status' => 'declined']);

        return redirect()
            ->route('groups.show', $group)
            ->with('status', 'グループに参加しました。');
    }

    public function decline(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::query()
            ->where('token', $token)
            ->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect()->route('dashboard');
        }

        if (Auth::check() && strtolower(Auth::user()->email) !== strtolower($invitation->email)) {
            return redirect()
                ->route('invitations.show', $token)
                ->withErrors(['email' => 'ログイン中のアカウントが招待先と一致しません。']);
        }

        $invitation->update(['status' => 'declined']);

        return redirect()
            ->route('dashboard')
            ->with('status', '招待を辞退しました。');
    }
}
