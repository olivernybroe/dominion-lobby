<?php


namespace Tests\Feature\Api;


use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUsersLobbyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_players()
    {
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();
        $userA->joinLobby($lobbyA);
        $userB->joinLobby($lobbyA);

        $response = $this->actingAs($userA)->getJson("/api/lobbies/{$lobbyA->getKey()}/users/players");

        $response->assertStatus(200);
        $data = $response->getOriginalContent();

        $this->assertModelIn($userA, $data);
        $this->assertModelIn($userB, $data);
    }
}