<?php

namespace Game;

class Queen extends Piece {

    public function getName(): string {
        return "Queen";
    }

    public function getShort(): string {
        return "Q";
    }

    public function getMoveCandidateMap(FieldBitMap $occupied, FieldBitMap $captureable, FieldBitMap $threatened): FieldBitMap {
        $result = FieldBitMap::empty();

        $directions = [true, true, true, true];
        for ($i = 1; $i < 8; $i++) {
            for ($d = 0; $d < 4; $d++) {
                if ($directions[$d]) {
                    $candidate = new Position(
                        $this->position->file + $i * ($d % 2 == 0 ? 1 : -1),
                        $this->position->rank + $i * ($d < 2 ? 1 : -1)
                    );
                    if ($candidate->isValid()) {
                        if ($captureable->has($candidate)) {
                            $result->add($candidate);
                            $directions[$d] = false;
                        } else if ($occupied->has($candidate)) {
                            $directions[$d] = false;
                        } else {
                            $result->add($candidate);
                        }
                    } else {
                        $directions[$d] = false;
                    }
                }
            }
        }

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