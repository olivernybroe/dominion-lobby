<?php


namespace App\Exceptions;


use Exception;

class LobbyException extends Exception
{
    protected $lobby;

    /**
     * LobbyException constructor.
     * @param $lobby
     */
    public function __construct($lobby)
    {
        $this->lobby = $lobby;
    }


}