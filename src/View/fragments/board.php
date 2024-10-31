<?php

use Game\Game;
use Game\Piece;
use Game\Position;use Game\Side;

$game ??= new Game([], Side::WHITE);

if (($viewSide ?? Side::WHITE) == Side::WHITE) {
    $start = 7;
    $end = -1;
    $dir = -1;
} else {
    $start = 0;
    $end = 8;
    $dir = 1;
}

$interactive ??= false;

global $boardId;
$boardId = ($boardId ?? 0) + 1;

function getImageForPice(Piece $piece): string {
    return "/static/pieces/" .
        $piece->getSide()->getShort() .
        $piece->getType()->getShort() .
        ".svg";
}

?>
<div class="board <?= $interactive ? "interactive" : "" ?>" id="board<?= $boardId ?>"
    data-board="<?= $boardId ?>"
    <?php if ($interactive) { ?>
        data-hx-ext="board"
        data-hx-post="/?move"
        data-hx-trigger="chess-move-<?= $boardId ?> from:body"
        data-hx-swap="outerHTML"
        data-hx-target="this"
    <?php } ?>
>
    <?php
        for($rank = $start; $rank != $end; $rank += $dir) {
            for($file = 7-$start; $file != 7-$end; $file -= $dir) {
                $position = new Position($file, $rank);
                $piece = $game->getPiece($position);
                $moves = $piece ? $game->getMovesForPiece($piece) : [];
                $hasMoves = count($moves) > 0;
                ?>
            <div
                class="square <?= strtolower($position->getSquareColor()->name) ?> <?= $hasMoves ? "hasMoves" : "" ?> <?= $position ?>"
                data-square="<?= $position ?>"
            >
                <?php
                    if ($piece) {
                ?>
                    <div
                        class="piece <?= strtolower($piece->getSide()->name) ?> <?= $hasMoves ? "hasMoves" : "" ?>"
                        data-moves="<?= join(";", array_map(fn($m) => $m->toJS(), $moves)) ?>"
                    >
                        <img
                                alt="<?= strtolower($piece->getSide()->name) ?> <?= strtolower($piece->getType()->name) ?>"
                                src="<?= getImageForPice($piece) ?>"
                        />
                    </div>
                <?php
                    }
                ?>
            </div>
    <?php
            }
        }
    ?>
</div>
