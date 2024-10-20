<?php

namespace Game;

abstract class Piece {
    protected Side $side;
    protected Position $position;
    protected bool $hasMoved = false;
    protected bool $wasMovedLast = false;
    protected ?Position $oldPosition = null;

    public function __construct(Position $position, Side $side, bool $hasMoved = false) {
        $this->position = $position;
        $this->side = $side;
        $this->hasMoved = $hasMoved;
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

    public function getSide(): Side {
        return $this->side;
    }

    public function getPosition(): Position {
        return $this->position;
    }

    abstract public function getType(): PieceType;
    abstract public function getMoveCandidateMap(FieldBitMap $occupied, FieldBitMap $captureable, FieldBitMap $threatened): FieldBitMap;
    public function getCaptureMap(FieldBitMap $occupied): FieldBitMap {
        return $this->getMoveCandidateMap($occupied, FieldBitMap::empty(), FieldBitMap::empty());
    }
    public function getCaptureableMap(bool $forPawn): FieldBitMap {
        return new FieldBitMap([$this->position]);
    }

    public function __toString() {
        return $this->getType()->getShort() . $this->getPosition();
    }

    public function toJS() {
        return join("-", [
            $this->getSide()->getShort(),
            $this->getType()->getShort(),
            $this->getPosition()
        ]);
    }

    private static function getClassForType(PieceType $type): string {
        switch ($type) {
            case PieceType::PAWN:
                return Pawn::class;
            case PieceType::BISHOP:
                return Bishop::class;
            case PieceType::KNIGHT:
                return Knight::class;
            case PieceType::ROOK:
                return Rook::class;
            case PieceType::QUEEN:
                return Queen::class;
            case PieceType::KING:
                return King::class;
        }

        throw new \RuntimeException("unknown piecetype " . $type->getLong());
    }

    public static function ofType(PieceType $type, Position $position, Side $side): Piece {
        return new (self::getClassForType($type))($position, $side);
    }

    public function promote(PieceType $type): Piece {
        $result = self::ofType($type, $this->position, $this->side);
        $result->hasMoved = $this->hasMoved;
        $result->wasMovedLast = $this->wasMovedLast;
        $result->oldPosition = $this->oldPosition;
        return $result;
    }

    public function equals(Piece $piece): bool {
        return get_class($this) == get_class($piece) &&
            $this->position->equals($piece->position);
    }
}