<?php

require_once '../src/core.php';

use Engine\GameOutcome;
use Engine\MinimaxDF;
use Engine\PeSTO;
use Engine\PieceValues;
use Engine\WeightedHeuristics;
use Game\Game;
use Game\Move;

session_start();

if (isset($_SESSION["game"])) {
    $game = $_SESSION["game"];
    $engine = $_SESSION["engine"];
} else {
    $game = Game::fromStartPosition();
    $engine = new MinimaxDF(1, new WeightedHeuristics([
        [new GameOutcome(), 1.0],
        [new PieceValues(), 1.0],
        [new PeSTO(), 1.0],
    ]));

    $_SESSION["game"] = $game;
    $_SESSION["engine"] = $engine;
}

$move = null;
$interactive = true;

$content = function() use ($game, &$move, &$interactive) {
    require "../src/View/fragments/game.php";
};

if (isset($_GET["move"])) {
    $move = Move::fromJS($_REQUEST["move"]);

    if (isset($_GET["wait"])) {
        $opponentMove = $engine->nextMove($game);
        $game->applyInPlace($opponentMove);

        $content();
    } else {
        $game->applyInPlace($move);

        $interactive = false;
        $content();
    }

} else {
    require "../src/View/base.php";
}

