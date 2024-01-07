<?php

namespace Game;

class Move {
    public Piece $piece;
    public Position $target;
    public ?Piece $captures = null;
    public ?PieceType $promoteTo = null;

    public function __construct(Piece $piece, Position $target, ?Piece $captures = null, ?PieceType $promoteTo = null) {
        $this->piece = $piece;
        $this->target = $target;
        $this->captures = $captures;
        $this->promoteTo = $promoteTo;
    }

    public function equals(Move $move): bool {
        return $this->piece->equals($move->piece) &&
            $this->target->equals($move->target) &&
            (
                ($this->captures != null && $move->captures != null && $this->captures->equals($move->captures)) ||
                ($this->captures == null && $move->captures == null)
            ) &&
            $this->promoteTo == $move->promoteTo;
    }

    public function __toString(): string {
        return $this->piece . " " .
            $this->piece->getType()->getShort() . ($this->captures ? "x" : "") . $this->target .
            ($this->promoteTo ? $this->promoteTo->getShort() : "");
    }
}