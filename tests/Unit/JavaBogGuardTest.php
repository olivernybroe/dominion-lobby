<?php


namespace Tests\Unit;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JavaBogGuardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_authenticate()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([

        ]);

        $response = $this->getJson("api/user",[
            'email' => $user->email,
            'password' => "1234"
        ]);
        dd($response);
        $response->dump();
    }

}