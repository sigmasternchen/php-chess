<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

final class PositionTest extends TestCase {

    public function testIsValid_default_min() {
        $subject = new Position(0, 0);
        $this->assertTrue($subject->isValid());
    }

    public function testIsValid_default_max() {
        $subject = new Position(7, 7);
        $this->assertTrue($subject->isValid());
    }

    public function testIsValid_fileTooHigh() {
        $subject = new Position(8, 7);
        $this->assertFalse($subject->isValid());
    }

    public function testIsValid_RankTooHigh() {
        $subject = new Position(7, 8);
        $this->assertFalse($subject->isValid());
    }

    public function testIsValid_fileTooLow() {
        $subject = new Position(-1, 0);
        $this->assertFalse($subject->isValid());
    }

    public function testIsValid_RankTooLow() {
        $subject = new Position(0, -1);
        $this->assertFalse($subject->isValid());
    }

    public function testToString() {
        $subject = new Position(4, 5);

        $this->assertEquals("e6", strval($subject));
    }
}