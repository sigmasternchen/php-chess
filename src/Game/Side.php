<?php

namespace Game;

enum Side {
    case WHITE;
    case BLACK;

    public function getNext(): Side {
        if ($this == Side::WHITE) {
            return Side::BLACK;
        } else {
            return Side::WHITE;
        }
    }

    public function getShort(): string {
        if ($this == Side::WHITE) {
            return "w";
        } else {
            return "b";
        }
    }
}