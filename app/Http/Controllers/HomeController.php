<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $groups = $user->groups()
            ->whereNull('groups.dissolved_at')
            ->with(['leader'])
            ->orderByDesc('groups.updated_at')
            ->get();

        $invitations = Invitation::query()
            ->where('email', $user->email)
            ->where('status', 'pending')
            ->with('group.leader')
            ->orderByDesc('created_at')
            ->get();

        return view('home', [
            'groups' => $groups,
            'invitations' => $invitations,
        ]);
    }
}
