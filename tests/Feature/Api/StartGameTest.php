<?php


namespace Tests\Feature\Api;


use App\Models\Game;
use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartGameTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([

        ], $overrides);
    }

    /** @test */
    public function can_create_game()
    {
        $this->withoutExceptionHandling();
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $userA->joinLobby($lobby)->ready($lobby);
        $userB->joinLobby($lobby)->ready($lobby);

        $this->assertTrue($lobby->hasUser($userA));
        $this->assertTrue($userA->isReady($lobby));
        $this->assertTrue($lobby->hasUser($userB));
        $this->assertTrue($userB->isReady($lobby));

        $response = $this->actingAs($userA)->postJson("api/lobbies/{$lobby->getKey()}/games/current", $this->validParams());

        $response->assertStatus(201);

        tap($lobby->refresh(), function (Lobby $lobby) use ($userA, $userB) {
            $game = $lobby->currentGame;

            $this->assertTrue($game->hasPlayer($userA));
            $this->assertTrue($game->hasPlayer($userB));
            $this->assertTrue($game->isOngoing());
            $this->assertTrue($lobby->hasCurrentGame());
        });
    }

    /** @test */
    public function cannot_create_game_if_lobby_has_a_game_running()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $userA->joinLobby($lobby)->ready($lobby);
        $userB->joinLobby($lobby)->ready($lobby);
        $lobby->createGame();

        $this->assertTrue($lobby->hasUser($userA));
        $this->assertTrue($lobby->hasUser($userB));
        $this->assertTrue($lobby->hasCurrentGame());

        $response = $this->actingAs($userA)->postJson("api/lobbies/{$lobby->getKey()}/games/current", $this->validParams());

        $response->assertStatus(422);

        $this->assertCount(1, Game::all());
        tap($lobby->refresh(), function (Lobby $lobby) use ($userA, $userB) {
            $game = $lobby->currentGame;

            $this->assertTrue($game->hasPlayer($userA));
            $this->assertTrue($game->hasPlayer($userB));
            $this->assertTrue($game->isOngoing());
            $this->assertTrue($lobby->hasCurrentGame());
        });
    }

    /** @test */
    public function cannot_create_game_if_players_are_not_ready()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $userA->joinLobby($lobby);
        $userB->joinLobby($lobby);

        $response = $this->actingAs($userA)->postJson("api/lobbies/{$lobby->getKey()}/games/current", $this->validParams());

        $response->assertStatus(422);

        $this->assertFalse($lobby->hasCurrentGame());
        $this->assertFalse($userA->isReady($lobby));
        $this->assertFalse($userB->isReady($lobby));
    }
}