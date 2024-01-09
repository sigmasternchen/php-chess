<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

final class MoveTest extends TestCase {

    public function testShort_noSourceCollision() {
        $game = new Game([
            new King(new Position(0,0), Side::WHITE),
            new King(new Position(7, 7), Side::BLACK),
            new Pawn(new Position(3, 3), Side::BLACK, true),
            new Knight(new Position(2, 1), Side::WHITE),
        ], Side::WHITE);

        $subject = new Move(
            new Knight(new Position(2, 1), Side::WHITE),
            new Position(3, 3),
            new Pawn(new Position(3, 3), Side::BLACK),
        );

        $this->assertEquals("Nxd4", $subject->getShort($game));
    }

    public function testShort_simpleCollisionAndCheck() {
        $game = new Game([
            new King(new Position(0,0), Side::WHITE),
            new King(new Position(2, 5), Side::BLACK),
            new Pawn(new Position(3, 3), Side::BLACK, true),
            new Knight(new Position(2, 1), Side::WHITE),
            new Knight(new Position(4, 5), Side::WHITE),
        ], Side::WHITE);

        $subject = new Move(
            new Knight(new Position(2, 1), Side::WHITE),
            new Position(3, 3),
            new Pawn(new Position(3, 3), Side::BLACK),
        );

        $this->assertEquals("Ncxd4+", $subject->getShort($game));
    }

    public function testShort_fileCollision() {
        $game = new Game([
            new King(new Position(0,0), Side::WHITE),
            new King(new Position(7, 7), Side::BLACK),
            new Pawn(new Position(3, 3), Side::BLACK, true),
            new Knight(new Position(2, 1), Side::WHITE),
            new Knight(new Position(2, 5), Side::WHITE),
        ], Side::WHITE);

        $subject = new Move(
            new Knight(new Position(2, 1), Side::WHITE),
            new Position(3, 3),
            new Pawn(new Position(3, 3), Side::BLACK),
        );

        $this->assertEquals("N2xd4", $subject->getShort($game));
    }

    public function testShort_rankCollision() {
        $game = new Game([
            new King(new Position(0,0), Side::WHITE),
            new King(new Position(0, 0), Side::BLACK),
            new Pawn(new Position(3, 3), Side::BLACK, true),
            new Knight(new Position(2, 1), Side::WHITE),
            new Knight(new Position(4, 1), Side::WHITE),
        ], Side::WHITE);

        $subject = new Move(
            new Knight(new Position(2, 1), Side::WHITE),
            new Position(3, 3),
            new Pawn(new Position(3, 3), Side::BLACK),
        );

        $this->assertEquals("Ncxd4", $subject->getShort($game));
    }

    public function testShort_fullCollisionAndCheckmate() {
        $game = new Game([
            new King(new Position(0,0), Side::WHITE),
            new King(new Position(4, 5), Side::BLACK),
            new Pawn(new Position(3, 3), Side::BLACK, true),
            new Knight(new Position(2, 1), Side::WHITE),
            new Knight(new Position(2, 5), Side::WHITE),
            new Knight(new Position(4, 1), Side::WHITE),
            new Queen(new Position(6, 6), Side::WHITE),
            new Rook(new Position(3, 7), Side::WHITE),
        ], Side::WHITE);

        $subject = new Move(
            new Knight(new Position(2, 1), Side::WHITE),
            new Position(3, 3),
            new Pawn(new Position(3, 3), Side::BLACK),
        );

        $this->assertEquals("Nc2xd4++", $subject->getShort($game));
    }

}