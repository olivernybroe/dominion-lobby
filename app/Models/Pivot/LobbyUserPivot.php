<?php


namespace App\Models\Pivot;


use Illuminate\Database\Eloquent\Relations\Pivot;

class LobbyUserPivot extends Pivot
{
    protected $table = 'lobby_user';

    protected $fillable = [
        'is_ready'
    ];
}