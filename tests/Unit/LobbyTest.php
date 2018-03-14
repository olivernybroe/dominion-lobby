<?php


namespace Tests\Unit;


use App\Models\Game;
use App\Models\Lobby;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LobbyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_all_games()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $lobby->games()->save($gameA = factory(Game::class)->create());
        $lobby->games()->save($gameB = factory(Game::class)->create());

        tap($lobby->refresh(), function (Lobby $lobby) {
            $this->assertEquals(2, $lobby->games()->count());
        });
    }

    /** @test */
    public function can_get_current_game()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $lobby->currentGame()->associate($game = factory(Game::class)->create())->save();

        tap($lobby->refresh(), function (Lobby $lobby) use ($game) {
            $this->assertModelIs($game, $lobby->currentGame);
        });
    }

    /** @test */
    public function can_check_if_user_in_lobby()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $this->assertCount(0, $lobby->users);
        $this->assertCount(0, $userA->lobbies);
        $this->assertCount(0, $userB->lobbies);

        $userA->joinLobby($lobby);

        tap($userA->refresh(), function (User $userA) use ($lobby) {
            $this->assertTrue($userA->inLobby($lobby));
        });
        tap($userB->refresh(), function (User $userB) use ($lobby) {
            $this->assertFalse($userB->inLobby($lobby));
        });
        tap($lobby->refresh(), function (Lobby $lobby) use ($userA, $userB) {
            $this->assertTrue($lobby->hasUser($userA));
            $this->assertFalse($lobby->hasUser($userB));
        });
    }

    /** @test */
    public function can_check_if_user_is_ready()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $userA->joinLobby($lobby);
        $userB->joinLobby($lobby);

        $this->assertFalse($userA->isReady($lobby));
        $this->assertFalse($userB->isReady($lobby));

        $userA->ready($lobby);

        $this->assertEquals(2, $lobby->users()->count());
        $this->assertTrue($userA->isReady($lobby));
        $this->assertFalse($userB->isReady($lobby));
    }

    /** @test */
    public function can_check_if_all_users_is_ready()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $userA->joinLobby($lobby);
        $userB->joinLobby($lobby);
        $userA->ready($lobby);

        $this->assertTrue($userA->isReady($lobby));
        $this->assertFalse($userB->isReady($lobby));
        $this->assertFalse($lobby->playersReady());

        $userB->ready($lobby);

        $this->assertTrue($userA->isReady($lobby));
        $this->assertTrue($userB->isReady($lobby));
        $this->assertTrue($lobby->playersReady());
    }

    /** @test */
    public function can_check_if_game_can_be_started()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $userA->joinLobby($lobby);
        $userB->joinLobby($lobby);
        $userA->ready($lobby);

        $this->assertTrue($userA->isReady($lobby));
        $this->assertFalse($userB->isReady($lobby));
        $this->assertFalse($lobby->canStart());

        $userB->ready($lobby);

        $this->assertTrue($userA->isReady($lobby));
        $this->assertTrue($userB->isReady($lobby));
        $this->assertTrue($lobby->canStart());

        $lobby->createGame(false);

        $this->assertFalse($lobby->canStart());
    }

    /** @test */
    public function can_check_if_user_joined_as_player()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $this->assertCount(0, $lobby->users);
        $this->assertCount(0, $userA->lobbies);
        $this->assertCount(0, $userB->lobbies);

        $userA->joinLobby($lobby);

        tap($userA->refresh(), function (User $userA) use ($lobby) {
            $this->assertTrue($userA->inLobby($lobby));
            $this->assertTrue($userA->isPlayer($lobby));
            $this->assertFalse($userA->isSpectator($lobby));
        });
        tap($userB->refresh(), function (User $userB) use ($lobby) {
            $this->assertFalse($userB->inLobby($lobby));
        });
        tap($lobby->refresh(), function (Lobby $lobby) use ($userA, $userB) {
            $this->assertTrue($lobby->hasUser($userA));
            $this->assertFalse($lobby->hasUser($userB));
        });
    }

    /** @test */
    public function can_check_if_user_joined_as_spectator()
    {
        /** @var Lobby $lobby */
        $lobby = factory(Lobby::class)->create();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $this->assertCount(0, $lobby->users);
        $this->assertCount(0, $userA->lobbies);
        $this->assertCount(0, $userB->lobbies);

        $userA->joinLobbyAsSpectator($lobby);

        tap($userA->refresh(), function (User $userA) use ($lobby) {
            $this->assertTrue($userA->inLobby($lobby));
            $this->assertTrue($userA->isSpectator($lobby));
            $this->assertFalse($userA->isPlayer($lobby));
        });
        tap($userB->refresh(), function (User $userB) use ($lobby) {
            $this->assertFalse($userB->inLobby($lobby));
        });
        tap($lobby->refresh(), function (Lobby $lobby) use ($userA, $userB) {
            $this->assertTrue($lobby->hasUser($userA));
            $this->assertFalse($lobby->hasUser($userB));
        });
    }
}