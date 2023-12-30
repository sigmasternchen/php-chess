<?php

namespace Game;

class Rook extends Piece {

    public function getName(): string {
        return "Rook";
    }

    public function getShort(): string {
        return "R";
    }

    public function getMoveCandidateMap(FieldBitMap $occupied, FieldBitMap $captureable, FieldBitMap $threatened): FieldBitMap {
        $result = FieldBitMap::empty();

        for ($offset = 1; $offset < 8; $offset++) {
            $candidate = new Position($this->position->file, $this->position->rank + $offset);
            if (!$candidate->isValid()) {
                break;
            }

            if ($occupied->has($candidate)) {
                break;
            }

            $result->add($candidate);

            if ($captureable->has($candidate)) {
                break;
            }
        }

        for ($offset = -1; $offset > -8; $offset--) {
            $candidate = new Position($this->position->file, $this->position->rank + $offset);
            if (!$candidate->isValid()) {
                break;
            }

            if ($occupied->has($candidate)) {
                break;
            }

            $result->add($candidate);

            if ($captureable->has($candidate)) {
                break;
            }
        }

        for ($offset = 1; $offset < 8; $offset++) {
            $candidate = new Position($this->position->file + $offset, $this->position->rank);
            if (!$candidate->isValid()) {
                break;
            }

            if ($occupied->has($candidate)) {
                break;
            }

            $result->add($candidate);

            if ($captureable->has($candidate)) {
                break;
            }
        }

        for ($offset = -1; $offset > -8; $offset--) {
            $candidate = new Position($this->position->file + $offset, $this->position->rank);
            if (!$candidate->isValid()) {
                break;
            }

            if ($occupied->has($candidate)) {
                break;
            }

            $result->add($candidate);

            if ($captureable->has($candidate)) {
                break;
            }
        }

        return $result;
    }
}