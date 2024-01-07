<?php

namespace Game;

class Game {
    private array $pieces;
    private King $whiteKing;
    private King $blackKing;

    private Side $current;

    private ?array $moveCache = null;

    public function __construct(array $pieces, Side $current) {
        $this->pieces = $pieces;
        $this->current = $current;

        $this->whiteKing = current(array_filter($this->pieces, fn($p) => ($p instanceof King) && $p->getSide() == Side::WHITE));
        $this->blackKing = current(array_filter($this->pieces, fn($p) => ($p instanceof King) && $p->getSide() == Side::BLACK));
    }

    private function getPieces(Side $side): array {
        return array_filter($this->pieces, fn($p) => $p->getSide() == $side);
    }

    private function &findPiece(Piece $needle): Piece {
        foreach ($this->pieces as &$piece) {
            if ($piece->equals($needle)) {
                return $piece;
            }
        }

        throw new \RuntimeException("piece not found: " . $piece);
    }

    private function removePiece(Piece $needle): void {
        $this->pieces = array_values(
            array_filter($this->pieces, fn($p) => !($p->equals($needle)))
        );
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

    private function getCaptureable(array $pieces, bool $forPawn): FieldBitMap {
        $captureableMap = FieldBitMap::empty();
        foreach ($pieces as $piece) {
            $captureableMap = $captureableMap->union($piece->getCaptureableMap($forPawn));
        }

        return $captureableMap;
    }

    private function getThreatened(array $pieces, FieldBitMap $occupied): FieldBitMap {
        $threatenedMap = FieldBitMap::empty();
        foreach ($pieces as $piece) {
            $threatenedMap = $threatenedMap->union($piece->getCaptureMap($occupied));
        }

        return $threatenedMap;
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


    public function getLegalMoves(): array {
        if ($this->moveCache) {
            return $this->moveCache;
        }

        $ownPieces = $this->getPieces($this->current);
        $opponentPieces = $this->getPieces($this->current->getNext());

        $occupied = $this->getOccupied($ownPieces);
        $threatened = $this->getThreatened($opponentPieces, $occupied);

        $this->moveCache = $this->getLegalMovesParameterized(
            $ownPieces,
            $opponentPieces,
            $occupied,
            $this->getCaptureable($opponentPieces, false),
            $this->getCaptureable($opponentPieces, true),
            $threatened,
        );

        return $this->moveCache;
    }

    private function generatePromotionMoves(Move $candidate): array {
        $candidates = [];

        foreach (PieceType::getPromotionPieces() as $type) {
            $candidate->promoteTo = $type;
            $candidates[] = clone $candidate;
        }

        return $candidates;
    }

    private function findCapturedPiece(Piece $piece, array $opponentPieces, Position $target): ?Piece {
        foreach ($opponentPieces as $capture) {
            if ($capture->getCaptureableMap($piece instanceof Pawn)->has($target)) {
                return $capture;
            }
        }

        return null;
    }

    private function isCapture(Position $target, FieldBitMap $captureableForPawn): bool {
        return $captureableForPawn->has($target);
    }

    private function getCandidateMovesForPiece(
        Piece       $piece,
        array       $opponentPieces,
        FieldBitMap $occupied,
        FieldBitMap $capturableForNonPawn,
        FieldBitMap $captureableForPawn,
        FieldBitMap $threatened,
    ): array {
        $candidates = [];

        $candidateMap = $piece->getMoveCandidateMap(
            $occupied,
            ($piece instanceof Pawn) ? $captureableForPawn : $capturableForNonPawn,
            $threatened
        );

        foreach ($candidateMap->getPositions() as $target) {
            $candidate = new Move($piece, $target);
            if ($this->isCapture($target, $captureableForPawn)) {
                $candidate->captures = $this->findCapturedPiece($piece, $opponentPieces, $target);
            }
            if ($piece instanceof Pawn && $piece->promotes($target)) {
                $candidates = array_merge($candidates, $this->generatePromotionMoves($candidate));
            } else {
                $candidates[] = $candidate;
            }
        }

        return $candidates;
    }

    private function isMoveLegal(Move $move) {
        $futureGame = $this->apply($move);
        return $futureGame->getGameState(true) != GameState::ILLEGAL;
    }

    private function getLegalMovesParameterized(
        array &$ownPieces,
        array &$opponentPieces,
        FieldBitMap $occupied,
        FieldBitMap $capturableNonPawn,
        FieldBitMap $captureablePawn,
        FieldBitMap $threatened
    ): array {
        $candidates = [];

        foreach ($ownPieces as $piece) {
            $candidates = array_merge($candidates, $this->getCandidateMovesForPiece(
                $piece,
                $opponentPieces,
                $occupied,
                $capturableNonPawn,
                $captureablePawn,
                $threatened,
            ));
        }

        return array_values(array_filter($candidates, [$this, "isMoveLegal"]));
    }

    public function apply(Move $move): Game {
        $game = new Game(
            array_map(fn($p) => clone $p, $this->pieces),
            $this->current,
        );

        $game->applyInPlace($move);

        return $game;
    }

    private function tick(): void {
        foreach ($this->pieces as $piece) {
            $piece->tick();
        }
    }

    public function applyInPlace(Move $move): void {
        $this->tick();

        if ($move->captures) {
            $this->removePiece($move->captures);
        }
        if ($move->promoteTo) {
            $this->removePiece($move->piece);

            $promoted = $move->piece->promote($move->promoteTo);
            $promoted->move($move->target);
            $this->pieces[] = $promoted;
        } else {
            $piece = $this->findPiece($move->piece);
            $piece->move($move->target);
        }

        $this->current = $this->current->getNext();
    }

    public function getGameState(bool $onlyIsLegal = false): GameState {
        $allOccupied = $this->getAllOccupied();

        if ($this->isIllegal($allOccupied)) {
            return GameState::ILLEGAL;
        }

        if ($onlyIsLegal) {
            return GameState::UNKNOWN_VALID;
        }

        $legalMoves = $this->getLegalMoves();

        if ($this->isCheck($allOccupied)) {
            if (!$legalMoves) {
                return GameState::CHECKMATE;
            } else {
                return GameState::CHECK;
            }
        }

        if (!$legalMoves) {
            return GameState::STALEMATE;
        }

        return GameState::DEFAULT;
    }

    public function visualize(): string {
        $result = "  ";

        for ($file = 0; $file < 8; $file++) {
            $result .= chr(ord('A') + $file) . " ";
        }

        $result .= "\n";

        for ($rank = 7; $rank >= 0; $rank--) {
            $result .= ($rank + 1) . " ";

            for ($file = 0; $file < 8; $file++) {
                $color = ($rank % 2) ^ ($file % 2);
                $result .= "\033[" . ($color ? 47 : 100) . "m";

                $piece = current(array_filter($this->pieces, fn($p) => $p->getPosition()->equals(new Position($file, $rank))));
                if ($piece) {
                    if ($piece->getSide() == Side::WHITE) {
                        $result .= "\033[97m";
                    } else {
                        $result .= "\033[30m";
                    }

                    $short = $piece->getType()->getShort();
                    $result .= ($short ?: "p") . " ";
                } else {
                    $result .= "  ";
                }

                $result .= "\033[0m";
            }

            $result .= " " . ($rank + 1) . "\n";
        }

        $result .= "  ";

        for ($file = 0; $file < 8; $file++) {
            $result .= chr(ord('A') + $file) . " ";
        }

        $result .= "\n";

        return $result;
    }
}