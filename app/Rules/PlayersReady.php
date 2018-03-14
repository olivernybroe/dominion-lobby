<?php


namespace App\Rules;


use App\Models\Lobby;
use Illuminate\Contracts\Validation\Rule;

class PlayersReady implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed|Lobby $value
     * @return bool
     * @throws \App\Exceptions\LobbyException
     */
    public function passes($attribute, $value)
    {
        if($value instanceof Lobby) {
            return $value->playersReady();
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "All players aren't ready in the lobby.";
    }
}