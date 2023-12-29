<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

final class FieldBitMapTest extends TestCase {

    public function testEmptyMapHasPositions() {
        $subject = FieldBitMap::empty();

        $this->assertTrue($subject->isEmpty());
        $this->assertEmpty($subject->getPositions());
    }

    public function testFullMapHasAllPositions() {
        $subject = FieldBitMap::full();

        $this->assertFalse($subject->isEmpty());
        $this->assertCount(64, $subject->getPositions());
    }

    public function testMapPositionsAreRetained() {
        $positions = [
            new Position(0, 0),
            new Position(3, 4),
            new Position(7, 7),
        ];

        $subject = new FieldBitMap($positions);

        $this->assertFalse($subject->isEmpty());
        $this->assertEquals($positions, $subject->getPositions());
    }

    public function testVisualize() {
        $subject = new FieldBitMap([
            new Position(0, 0),
            new Position(1, 1),
            new Position(2,2),
            new Position(3, 3),
            new Position(4, 3),
            new Position(5, 3),
            new Position(6, 3),
            new Position(7, 3),
            new Position(7, 4),
            new Position(7, 5),
            new Position(7, 6),
            new Position(7, 7),
        ]);

        $result = $subject->visualize();
        $this->assertEquals(
            "" .
            "00000001\n" .
            "00000001\n" .
            "00000001\n" .
            "00000001\n" .
            "00011111\n" .
            "00100000\n" .
            "01000000\n" .
            "10000000\n",
            $result,
        );
    }

}