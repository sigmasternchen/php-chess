<?php

namespace Game;

class Knight extends Piece {

    public function getType(): PieceType {
        return PieceType::KNIGHT;
    }

    public function getMoveCandidateMap(FieldBitMap $occupied, FieldBitMap $captureable, FieldBitMap $threatened): FieldBitMap {
        $result = FieldBitMap::empty();

        for ($i = 0; $i < 8; $i++) {
            $fileOffset = ($i % 2 + 1) * ($i < 4 ? 1 : -1);
            $rankOffset = (($i + 1) % 2 + 1) * (($i & 2) == 0 ? 1 : -1);

            $candidate = new Position(
                $this->position->file + $fileOffset,
                $this->position->rank + $rankOffset
            );
            if ($candidate->isValid() && !$occupied->has($candidate)) {
                $result->add($candidate);
            }
        }

        return $result;
    }
}