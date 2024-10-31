<?php

namespace Engine;

use Game\Game;
use Game\Move;

class Random implements Engine {
    public function nextMove(Game $game): Move {
        $legalMoves = $game->getLegalMoves();
        return $legalMoves[array_rand($legalMoves, 1)];
    }
}