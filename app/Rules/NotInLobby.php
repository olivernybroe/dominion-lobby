<?php

namespace App\Rules;

use App\Models\Lobby;
use Illuminate\Contracts\Validation\Rule;

class NotInLobby implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($value instanceof Lobby) {
            return !\Auth::user()->inLobby($value);
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
        return 'User is in the lobby.';
    }
}
