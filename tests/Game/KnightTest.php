<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

final class KnightTest extends TestCase {

    public function testType() {
        $subject = new Knight(
            new Position(0, 0),
            Side::WHITE,
        );

        $this->assertEquals(PieceType::KNIGHT, $subject->getType());
    }

    public function testMoves_unobstructed() {
        $subject = new Knight(new Position(
            3, 4
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(FieldBitMap::empty(), FieldBitMap::empty(), FieldBitMap::empty());

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(4, 2),
                new Position(4, 6),
                new Position(2, 2),
                new Position(2, 6),
                new Position(5, 3),
                new Position(5, 5),
                new Position(1, 3),
                new Position(1, 5),
            ]))
        );
    }

    public function testMoves_obstructed() {
        $subject = new Knight(new Position(
            3, 1
        ), Side::WHITE);

        $result = $subject->getMoveCandidateMap(
            new FieldBitMap([
                new Position(2, 3),
                new Position(5, 2)
            ]),
            new FieldBitMap([
                new Position(4, 3)
            ]),
            FieldBitMap::empty()
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(4, 3),
                new Position(1, 2),
                new Position(1, 0),
                new Position(5, 0)
            ]))
        );
    }

    public function testCaptureable() {
        $subject = new Knight(
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
        $subject = new Knight(new Position(
            5, 2
        ), Side::WHITE);

        $result = $subject->getCaptureMap(
            new FieldBitMap([
                new Position(6, 4),
                new Position(4, 4),
                new Position(3, 1),
                new Position(7, 1),
            ])
        );

        $this->assertTrue(
            $result->equals(new FieldBitMap([
                new Position(3, 3),
                new Position(7, 3),
                new Position(6, 0),
                new Position(4, 0),
            ]))
        );
    }
}