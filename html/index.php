<?php

require_once '../src/core.php';

use Game\Game;
use Game\Move;

session_start();

if (isset($_SESSION["game"])) {
    $game = $_SESSION["game"];
} else {
    $game = Game::fromStartPosition();
    $_SESSION["game"] = $game;
}

$content = function() use ($game) {
    require '../src/View/fragments/game.php';
};

if (isset($_GET["move"])) {
    $move = Move::fromJS($_REQUEST["move"]);
    $game->applyInPlace($move);

    $content();
} else {
    require '../src/View/base.php';
}

