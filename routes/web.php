<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupTaskController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

Route::get('/dashboard', HomeController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/invitations/{token}', [InvitationController::class, 'show'])
    ->name('invitations.show');

Route::post('/invitations/{token}/decline', [InvitationController::class, 'decline'])
    ->name('invitations.decline');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])
        ->name('invitations.accept');

    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('/groups/{group}/invitations', [GroupController::class, 'invite'])->name('groups.invitations.store');

    Route::post('/groups/{group}/tasks', [GroupTaskController::class, 'store'])->name('groups.tasks.store');
    Route::put('/groups/{group}/tasks/{task}', [GroupTaskController::class, 'update'])->name('groups.tasks.update');
    Route::delete('/groups/{group}/tasks/{task}', [GroupTaskController::class, 'destroy'])->name('groups.tasks.destroy');

    Route::get('/gantt', function () {
        $group = auth()->user()->groups()->whereNull('groups.dissolved_at')->orderByDesc('groups.updated_at')->first();

        return $group
            ? redirect()->route('groups.show', ['group' => $group, 'tab' => 'gantt'])
            : redirect()->route('dashboard')->with('status', '表示するグループがありません。グループを作成または参加してください。');
    })->name('gantt.hub');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar', [CalendarController::class, 'store'])->name('calendar.store');
    Route::delete('/calendar/{schedule}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
