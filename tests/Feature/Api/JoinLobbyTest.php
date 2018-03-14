<?php


namespace Tests\Feature\Api;


use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JoinLobbyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_join()
    {
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();

        $response = $this->actingAs($userA)->postJson("/api/lobbies/{$lobbyA->getKey()}/users/players");

        $response->assertStatus(204);
        tap($lobbyA->refresh(), function (Lobby $lobbyA) use ($userA, $userB) {
           $this->assertTrue($lobbyA->hasUser($userA));
           $this->assertTrue($userA->isPlayer($lobbyA));
           $this->assertFalse($lobbyA->hasUser($userB));
        });
    }

    /** @test */
    public function cannot_join_if_guest()
    {
        $lobbyA = factory(Lobby::class)->create();

        $response = $this->postJson("/api/lobbies/{$lobbyA->getKey()}/users/players");

        $response->assertStatus(401);
    }

    /** @test */
    public function can_join_lobby_as_spectator()
    {
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();

        $response = $this->actingAs($userA)->postJson("/api/lobbies/{$lobbyA->getKey()}/users/spectators");

        $response->assertStatus(204);
        tap($lobbyA->refresh(), function (Lobby $lobbyA) use ($userA, $userB) {
            $this->assertTrue($lobbyA->hasUser($userA));
            $this->assertTrue($userA->isSpectator($lobbyA));
            $this->assertFalse($lobbyA->hasUser($userB));
        });
    }

    /** @test */
    public function can_change_to_spectator_if_joined_as_player()
    {
        $userA = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();
        $userA->joinLobby($lobbyA);

        $this->assertTrue($userA->isPlayer($lobbyA));
        $this->assertFalse($userA->isSpectator($lobbyA));


        $response = $this->actingAs($userA)->postJson("/api/lobbies/{$lobbyA->getKey()}/users/spectators");

        $response->assertStatus(204);
        $this->assertTrue($userA->isSpectator($lobbyA));
        $this->assertFalse($userA->isPlayer($lobbyA));
    }

    /** @test */
    public function can_change_to_player_if_joined_as_spectator()
    {
        $userA = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();
        $userA->joinLobbyAsSpectator($lobbyA);

        $this->assertFalse($userA->isPlayer($lobbyA));
        $this->assertTrue($userA->isSpectator($lobbyA));


        $response = $this->actingAs($userA)->postJson("/api/lobbies/{$lobbyA->getKey()}/users/players");

        $response->assertStatus(204);
        $this->assertFalse($userA->isSpectator($lobbyA));
        $this->assertTrue($userA->isPlayer($lobbyA));
    }
}