<?php

namespace Game;

class Game {
    private array $pieces;
    private King $whiteKing;
    private King $blackKing;

    private Side $current;

    public function __construct(array $pieces, Side $current) {
        $this->pieces = $pieces;
        $this->current = $current;

        $this->whiteKing = current(array_filter($this->pieces, fn($p) => ($p instanceof King) && $p->getSide() == Side::WHITE));
        $this->blackKing = current(array_filter($this->pieces, fn($p) => ($p instanceof King) && $p->getSide() == Side::BLACK));
    }

    private function getPieces(Side $side): array {
        return array_filter($this->pieces, fn($p) => $p->getSide() == $side);
    }

    private function getKing(Side $side): King {
        if ($side == Side::WHITE) {
            return $this->whiteKing;
        } else {
            return $this->blackKing;
        }
    }

    private function getOccupied(array $pieces): FieldBitMap {
        $occupiedMap = FieldBitMap::empty();
        foreach ($pieces as $piece) {
            $occupiedMap->add($piece->getPosition());
        }

        return $occupiedMap;
    }

    private function getAllOccupied(): FieldBitMap {
        return $this->getOccupied($this->pieces);
    }

    private function isInCheck(Side $side, FieldBitMap $allOccupied): bool {
        $opponentPieces = $this->getPieces($side->getNext());
        $king = $this->getKing($side);

        $occupied = clone $allOccupied;
        $occupied->remove($king->getPosition());

        $captureMap = FieldBitMap::empty();
        foreach ($opponentPieces as $piece) {
            $captureMap = $captureMap->union($piece->getCaptureMap($occupied));
        }

        return $king->isInCheck($captureMap);
    }

    private function isCheck(FieldBitMap $allOccupied): bool {
        return $this->isInCheck($this->current, $allOccupied);
    }

    private function isIllegal(FieldBitMap $allOccupied): bool {
        return $this->isInCheck($this->current->getNext(), $allOccupied);
    }

    public function getGameState(): GameState {
        $allOccupied = $this->getAllOccupied();

        if ($this->isIllegal($allOccupied)) {
            return GameState::ILLEGAL;
        }

        if ($this->isCheck($allOccupied)) {
            // TODO: check for checkmate

            return GameState::CHECK;
        }

        return GameState::DEFAULT;
    }

    public function visualize(): string {

    }
}