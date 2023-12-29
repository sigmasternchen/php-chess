<?php

namespace Game;

abstract class Piece {
    protected Side $side;
    protected Position $position;
    protected bool $hasMoved = false;
    protected bool $wasMovedLast = false;
    protected ?Position $oldPosition = null;

    public function __construct(Position $position, Side $side) {
        $this->position = $position;
        $this->side = $side;
    }

    public function tick() {
        $this->wasMovedLast = false;
    }

    public function move(Position $position) {
        $this->oldPosition = $this->position;
        $this->position = $position;
        $this->hasMoved = true;
        $this->wasMovedLast = true;
    }

    abstract public function getName(): string;
    abstract public function getShort(): string;
    abstract public function getMoveCandidateMap(FieldBitMap $occupied, FieldBitMap $captureable, FieldBitMap $threatened): FieldBitMap;
    public function getCaptureMap(FieldBitMap $occupied): FieldBitMap {
        return $this->getMoveCandidateMap($occupied, FieldBitMap::empty(), FieldBitMap::empty());
    }
    public function getCaptureableMap(bool $forPawn): FieldBitMap {
        return new FieldBitMap([$this->position]);
    }


}