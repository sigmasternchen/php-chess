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

    public function __toString(): string {
        return ["a", "b", "c", "d", "e", "f", "g", "h"][$this->file] . ($this->rank + 1);
    }
}