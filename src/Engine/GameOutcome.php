<?php

namespace Engine;

use Game\Game;
use Game\GameState;

class GameOutcome implements Heuristic {
    public function ratePosition(Game $game): float {
        return match ($game->getGameState()) {
            GameState::CHECKMATE => -INF,
            default => 0
        };
    }
}