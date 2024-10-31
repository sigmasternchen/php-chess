<?php

namespace Engine;

use Game\Game;

interface Heuristic {
    public function ratePosition(Game $game): float;
}