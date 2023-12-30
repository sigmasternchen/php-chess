<?php

namespace Game;

class King extends Piece {

    public function getName(): string {
        return "King";
    }

    public function getShort(): string {
        return "K";
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
}