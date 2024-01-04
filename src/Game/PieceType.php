<?php

namespace Game;

enum PieceType {
    case PAWN;
    case BISHOP;
    case KNIGHT;
    case ROOK;
    case QUEEN;
    case KING;

    public static function getPromotionPieces() {
        return [PieceType::BISHOP, PieceType::KNIGHT, PieceType::ROOK, PieceType::QUEEN];
    }

    public function getShort(): string {
        return match ($this) {
            self::PAWN => "",
            self::BISHOP => "B",
            self::KNIGHT => "N",
            self::ROOK => "R",
            self::QUEEN => "Q",
            self::KING => "K",
        };
    }
}