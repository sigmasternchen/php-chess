<?php

namespace Game;

class Bishop extends Piece {
    public function getName(): string {
        return "Bishop";
    }

    public function getShort(): string {
        return "B";
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

        return $result;
    }
}