<?php

namespace Game;

class Game {
    private array $pieces;
    private King $whiteKing;
    private King $blackKing;

    private Side $current;

    private GameHistory $history;

    private ?array $moveCache = null;

    private int $movesSinceLastCapture = 0;
    private int $movesSinceLastPawnMove = 0;

    public function __construct(array $pieces, Side $current) {
        $this->pieces = $pieces;
        $this->current = $current;

        $this->whiteKing = current(array_filter($this->pieces, fn($p) => ($p instanceof King) && $p->getSide() == Side::WHITE));
        $this->blackKing = current(array_filter($this->pieces, fn($p) => ($p instanceof King) && $p->getSide() == Side::BLACK));

        $this->history = new GameHistory();
        $this->history->add($this);
    }

    public function getCurrentSide(): Side {
        return $this->current;
    }

    public function getAllPieces(): array {
        return $this->pieces;
    }

    public function getPieces(Side $side): array {
        return array_values(array_filter($this->pieces, fn($p) => $p->getSide() == $side));
    }

    private function &findPiece(Piece $needle): Piece {
        foreach ($this->pieces as &$piece) {
            if ($piece->equals($needle)) {
                return $piece;
            }
        }

        throw new \RuntimeException("piece not found: " . $needle);
    }

    private function removePiece(Piece $needle): void {
        $this->pieces = array_values(
            array_filter($this->pieces, fn($p) => !($p->equals($needle)))
        );
    }

    public function getKing(Side $side): King {
        if ($side == Side::WHITE) {
            return $this->whiteKing;
        } else {
            return $this->blackKing;
        }
    }

    public function getOccupied(array $pieces): FieldBitMap {
        $occupiedMap = FieldBitMap::empty();
        foreach ($pieces as $piece) {
            $occupiedMap->add($piece->getPosition());
        }

        return $occupiedMap;
    }

    private function getAllOccupied(): FieldBitMap {
        return $this->getOccupied($this->pieces);
    }

    public function getCaptureable(array $pieces, bool $forPawn): FieldBitMap {
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
        array       &$opponentPieces,
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

    private function getCastleMoves(
        array &$ownPieces,
        FieldBitMap $occupied,
        FieldBitMap $capturable,
        FieldBitMap $threatened
    ): array {
        $king = $this->getKing($this->current);
        return array_values(
            array_map(
                fn($r) => new Move(
                    $king,
                    new Position(
                        $king->getPosition()->file + 2 * ($r->getPosition()->file <=> $king->getPosition()->file),
                        $king->getPosition()->rank,
                    ),
                    null,
                    null,
                    $r,
                ),
                array_filter(
                    array_filter($ownPieces, fn($p) => $p instanceof Rook),
                    fn($r) => $king->canCastle($occupied, $capturable, $threatened, $r)
                )
            )
        );
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

        $candidates = array_values(array_filter($candidates, [$this, "isMoveLegal"]));

        // castle moves should always be legal
        $candidates = array_merge($candidates, $this->getCastleMoves(
            $ownPieces,
            $occupied,
            $capturableNonPawn,
            $threatened,
        ));

        return $candidates;
    }

    public function apply(Move $move): Game {
        $game = new Game(
            array_map(fn($p) => clone $p, $this->pieces),
            $this->current,
        );
        $game->history = clone $game->history;

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

        $this->movesSinceLastPawnMove++;
        $this->movesSinceLastCapture++;

        if ($move->castleWith) {
            $king = $this->findPiece($move->piece);
            $rook = $this->findPiece($move->castleWith);

            // move rook first to avoid temporary variable
            $rook->move(new Position(
                ($king->getPosition()->file + $move->target->file) / 2,
                $rook->getPosition()->rank,
            ));
            $king->move($move->target);
        } else {
            if ($move->captures) {
                $this->removePiece($move->captures);
                $this->movesSinceLastCapture = 0;
            }
            if ($move->piece instanceof Pawn) {
                $this->movesSinceLastPawnMove = 0;
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
        }

        $this->current = $this->current->getNext();
        $this->moveCache = null;

        $this->history->add($this, $move);
    }

    private function isDeadPosition(): bool {
            $ownPieces = $this->getPieces($this->current);
            $opponentPieces = $this->getPieces($this->current->getNext());

            $getRemaining = function (array $pieces): Piece {
                return ($pieces[0] instanceof King ? $pieces[1] : $pieces[0]);
            };

            if (count($ownPieces) > 2) {
                return false;
            } else if (count($opponentPieces) > 2) {
                return false;
            } else if (count($ownPieces) == 1 && count($opponentPieces) == 1) {
                return true;
            } else if (count($ownPieces) == 1) {
                $opponentRemaining = $getRemaining($opponentPieces);
                return !(
                    $opponentRemaining instanceof Queen ||
                    $opponentRemaining instanceof Rook ||
                    $opponentRemaining instanceof Pawn
                );
            } else if (count($opponentPieces) == 1) {
                $ownRemaining = $getRemaining($ownPieces);
                return !(
                    $ownRemaining instanceof Queen ||
                    $ownRemaining instanceof Rook ||
                    $ownRemaining instanceof Pawn
                );
            } else { // both have 1 piece left besides king
                $ownRemaining = $getRemaining($ownPieces);
                $opponentRemaining = $getRemaining($opponentPieces);

                return (
                    $ownRemaining instanceof Bishop &&
                    $opponentRemaining instanceof Bishop &&
                    $ownRemaining->getPosition()->getSquareColor() == $opponentRemaining->getPosition()->getSquareColor()
                );
            }
    }

    public function _testFiftyMoveRule(int $movesSinceLastCapture, int $movesSinceLastPawnMove) {
        $this->movesSinceLastPawnMove = $movesSinceLastPawnMove;
        $this->movesSinceLastCapture = $movesSinceLastCapture;
    }

    private function isFiftyMoveRule(): bool {
        return $this->movesSinceLastCapture >= 50 && $this->movesSinceLastPawnMove >= 50;
    }

    public function getGameState(bool $onlyIsLegal = false): GameState {
        $allOccupied = $this->getAllOccupied();

        if ($this->isIllegal($allOccupied)) {
            return GameState::ILLEGAL;
        }

        if ($onlyIsLegal) {
            return GameState::UNKNOWN_VALID;
        }

        if ($this->history->count($this) >= 3) {
            return GameState::THREEFOLD_REPETITION;
        }

        if ($this->isDeadPosition()) {
            return GameState::DEAD_POSITION;
        }

        if ($this->isFiftyMoveRule()) {
            return GameState::FIFTY_MOVE_RULE;
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

    public function getPiece(Position $position): Piece|false {
        return current(array_filter($this->pieces, fn($p) => $p->getPosition()->equals($position)));
    }

    public function getMovesForPiece(Piece $piece): array {
        return array_values(array_filter($this->getLegalMoves(), fn($m) => $piece->equals($m->getPiece())));
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
                $position = new Position($file, $rank);
                $result .= "\033[" . ($position->getSquareColor() == Side::WHITE ? 47 : 100) . "m";

                $piece = $this->getPiece($position);
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

    public function getLastMove(): Move|null {
        return $this->history->getLastMove();
    }

    public static function fromStartPosition(): Game {
        return new Game([
            new Pawn(new Position(0, 1), Side::WHITE),
            new Pawn(new Position(1, 1), Side::WHITE),
            new Pawn(new Position(2, 1), Side::WHITE),
            new Pawn(new Position(3, 1), Side::WHITE),
            new Pawn(new Position(4, 1), Side::WHITE),
            new Pawn(new Position(5, 1), Side::WHITE),
            new Pawn(new Position(6, 1), Side::WHITE),
            new Pawn(new Position(7, 1), Side::WHITE),
            new Rook(new Position(0, 0), Side::WHITE),
            new Knight(new Position(1, 0), Side::WHITE),
            new Bishop(new Position(2, 0), Side::WHITE),
            new Queen(new Position(3, 0), Side::WHITE),
            new King(new Position(4, 0), Side::WHITE),
            new Bishop(new Position(5, 0), Side::WHITE),
            new Knight(new Position(6, 0), Side::WHITE),
            new Rook(new Position(7, 0), Side::WHITE),

            new Pawn(new Position(0, 6), Side::BLACK),
            new Pawn(new Position(1, 6), Side::BLACK),
            new Pawn(new Position(2, 6), Side::BLACK),
            new Pawn(new Position(3, 6), Side::BLACK),
            new Pawn(new Position(4, 6), Side::BLACK),
            new Pawn(new Position(5, 6), Side::BLACK),
            new Pawn(new Position(6, 6), Side::BLACK),
            new Pawn(new Position(7, 6), Side::BLACK),
            new Rook(new Position(0, 7), Side::BLACK),
            new Knight(new Position(1, 7), Side::BLACK),
            new Bishop(new Position(2, 7), Side::BLACK),
            new Queen(new Position(3, 7), Side::BLACK),
            new King(new Position(4, 7), Side::BLACK),
            new Bishop(new Position(5, 7), Side::BLACK),
            new Knight(new Position(6, 7), Side::BLACK),
            new Rook(new Position(7, 7), Side::BLACK),
        ], Side::WHITE);
    }
}