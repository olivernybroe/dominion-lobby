<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Lobby;
use App\Rules\NoOngoingGame;
use App\Rules\PlayersReady;

class GameController extends Controller
{
    public function create(Lobby $lobby)
    {
        \Validator::validate([
            'lobby' => $lobby
        ], [
            'lobby' => [
                new NoOngoingGame(),
                new PlayersReady()
            ]
        ]);

        $lobby->createGame();

        return response(null, 201);
    }
}