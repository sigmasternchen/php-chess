<?php

namespace Game;

class Pawn extends Piece {
    public function getName(): string {
        return "Pawn";
    }

    public function getShort(): string {
        return "";
    }

    public function getMoveCandidateMap(FieldBitMap $occupied, FieldBitMap $captureable, FieldBitMap $threatened): FieldBitMap {
        $result = FieldBitMap::empty();

        $direction = $this->side == Side::WHITE ? 1 : -1;

        $rank = $this->position->rank;
        $file = $this->position->file;

        $regular = new Position($file, $rank + 1 * $direction);
        $capture1 = new Position($file - 1, $rank + 1 * $direction);
        $capture2 = new Position($file + 1, $rank + 1 * $direction);

        $initial = new Position($file, $rank + 2 * $direction);

        // checking for collisions in the captureable map works because there is always
        // a pawn before the en passant square.
        if (!$occupied->has($regular) && !$captureable->has($regular)) {
            $result->add($regular);

            if (!$this->hasMoved && !$occupied->has($initial) && !$captureable->has($initial)) {
                $result->add($initial);
            }
        }
        if ($captureable->has($capture1)) {
            $result->add($capture1);
        }
        if ($captureable->has($capture2)) {
            $result->add($capture2);
        }

        return $result;
    }

    public function getCaptureMap(FieldBitMap $occupied): FieldBitMap {
        $direction = $this->side == Side::WHITE ? 1 : -1;

        $rank = $this->position->rank;
        $file = $this->position->file;

        return new FieldBitMap([
            new Position($file - 1, $rank + 1 * $direction),
            new Position($file + 1, $rank + 1 * $direction),
        ]);
    }

    public function getCaptureableMap(bool $forPawn): FieldBitMap {
        $result = new FieldBitMap([
            $this->position,
        ]);

        if ($forPawn && $this->wasMovedLast && abs($this->oldPosition->rank - $this->position->rank) == 2) {
            $result->add(new Position(
                $this->position->file,
                ($this->position->rank + $this->oldPosition->rank) / 2
            ));
        }

        return $result;
    }

    public function canPromote(Position $position): bool {
        return ($this->side == Side::WHITE) ? ($position->rank == 7) : ($position->rank == 0);
    }
}