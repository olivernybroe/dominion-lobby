<?php

namespace App\Models;

use App\Exceptions\GameAlreadyInProgressException;
use App\Exceptions\LobbyException;
use App\Exceptions\UserNotReadyException;
use App\Models\Pivot\LobbyGamePivot;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Lobby
 * @package App\Models
 * @property Game currentGame
 * @property \Illuminate\Database\Eloquent\Collection users
 * @property \Illuminate\Database\Eloquent\Collection players
 * @property \Illuminate\Database\Eloquent\Collection spectators
 */
class Lobby extends Model
{
    use HasUserstamps, HasTimestamps;

    protected $fillable = [
        'name'
    ];


    /**
     * @return BelongsTo|Game
     */
    public function currentGame() : BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class)->using(LobbyGamePivot::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'lobby_user')
            ->as('lobby')
            ->withPivot(['is_ready', 'is_spectator'])
            ->withTimestamps();
    }

    public function spectators()
    {
        return $this->users()->wherePivot('is_spectator', true);
    }

    public function players()
    {
        return $this->users()->wherePivot('is_spectator', false);
    }

    public function hasUser(User $user) : bool
    {
        return $this->users()->whereKey($user->getKey())->exists();
    }

    public function hasCurrentGame($throwException = false) : bool
    {
        if($this->currentGame()->ongoing()->exists()) {
            if(!$throwException) {
                return true;
            }
            throw new GameAlreadyInProgressException($this, $this->currentGame);
        }

        return false;
    }

    public function createGame($withCheck = true) : Game
    {
        if($withCheck) {
            $this->canStart(true);
        }

        /** @var Game $game */
        $game = Game::create([
            'ongoing' => true
        ]);
        $game->users()->saveMany($this->users);

        $this->games()->save($game);
        $this->currentGame()->associate($game)->save();

        return $game;
    }

    public function playersReady($throwException = false) : bool
    {
        if($throwException) {
            $this->players->filter(function (User $user) {
                return !$user->isReady($this);
            })->each(function (User $user) {
                throw new UserNotReadyException($this, $user);
            });
        }

        return $this->users()->get()->every(function (User $user) {
            return (bool) $user->lobby->is_ready;
        });
    }

    public function canStart($throwException = false) : bool
    {
        return !$this->hasCurrentGame($throwException) &&
            $this->playersReady($throwException);
    }

    public function deleteIfEmpty() : Lobby
    {
        if($this->users()->exists()) {
            return $this;
        }
        $this->delete();
        return $this;
    }

}
