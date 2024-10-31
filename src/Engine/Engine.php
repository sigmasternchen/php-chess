<?php

namespace Engine;

use Game\Game;
use Game\Move;

interface Engine {
    public function nextMove(Game $game): Move;
}