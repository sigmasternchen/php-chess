<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

final class BishopTest extends TestCase {

    public function testType() {
        $subject = new Bishop(
            new Position(0, 0),
            Side::WHITE,
        );

        $this->assertEquals(PieceType::BISHOP, $subject->getType());
    }

    public function testMoves_unobstructed() {
        $subject = new Bishop(new Position(
            3, 4
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty());

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(2, 3),
                new Position(1, 2),
                new Position(0, 1),
                new Position(4, 5),
                new Position(5, 6),
                new Position(6, 7),
                new Position(4, 3),
                new Position(5, 2),
                new Position(6, 1),
                new Position(7, 0),
                new Position(2, 5),
                new Position(1, 6),
                new Position(0, 7),
            ]))
        );
    }

    public function testMoves_obstructed() {
        $subject = new Bishop(new Position(
            3, 2
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(
            new FieldBitMap([
                new Position(4, 3),
                new Position(5, 0)
            ]),
            new FieldBitMap([
                new Position(2, 1)
            ]),
            FieldBitMap::empty()
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(4, 1),
                new Position(2, 1),
                new Position(2, 3),
                new Position(1, 4),
                new Position(0, 5)
            ]))
        );
    }

    public function testCaptureable() {
        $subject = new Bishop(
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
        $subject = new Bishop(new Position(
            5, 2
        ), Side::WHITE);

        $result = $subject->getCaptureMap(
            new FieldBitMap([
                new Position(6, 3),
                new Position(3, 4)
            ])
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(6, 1),
                new Position(7, 0),
                new Position(4, 3),
                new Position(4, 1),
                new Position(3, 0),
            ]))
        );
    }
}