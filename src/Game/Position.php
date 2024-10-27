<?php

namespace Game;

class Position {
    public int $file;
    public int $rank;

    public function __construct($file, $rank) {
        $this->rank = $rank;
        $this->file = $file;
    }

    public function isValid(): bool {
        return
            $this->file >= 0 && $this->file < 8 &&
            $this->rank >= 0 && $this->rank < 8;
    }

    public function getSquareColor(): Side {
        return ($this->rank % 2) ^ ($this->file % 2) ? Side::WHITE : Side::BLACK;
    }

    public function getFileString(): string {
        return ["a", "b", "c", "d", "e", "f", "g", "h"][$this->file];
    }

    public function getRankString(): string {
        return strval($this->rank + 1);
    }

    public function __toString(): string {
        return $this->getFileString() . $this->getRankString();
    }

    public static function fromString(string $str): Position {
        $str = strtolower($str);
        $file = ord($str[0]) - ord("a");
        $rank = ord($str[1]) - ord("1");

        $position = new Position($file, $rank);
        if (!$position->isValid()) {
            throw new \InvalidArgumentException("position is not valid: " . $str);
        }
        return $position;
    }

    public function equals(Position $position): bool {
        return $this->file == $position->file && $this->rank == $position->rank;
    }
}