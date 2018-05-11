<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\LobbyResource;
use App\Http\Resources\UserResource;
use App\Models\Lobby;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LobbyController extends Controller
{
    public function add(Request $request)
    {
        $lobby = Lobby::create([
            'name' => $request->get('name')
        ]);
        \Auth::user()->joinLobby($lobby);

        return LobbyResource::make($lobby);
    }

    public function all(Request $request)
    {
        return LobbyResource::collection(
            Lobby::whereHas('currentGame', function (Builder $query) {
                $query->finished();
            })->orWhereDoesntHave('currentGame')->paginate()
        );
    }

    public function get(Lobby $lobby)
    {
        return LobbyResource::make($lobby);
    }

    public function joinAsPlayer(Request $request, Lobby $lobby)
    {
        \Auth::user()->joinLobby($lobby);

        return response(null, 204);
    }

    public function joinAsSpectator(Request $request, Lobby $lobby)
    {
        \Auth::user()->joinLobbyAsSpectator($lobby);

        return response(null, 204);
    }

    public function leaveAsCurrentUser(Lobby $lobby)
    {
        \Auth::user()->leave($lobby);
        $lobby->deleteIfEmpty();

        return response(null, 204);
    }

    public function allPlayers(Lobby $lobby) {
        return UserResource::collection(
            $lobby->players
        );
    }
 }