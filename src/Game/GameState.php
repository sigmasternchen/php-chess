<?php

namespace Game;

enum GameState {
    case DEFAULT;
    case CHECK;
    case CHECKMATE;
    case STALEMATE;
    case DEAD_POSITION;
    case THREEFOLD_REPETITION;
    case FIFTY_MOVE_RULE;
    case ILLEGAL;
    case UNKNOWN_VALID;
}