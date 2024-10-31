<?php

require_once '../src/core.php';

use Game\Game;
use Game\Move;

session_start();

if (isset($_SESSION["game"])) {
    $game = $_SESSION["game"];
    $engine = $_SESSION["engine"];
} else {
    $game = Game::fromStartPosition();
    $engine = new \Engine\Random();

    $_SESSION["game"] = $game;
    $_SESSION["engine"] = $engine;
}

$content = function() use ($game) {
    require '../src/View/fragments/game.php';
};

if (isset($_GET["move"])) {
    $move = Move::fromJS($_REQUEST["move"]);
    $game->applyInPlace($move);

    $opponentMove = $engine->nextMove($game);
    $game->applyInPlace($opponentMove);

    $content();
} else {
    require '../src/View/base.php';
}

