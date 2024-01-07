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
}