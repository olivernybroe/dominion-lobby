<?php

namespace App\Models;

use App\Models\Pivot\GameUserPivot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon finished_at
 */
class Game extends Model
{
    protected $fillable = [
        'finished_at'
    ];

    protected $dates = [
        'finished_at'
    ];

    public function users() {
        return $this->belongsToMany(User::class)->using(GameUserPivot::class);
    }

    public function hasPlayer(User $user)
    {
        return $this->users()->whereKey($user->getKey())->exists();
    }

    public function scopeFinished(Builder $query)
    {
        return $query->whereNotNull('finished_at');
    }

    public function finish() : Game
    {
        $this->finished_at = Carbon::now();
        $this->save();
        return $this;
    }

    public function scopeOngoing(Builder $query)
    {
        return $query->whereNull('finished_at');
    }

    public function isFinished() : bool
    {
        return $this->finished_at != null;
    }

    public function isOngoing() : bool
    {
        return $this->finished_at == null;
    }

}
