<?php
declare(strict_types=1);

use Game\FieldBitMap;
use Game\Position;
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

}