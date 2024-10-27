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

    public static function fromShort(string $short): PieceType {
        return match (strtoupper($short)) {
            "" => self::PAWN,
            "B" => self::BISHOP,
            "N" => self::KNIGHT,
            "R" => self::ROOK,
            "Q" => self::QUEEN,
            "K" => self::KING,
            default => throw new \InvalidArgumentException("unknown piece type: " . $short)
        };
    }

    public function getLong(): string {
        return match ($this) {
            self::PAWN => "Pawn",
            self::BISHOP => "Bishop",
            self::KNIGHT => "Knight",
            self::ROOK => "Rook",
            self::QUEEN => "Queen",
            self::KING => "King",
        };
    }
}