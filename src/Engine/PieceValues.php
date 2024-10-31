<?php

namespace Engine;

use Game\Game;
use Game\Piece;
use Game\PieceType;

class PieceValues implements Heuristic {
    private function valueOfPiece(Piece $piece): float {
        return match($piece->getType()) {
            PieceType::PAWN => 1,
            PieceType::BISHOP => 3,
            PieceType::KNIGHT => 3,
            PieceType::ROOK => 5,
            PieceType::QUEEN => 9,
            default => 0,
        };
    }

    public function ratePosition(Game $game): float {
        $ownPieces = $game->getPieces($game->getCurrentSide());
        $opponentPieces = $game->getPieces($game->getCurrentSide()->getNext());

        $ownValue = array_sum(array_map([$this, "valueOfPiece"], $ownPieces));
        $opponentValue = array_sum(array_map([$this, "valueOfPiece"], $opponentPieces));

        return $ownValue - $opponentValue;
    }
}