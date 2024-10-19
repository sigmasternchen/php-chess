<?php

use Game\Game;
use Game\Piece;
use Game\Position;use Game\Side;

$game ??= new Game([], \Game\Side::WHITE);

$current = Side::WHITE;

if (($current ?? Side::WHITE) == Side::WHITE) {
    $start = 7;
    $end = -1;
    $dir = -1;
} else {
    $start = 0;
    $end = 8;
    $dir = 1;
}

function getImageForPice(Piece $piece): string {
    return "/static/pieces/" .
        ($piece->getSide() == Side::WHITE ? "w" : "b") .
        $piece->getType()->getShort() .
        ".svg";
}

?>
<div class="board">
    <?php
        for($rank = $start; $rank != $end; $rank += $dir) {
            for($file = $start; $file != $end; $file += $dir) {
                $position = new Position($file, $rank);
                $piece = $game->getPiece($position);
                $moves = $piece ? $game->getMovesForPiece($piece) : [];
    ?>
            <div class="square <?= strtolower($position->getSquareColor()->name) ?> <?= count($moves) > 0 ? "hasMoves" : "" ?>">
                <?php
                    if ($piece) {
                ?>
                    <div class="piece <?= strtolower($piece->getSide()->name) ?>">
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
