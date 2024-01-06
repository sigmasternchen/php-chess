<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

final class PawnTest extends TestCase {

    public function testMoves_initialWhiteUnobstructed() {
        $subject = new Pawn(new Position(
            3, 1
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty());

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(3, 2),
                new Position(3, 3)
            ]))
        );
    }

    public function testMoves_initialWhitePartlyObstructed() {
        $subject = new Pawn(new Position(
            3, 1
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(
            new FieldBitMap([
                new Position(3, 3),
            ]),
            FieldBitMap::empty(),
            FieldBitMap::empty()
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(3, 2),
            ]))
        );
    }

    public function testMoves_whiteDefault() {
        $subject = new Pawn(new Position(
                4, 3
            ), Side::WHITE
        );
        $subject->move(new Position(4, 3));

        $result = $subject->getMoveCandidateMap(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty());
        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(4, 4)
            ]))
        );
    }

    public function testMoves_whiteObstructedCapturesPossible() {
        $subject = new Pawn(new Position(
            3, 4
        ), Side::WHITE);
        $subject->move(new Position(3, 4));

        $result = $subject->getMoveCandidateMap(
            new FieldBitMap([
                new Position(2, 5),
                new Position(3, 5)
            ]),
            new FieldBitMap([
                new Position(4, 5)
            ]),
            FieldBitMap::empty()
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(4, 5),
            ]))
        );
    }

    public function testMoves_initialBlackUnobstructed() {
        $subject = new Pawn(new Position(
            3, 6
        ), Side::BLACK);

        $result = $subject->getMoveCandidateMap(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty());

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(3, 5),
                new Position(3, 4)
            ]))
        );
    }

    public function testMoves_initialBlackPartlyObstructed() {
        $subject = new Pawn(new Position(
            3, 6
        ), Side::BLACK);

        $result = $subject->getMoveCandidateMap(
            new FieldBitMap([
                new Position(3, 4),
            ]),
            FieldBitMap::empty(),
            FieldBitMap::empty()
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(3, 5),
            ]))
        );
    }

    public function testMoves_blackDefault() {
        $subject = new Pawn(new Position(
                4, 3
            ), Side::BLACK
        );
        $subject->move(new Position(4, 3));

        $result = $subject->getMoveCandidateMap(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty());
        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(4, 2)
            ]))
        );
    }

    public function testMoves_blackObstructedCapturesPossible() {
        $subject = new Pawn(new Position(
            3, 5
        ), Side::BLACK);
        $subject->move(new Position(3, 5));

        $result = $subject->getMoveCandidateMap(
            new FieldBitMap([
                new Position(2, 4),
                new Position(3, 4)
            ]),
            new FieldBitMap([
                new Position(4, 4)
            ]),
            FieldBitMap::empty()
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(4, 4),
            ]))
        );
    }

    public function testMoves_bug_captureForwardNotAllowed() {
        $subject = new Pawn(
            new Position(4, 3),
            Side::BLACK,
            true
        );

        $result = $subject->getMoveCandidateMap(
            FieldBitMap::empty(),
            new FieldBitMap([new Position(4, 2)]),
            FieldBitMap::empty());
        $this->assertTrue(
            $result->isEmpty()
        );
    }

    public function testCaptureable_default() {
        $subject = new Pawn(
            new Position(3, 4),
            Side::WHITE,
        );
        $subject->move(new Position(3, 4));
        $subject->tick();

        $this->assertTrue(
            $subject->getCaptureableMap(true)->equals(new FieldBitMap([
                new Position(3, 4)
            ]))
        );
    }

    public function testCaptureable_whiteInitial() {
        $subject = new Pawn(
            new Position(3, 1),
            Side::WHITE,
        );
        $subject->move(new Position(3, 3));

        $this->assertTrue(
            $subject->getCaptureableMap(true)->equals(new FieldBitMap([
                new Position(3, 2),
                new Position(3, 3)
            ]))
        );
    }

    public function testCaptureable_blackInitial() {
        $subject = new Pawn(
            new Position(3, 6),
            Side::BLACK,
        );
        $subject->move(new Position(3, 4));

        $this->assertTrue(
            $subject->getCaptureableMap(true)->equals(new FieldBitMap([
                new Position(3, 5),
                new Position(3, 4)
            ]))
        );
    }

    public function testCaptureable_initialForNonPawns() {
        $subject = new Pawn(
            new Position(3, 1),
            Side::WHITE,
        );
        $subject->move(new Position(3, 3));

        $this->assertTrue(
            $subject->getCaptureableMap(false)->equals(new FieldBitMap([
                new Position(3, 3)
            ]))
        );
    }

    public function testCaptureMap() {
        $subject = new Pawn(
            new Position(3, 4),
            Side::WHITE,
        );

        $this->assertTrue(
            $subject->getCaptureMap(FieldBitMap::empty())->equals(new FieldBitMap([
                new Position(2, 5),
                new Position(4, 5)
            ]))
        );
    }
}