<?php

namespace Game;

class Move {
    public Piece $piece;
    public Position $target;
    public ?Piece $captures = null;
    public ?PieceType $promoteTo = null;
    public ?Piece $castleWith = null;

    public function __construct(
        Piece $piece,
        Position $target,
        ?Piece $captures = null,
        ?PieceType $promoteTo = null,
        ?Piece $castleWith = null,
    ) {
        $this->piece = $piece;
        $this->target = $target;
        $this->captures = $captures;
        $this->promoteTo = $promoteTo;
        $this->castleWith = $castleWith;
    }

    public function equals(Move $move): bool {
        return $this->piece->equals($move->piece) &&
            $this->target->equals($move->target) &&
            (
                ($this->captures != null && $move->captures != null && $this->captures->equals($move->captures)) ||
                ($this->captures == null && $move->captures == null)
            ) &&
            $this->promoteTo == $move->promoteTo &&
            (
                ($this->castleWith != null && $move->castleWith != null && $this->castleWith->equals($move->castleWith)) ||
                ($this->castleWith == null && $move->castleWith == null)
            );
    }

    private function getCheckMarker(?Game $game = null): string {
        if (!$game) {
            return "";
        } else {
            $game = $game->apply($this);
            $state = $game->getGameState();

            if ($state == GameState::CHECK) {
                return "+";
            } else if ($state == GameState::CHECKMATE) {
                return "++";
            } else {
                return "";
            }
        }
    }

    private function isCastles(): bool {
        return !!$this->castleWith;
    }

    private function isLongCastles(): bool {
        return abs($this->piece->getPosition()->file - $this->castleWith->getPosition()->file) > 3;
    }

    private function getCastlesMarker(): string {
        if ($this->isLongCastles()) {
            return "O-O-O";
        } else {
            return "O-O";
        }
    }

    private function getCapturesMarker(): string {
        return $this->captures ? "x" : "";
    }

    private function getPromotionMarker(): string {
        return $this->promoteTo ? $this->promoteTo->getShort() : "";
    }

    private function getMinimalSourcePositionMarker(Game $game): string {
        $pieceClass = get_class($this->piece);
        $piecePosition = $this->piece->getPosition();

        $needsFile = false;
        $needsRank = false;
        $needsAny = false;

        foreach ($game->getLegalMoves() as $move) {
            if (!is_a($move->piece, $pieceClass)) {
                continue;
            }
            if (!$this->target->equals($move->target)) {
                continue;
            }
            if ($this->piece->equals($move->piece)) {
                continue;
            }

            $otherPosition = $move->piece->getPosition();

            $wouldNeedRank = $piecePosition->file == $otherPosition->file;
            $wouldNeedFile = $piecePosition->rank == $otherPosition->rank;

            if ($wouldNeedRank) {
                $needsRank = true;
                $needsAny = false;
            }
            if ($wouldNeedFile) {
                $needsFile = true;
                $needsAny = false;
            }
            if (!$needsRank && !$needsFile) {
                $needsAny = true;
            }
        }

        $result = "";
        if ($needsFile || $needsAny) {
            $result .= $piecePosition->getFileString();
        }
        if ($needsRank) {
            $result .= $piecePosition->getRankString();
        }

        return $result;
    }

    public function getLong(?Game $game = null): string {
        if ($this->isCastles()) {
            $result = $this->getCastlesMarker();
        } else {
            $result = $this->piece;
            $result .= $this->getCapturesMarker();
            $result .= $this->target;
            $result .= $this->getPromotionMarker();
        }

        $result .= $this->getCheckMarker($game);

        return $result;
    }

    public function getShort(Game $game): string {
        if ($this->isCastles()) {
            $result = $this->getCastlesMarker();
        } else {
            $result = $this->piece->getType()->getShort();
            $result .= $this->getMinimalSourcePositionMarker($game);
            $result .= $this->getCapturesMarker();
            $result .= $this->target;
            $result .= $this->getPromotionMarker();
        }

        $result .= $this->getCheckMarker($game);

        return $result;
    }

    public function __toString(): string {
        return $this->getLong();
    }
}