<?php

namespace App\Models;

use App\Exceptions\LobbyException;
use App\Exceptions\UserNotInLobbyException;
use App\Models\Pivot\LobbyUserPivot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'campusnet_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function lobbies()
    {
        return $this->belongsToMany(Lobby::class, 'lobby_user')
        ->as('user')
        ->withPivot(['is_ready', 'is_spectator'])
        ->withTimestamps();
    }

    public function joinLobby(Lobby &$lobby) : User
    {
        if($this->inLobby($lobby)) {
            $lobby = $this->lobbies()->updateExistingPivot($lobby->getKey(), ['is_spectator' => false]);
        }
        else {
            $lobby = $this->lobbies()->save($lobby, ['is_spectator' => false]);
        }
        return $this;
    }

    public function joinLobbyAsSpectator(Lobby &$lobby) : User
    {
        if($this->inLobby($lobby)) {
            $lobby = $this->lobbies()->updateExistingPivot($lobby->getKey(), ['is_spectator' => true]);

        }
        else {
            $lobby = $this->lobbies()->save($lobby, ['is_spectator' => true]);
        }
        return $this;
    }

    public function leave(Lobby $lobby) : User
    {
        if($lobby->users()->detach($this->getKey())) {
            return $this;
        }
        throw new UserNotInLobbyException($lobby);
    }

    public function inLobby(Lobby $lobby) : bool
    {
        return $this->lobbies()->whereKey($lobby->getKey())->exists();
    }

    public function isReady(Lobby $lobby) : bool
    {
        $lobby = $lobby->users()->whereKey($this->getKey())->first()->lobby;
        return $lobby->is_ready || $lobby->is_spectator;
    }

    public function isSpectator(Lobby $lobby) : bool
    {
        return $lobby->spectators()->whereKey($this->getKey())->exists();
    }

    public function isPlayer(Lobby $lobby) : bool
    {
        return $lobby->players()->whereKey($this->getKey())->exists();
    }

    public function ready(Lobby &$lobby) : User
    {
        $lobby->users()->updateExistingPivot($this->getKey(), ['is_ready' => true]);
        return $this;
    }

    public function scopeFromStudentId(Builder $query, string $id)
    {
        return $query->where('campusnet_id', $id);
    }

    public function findForPassport($username)
    {
        return $this->fromStudentId($username)->first();
    }
}
