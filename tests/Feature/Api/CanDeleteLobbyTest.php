<?php


namespace Tests\Feature\Api;


use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanDeleteLobbyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_delete_a_lobby_if_empty()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userA->joinLobby($lobby);

        $this->assertCount(1, Lobby::all());

        $response = $this->actingAs($userA)->deleteJson("api/lobbies/{$lobby->getKey()}/users/current");

        $response->assertStatus(204);
        $this->assertCount(0, Lobby::all());
    }

    /** @test */
    public function cannot_delete_a_lobby_if_not_empty()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $userA->joinLobby($lobby);
        $userB->joinLobby($lobby);

        $this->assertCount(1, Lobby::all());

        $response = $this->actingAs($userA)->deleteJson("api/lobbies/{$lobby->getKey()}/users/current");

        $response->assertStatus(204);
        $this->assertCount(1, Lobby::all());
    }

    /** @test */
    public function can_delete_a_lobby_if_game_is_ongoing_but_empty()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userA->joinLobby($lobby)->ready($lobby);
        $lobby->createGame();

        $this->assertCount(1, Lobby::all());

        $response = $this->actingAs($userA)->deleteJson("api/lobbies/{$lobby->getKey()}/users/current");

        $response->assertStatus(204);
        $this->assertCount(0, Lobby::all());
    }

}