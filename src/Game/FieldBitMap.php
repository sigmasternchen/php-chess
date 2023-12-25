<?php

namespace Game;

if (gettype(2147483648) == "double") {
    die("need 64 bit integers");
}

class FieldBitMap {
    private int $map = 0;

    public function __construct(array|int $argument) {
        if (gettype($argument) == "array") {
            foreach ($argument as $position) {
                $this->add($position);
            }
        } else {
            $this->map = $argument;
        }
    }

    public function getBitForPosition(Position $position): int {
        return $position->file * 8 + $position->rank;
    }

    public function add(Position $position): void {
        if ($position->isValid()) {
            $this->map |= 1 << $this->getBitForPosition($position);
        }
    }

    public function intersect(FieldBitMap $map): FieldBitMap {
        return new FieldBitMap($this->map & $map->map);
    }

    public function union(FieldBitMap $map): FieldBitMap {
        return new FieldBitMap($this->map | $map->map);
    }

    public function isEmpty(): bool {
        return $this->map == 0;
    }

    public function has(Position $position): bool {
        return ($this->map & (1 << $this->getBitForPosition($position))) != 0;
    }

    public function getPositions(): array {
        $result = [];

        for ($i = 0; $i < 64; $i++) {
            if ($this->map & (1 << $i)) {
                $result[] = new Position(floor($i / 8), $i % 8);
            }
        }

        return $result;
    }

    public function getMap(): int {
        return $this->map;
    }

    public static function full(): FieldBitMap {
        return new FieldBitMap(-1);
    }

    public static function empty(): FieldBitMap {
        return new FieldBitMap(0);
    }
}