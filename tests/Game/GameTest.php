<?php

namespace Game;

use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase {

    public function testGameState_illegal_white() {
        $subject = new Game(
            [
                new King(new Position(1, 1), Side::BLACK),
                new Knight(new Position(2, 3), Side::WHITE),
                new King(new Position(7, 6), Side::WHITE),
            ],
            Side::WHITE
        );

        $this->assertEquals(GameState::ILLEGAL, $subject->getGameState());
    }

    public function testGameState_illegal_black() {
        $subject = new Game(
            [
                new King(new Position(0, 0), Side::WHITE),
                new Queen(new Position(7, 7), Side::BLACK),
                new King(new Position(7, 6), Side::BLACK),
            ],
            Side::BLACK
        );

        $this->assertEquals(GameState::ILLEGAL, $subject->getGameState());
    }

    public function testGameState_check_white() {
        $subject = new Game(
            [
                new King(new Position(5, 4), Side::WHITE),
                new Rook(new Position(2, 4), Side::BLACK),
                new King(new Position(7, 6), Side::BLACK),
            ],
            Side::WHITE
        );

        $this->assertEquals(GameState::CHECK, $subject->getGameState());
    }

    public function testGameState_check_black() {
        $subject = new Game(
            [
                new King(new Position(5, 4), Side::BLACK),
                new Pawn(new Position(4, 3), Side::WHITE),
                new King(new Position(7, 6), Side::WHITE),
            ],
            Side::BLACK
        );

        $this->assertEquals(GameState::CHECK, $subject->getGameState());
    }
}