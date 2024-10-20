<?php

require_once '../src/core.php';

use Game\Game;

$game = Game::fromStartPosition();

$content = function() use ($game) {
    require '../src/View/fragments/game.php';
};

require '../src/View/base.php';
