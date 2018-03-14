<?php


namespace Tests\Feature\Api;


use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateLobbyTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([

        ], $overrides);
    }

    /** @test */
    public function guest_cannot_create_a_lobby() {
        $response = $this->postJson("/api/lobbies", $this->validParams());

        $response->assertStatus(401);
    }

    /** @test */
    public function users_can_create_a_lobby()
    {
        // Create a user
        $user = factory(User::class)->create();

        // Create lobby
        $response = $this->actingAs($user)->post("/api/lobbies", $this->validParams());

        // Check if lobby is created
        $response->assertStatus(201);

        tap(Lobby::first(), function (Lobby $lobby) use ($response, $user) {
            $this->assertModelIs($user, $lobby->creator);
            $this->assertModelIs($user, $lobby->updator);
            $this->assertFalse($lobby->hasCurrentGame());
            $this->assertTrue($lobby->games()->doesntExist());
            $this->assertTrue($user->inLobby($lobby));
            $this->assertFalse($user->isSpectator($lobby));
        });
    }


}