<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

final class KingTest extends TestCase {

    public function testType() {
        $subject = new King(
            new Position(0, 0),
            Side::WHITE,
        );

        $this->assertEquals(PieceType::KING, $subject->getType());
    }

    public function testMoves_unobstructed() {
        $subject = new King(new Position(
            3, 4
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty());

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(2, 3),
                new Position(2, 4),
                new Position(2, 5),
                new Position(3, 3),
                new Position(3, 5),
                new Position(4, 3),
                new Position(4, 4),
                new Position(4, 5),
            ]))
        );
    }

    public function testMoves_obstructed() {
        $subject = new King(new Position(
            3, 0
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(
            new FieldBitMap([
                new Position(3, 1),
            ]),
            new FieldBitMap([
                new Position(2, 1),
            ]),
            new FieldBitMap([
               new Position(2, 0),
            ]),
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(2, 1),
                new Position(4, 1),
                new Position(4, 0),
            ]))
        );
    }

    public function testCaptureable() {
        $subject = new King(
            new Position(5, 6),
            Side::WHITE,
        );

        $this->assertTrue(
            $subject->getCaptureableMap(true)->equals(new FieldBitMap([
                new Position(5, 6)
            ]))
        );
    }

    public function testCaptureMap() {
        $subject = new King(new Position(
            5, 2
        ), Side::WHITE);

        $result = $subject->getCaptureMap(
            new FieldBitMap([
                new Position(4, 3),
                new Position(4, 1),
                new Position(6, 3),
                new Position(6, 1),
            ])
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(5, 1),
                new Position(5, 3),
                new Position(4, 2),
                new Position(6, 2),
            ]))
        );
    }

    public function testCanCastle_long() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertTrue($king->canCastle(
            $maximumOccupiedMap,
            $maximumOccupiedMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_short() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(7, 0),
            Side::WHITE,
        );

        $maximumMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(6, 0),
                new Position(5, 0),
            ]))->invert());

        $this->assertTrue($king->canCastle(
            $maximumMap,
            $maximumMap,
            $maximumMap,
            $rook,
        ));
    }

    public function testCanCastle_long_occupied1() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }
    public function testCanCastle_long_occupied2() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_occupied3() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_captureable1() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_captureable2() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_captureable3() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_threatened2() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_threatened3() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_kingMoved() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE,
            true
        );

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }

    public function testCanCastle_long_rookMoved() {
        $king = new King(
            new Position(4, 0),
            Side::WHITE,
        );
        $king->move(new Position(4, 0));

        $rook = new Rook(
            new Position(0, 0),
            Side::WHITE,
            true,
        );

        $maximumOccupiedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumCaptureableMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(1, 0),
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());
        $maximumThreatenedMap =
            FieldBitMap::full()->intersect((new FieldBitMap([
                new Position(2, 0),
                new Position(3, 0),
            ]))->invert());

        $this->assertFalse($king->canCastle(
            $maximumOccupiedMap,
            $maximumCaptureableMap,
            $maximumThreatenedMap,
            $rook,
        ));
    }
}