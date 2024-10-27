<?php

namespace Game;

enum Side {
    case WHITE;
    case BLACK;

    public function getNext(): Side {
        if ($this == Side::WHITE) {
            return Side::BLACK;
        } else {
            return Side::WHITE;
        }
    }

    public function getShort(): string {
        if ($this == Side::WHITE) {
            return "w";
        } else {
            return "b";
        }
    }

    public static function fromShort(string $short): Side {
        return match ($short) {
            "w" => self::WHITE,
            "b" => self::BLACK,
            default => throw new \InvalidArgumentException("unknown side: " . $short),
        };
    }
}