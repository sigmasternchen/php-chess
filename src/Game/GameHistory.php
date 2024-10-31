<?php

namespace Game;

if (gettype(2147483648) == "double") {
    die("need 64 bit integers");
}

class GameHistory {
    private array $counts = [];
    private array $moves = [];

    private function hasCastlingRights(array &$rooks, King $king): bool {
        foreach ($rooks as $rook) {
            if ($king->canCastle(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty(), $rook)) {
                return true;
            }
        }

        return false;
    }

    private function getHashForPieces(array &$pieces): string {
        usort($pieces, function ($a, $b) {
            $ap = $a->getPosition();
            $bp = $b->getPosition();
            $fileComp = $ap->file <=> $bp->file;
            if ($fileComp == 0) {
                return $ap->rank <=> $bp->rank;
            } else {
                return $fileComp;
            }
        });

        return join("", array_map(function ($piece) {
            $pos = $piece->getPosition();
            $num = $pos->file * 8 + $pos->rank;
            if ($num < 26) {
                return chr(ord('a') + $num);
            } else if ($num < 26 + 26) {
                return chr(ord('A') + $num - 26);
            } else if ($num < 26 + 26 + 10) {
                return chr(ord('0') + $num - 26 - 26);
            } else { // 26 + 26 + 10 = 62 -> 2 left
                return ["-", "="][$num - 26 - 26 - 10];
            }
        }, $pieces));
    }

    private function getHashForSide(array &$pieces, King $king): string {
        $pawns = [];
        $bishops = [];
        $knights = [];
        $rooks = [];
        $queens = [];
        foreach ($pieces as $piece) {
            if ($piece instanceof Pawn) {
                $pawns[] = $piece;
            } else if ($piece instanceof Bishop) {
                $bishops[] = $piece;
            } else if ($piece instanceof Knight) {
                $knights[] = $piece;
            } else if ($piece instanceof Rook) {
                $rooks[] = $piece;
            } else if ($piece instanceof Queen) {
                $queens[] = $piece;
            }
        }

        $kings = [$king];

        return join(
            ",",
            array_map(
                [$this, "getHashForPieces"],
                [&$pawns, &$bishops, &$knights, &$rooks, &$queens, &$kings]
            )
        ) . ($this->hasCastlingRights($rooks, $king) ? "." : "");
    }

    private function getHash(array $whitePieces, array $blackPieces, King $whiteKing, King $blackKing) {
        return $this->getHashForSide($whitePieces, $whiteKing) .
            $this->getHashForSide($blackPieces, $blackKing);
    }

    private function getHashForGame(Game $game): string {
        return ($game->getCurrentSide() == Side::WHITE ? "*" : "+") .
            $this->getHash(
                $game->getPieces(Side::WHITE),
                $game->getPieces(Side::BLACK),
                $game->getKing(Side::WHITE),
                $game->getKing(Side::BLACK),
            );
    }

    public function count(Game $game): int {
        $hash = $this->getHashForGame($game);

        if (array_key_exists($hash, $this->counts)) {
            return $this->counts[$hash];
        } else {
            return 0;
        }
    }

    public function add(Game $game, ?Move $move = null): void {
        if ($move) {
            $this->moves[] = $move;
        }

        $hash = $this->getHashForGame($game);

        if (array_key_exists($hash, $this->counts)) {
            $this->counts[$hash]++;
        } else {
            $this->counts[$hash] = 1;
        }
    }

    public function getLastMove(): Move|null {
        if (count($this->moves) > 0) {
            return $this->moves[count($this->moves) - 1];
        } else {
            return null;
        }
    }
}