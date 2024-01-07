<?php

namespace Game;

class King extends Piece {

    public function getType(): PieceType {
        return PieceType::KING;
    }

    public function getMoveCandidateMap(FieldBitMap $occupied, FieldBitMap $captureable, FieldBitMap $threatened): FieldBitMap {
        $result = FieldBitMap::empty();

        $checkCandidate = function(Position $position) use (&$result, $occupied, $captureable, $threatened) {
            if ($position->isValid() && !$occupied->has($position) && !$threatened->has($position)) {
                $result->add($position);
            }
        };

        $checkCandidate(new Position($this->position->file - 1, $this->position->rank - 1));
        $checkCandidate(new Position($this->position->file - 1, $this->position->rank + 0));
        $checkCandidate(new Position($this->position->file - 1, $this->position->rank + 1));
        $checkCandidate(new Position($this->position->file + 0, $this->position->rank - 1));
        $checkCandidate(new Position($this->position->file + 0, $this->position->rank + 1));
        $checkCandidate(new Position($this->position->file + 1, $this->position->rank - 1));
        $checkCandidate(new Position($this->position->file + 1, $this->position->rank + 0));
        $checkCandidate(new Position($this->position->file + 1, $this->position->rank + 1));

        return $result;
    }

    public function isInCheck(FieldBitMap $captureable): bool {
        return $captureable->has($this->position);
    }

    public function canCastle(
        FieldBitMap $occupied,
        FieldBitMap $captureable,
        FieldBitMap $threatened,
        Rook $rook,
    ): bool {
        if ($this->position->rank != $rook->position->rank) {
            return false;
        }
        if ($this->hasMoved || $rook->hasMoved) {
            return false;
        }

        $increment = $rook->position->file <=> $this->position->file;
        for ($file = $this->position->file + $increment; $file != $rook->position->file; $file += $increment) {
            $square = new Position($file, $this->position->rank);
            if ($occupied->has($square) || $captureable->has($square)) {
                return false;
            }
            if (abs($file - $this->position->file) <= 2 && $threatened->has($square)) {
                return false;
            }
        }

        return true;
    }
}