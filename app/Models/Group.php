<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'deadline',
        'leader_id',
        'dissolved_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'dissolved_at' => 'datetime',
        ];
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function isDissolved(): bool
    {
        return $this->dissolved_at !== null;
    }

    public function progressPercent(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }

        $done = $this->tasks()->where('status', 'done')->count();

        return (int) round(($done / $total) * 100);
    }

    public function memberRole(User $user): ?string
    {
        if ($this->leader_id === $user->id) {
            return 'leader';
        }

        $member = $this->members()->where('users.id', $user->id)->first();

        return $member?->pivot?->role;
    }
}
