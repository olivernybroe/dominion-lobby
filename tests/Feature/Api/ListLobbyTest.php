<?php


namespace Tests\Feature\Api;


use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ListLobbyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_list_all_lobbies()
    {
        $userA = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();
        $lobbyB = factory(Lobby::class)->create();
        $lobbyC = factory(Lobby::class)->create();

        $response = $this->actingAs($userA)->getJson("api/lobbies");

        $response->assertStatus(200);

        $responseData = $response->getOriginalContent();

        $this->assertCount(3, $responseData);
        $this->assertTrue($lobbyA->is($responseData->get(0)));
        $this->assertTrue($lobbyB->is($responseData->get(1)));
        $this->assertTrue($lobbyC->is($responseData->get(2)));
    }

    /** @test */
    public function can_list_all_lobbies_with_pagination()
    {
        $userA = factory(User::class)->create();
        factory(Lobby::class)->times(20)->create();

        // Chose page 2, should have 5 elements.
        $response = $this->actingAs($userA)->getJson("api/lobbies?page=2");

        $response->assertStatus(200);

        $responseData = $response->getOriginalContent();
        $this->assertCount(5, $responseData);
    }

    /** @test */
    public function can_list_only_non_started_lobbies()
    {
        $userA = factory(User::class)->create();

        // Won't be listed, as game i still running
        $lobbyA = factory(Lobby::class)->create();
        $lobbyA->createGame(false);

        // Will be listed, as lobby has no game.
        $lobbyB = factory(Lobby::class)->create();

        // Will be listed, as game is finished.
        $lobbyC = factory(Lobby::class)->create();
        $lobbyC->createGame(false)->finish();

        $response = $this->actingAs($userA)->getJson("api/lobbies");

        $response->assertStatus(200);

        $responseData = $response->getOriginalContent();

        $this->assertCount(2, $responseData);
        $this->assertTrue($lobbyB->is($responseData->get(0)));
        $this->assertTrue($lobbyC->is($responseData->get(1)));
    }

    /** @test */
    public function cannot_list_lobbies_if_guest()
    {
        factory(Lobby::class)->create();

        $response = $this->getJson("api/lobbies");

        $response->assertStatus(401);
    }

    /** @test */
    public function can_get_a_lobby()
    {
        $userA = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();
        $lobbyB = factory(Lobby::class)->create();
        $lobbyC = factory(Lobby::class)->create();

        $response = $this->actingAs($userA)->getJson("api/lobbies/{$lobbyA->getKey()}");

        $response->assertStatus(200);

        $responseData = $response->getOriginalContent();

        $this->assertTrue($lobbyA->is($responseData));
    }

    /** @test */
    public function cannot_get_a_lobby_if_invalid_id()
    {
        $userA = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();
        $lobbyB = factory(Lobby::class)->create();
        $lobbyC = factory(Lobby::class)->create();

        $response = $this->actingAs($userA)->getJson("api/lobbies/1000");

        $response->assertStatus(404);
    }

    /** @test */
    public function cannot_get_a_lobby_if_guest()
    {
        $userA = factory(User::class)->create();
        $lobbyA = factory(Lobby::class)->create();
        $lobbyB = factory(Lobby::class)->create();
        $lobbyC = factory(Lobby::class)->create();

        $response = $this->getJson("api/lobbies/{$lobbyA->getKey()}");

        $response->assertStatus(401);
    }
}