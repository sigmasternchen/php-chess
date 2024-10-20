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
<div class="board <?= $interactive ? "interactive" : "" ?>" id="board<?= $boardId ?>">
    <?php
        for($rank = $start; $rank != $end; $rank += $dir) {
            for($file = $start; $file != $end; $file += $dir) {
                $position = new Position($file, $rank);
                $piece = $game->getPiece($position);
                $moves = $piece ? $game->getMovesForPiece($piece) : [];
                $hasMoves = count($moves) > 0;
                ?>
            <div class="square <?= strtolower($position->getSquareColor()->name) ?> <?= $hasMoves ? "hasMoves" : "" ?> <?= $position ?>">
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
<?php
if ($interactive) {
?>
<script>
window.addEventListener("load", () => {
    const board = document.getElementById("board<?= $boardId ?>");
    const getSquare = square => board.getElementsByClassName(square)[0];
    const clearSelection = () => {
        board.classList.remove("moveSelection");
        board.querySelectorAll(".source").forEach(element => {
            element.classList.remove("source")
        });
        board.querySelectorAll(".movePossible").forEach(element => {
            element.classList.remove("movePossible")
        });
    };
    const enterSelection = (moves) => {
        board.classList.add("moveSelection");
        for (const move of moves) {
            getSquare(move.target).classList.add("movePossible");
            getSquare(move.source).classList.add("source");
        }
    }
    const moveSelected = (move) => {
        // TODO
    };

    board.querySelectorAll(".piece.hasMoves").forEach(element => {
        element.addEventListener("click", event => {
            clearSelection();
            const moves = element.getAttribute("data-moves").split(";").map(move => ({
                encoded: move,
                source: move.split(",")[0].split("-")[2],
                target: move.split(",")[1]
            }));
            enterSelection(moves);
            event.stopPropagation();
        });
    });
    board.addEventListener("click", () => {
        clearSelection();
    });
});
</script>
<?php
}
