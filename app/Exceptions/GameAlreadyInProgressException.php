<?php


namespace App\Exceptions;


use Exception;

class GameAlreadyInProgressException extends LobbyException
{
    protected $game;

    /**
     * GameAlreadyInProgressException constructor.
     * @param $game
     * @param $lobby
     */
    public function __construct($lobby, $game)
    {
        parent::__construct($lobby);
        $this->game = $game;
    }


}