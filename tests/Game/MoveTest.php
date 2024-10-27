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
            new Knight(new Position(2, 1), Side::WHITE),
            new Knight(new Position(4, 1), Side::WHITE),
        ], Side::WHITE);

        $subject = new Move(
            new Knight(new Position(2, 1), Side::WHITE),
            new Position(3, 3),
        );

        $this->assertEquals("Ncd4", $subject->getShort($game));
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

    public function testToJS_capturesAndPromotes() {
        $move = new Move(
            new Pawn(new Position(1, 6), Side::WHITE),
            new Position(2, 7),
            new Queen(new Position(2, 7), Side::BLACK),
            PieceType::QUEEN,
        );

        $this->assertEquals("w--b7,c8,b-Q-c8,Q,", $move->toJS());
    }

    public function testToJS_minimal() {
        $move = new Move(
            new Queen(new Position(1, 6), Side::WHITE),
            new Position(3, 4)
        );

        $this->assertEquals("w-Q-b7,d5,,,", $move->toJS());
    }

    public function testToJS_castle() {
        $move = new Move(
            new King(new Position(4, 0), Side::WHITE),
            new Position(2, 0),
            null,
            null,
            new Rook(new Position(0, 0), Side::WHITE)
        );

        $this->assertEquals("w-K-e1,c1,,,w-R-a1", $move->toJS());
    }

    public function testFromJS_capturesAndPromotes() {
        $move = Move::fromJS("w--b7,c8,b-Q-c8,Q,");

        $this->assertEquals("b7xc8Q", $move->getLong());
    }

    public function testFromJS_minimal() {
        $move = Move::fromJS("w-Q-b7,d5,,,");

        $this->assertEquals("Qb7d5", $move->getLong());
    }

    public function testFromJS_castle() {
        $move = Move::fromJS("w-K-e1,c1,,,w-R-a1");

        $this->assertEquals("O-O-O", $move->getLong());
    }

}