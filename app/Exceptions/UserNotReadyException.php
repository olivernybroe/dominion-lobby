<?php

namespace App\Exceptions;

use App\Models\Lobby;
use App\Models\User;
use Exception;

class UserNotReadyException extends Exception
{
    /** @var User */
    protected $user;

    /**
     * UserNotReadyException constructor.
     * @param Lobby $lobby
     * @param User $user
     */
    public function __construct(Lobby $lobby, User $user)
    {
        parent::__construct($lobby);
        $this->user = $user;
    }

}
