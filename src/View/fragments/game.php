<?php

use Game\Game;
use Game\Side;

$game ??= new Game([], Side::WHITE);

$viewSide ??= Side::WHITE;

$interactive = true;

require "board.php";