<?php

namespace Engine;

use Game\Game;
use Game\GameState;
use Game\Move;
use Game\Side;

class MinimaxDF implements Engine {
    private int $depth;
    private Heuristic $heuristic;

    public function __construct(int $depth, Heuristic $heuristic) {
        $this->depth = $depth;
        $this->heuristic = $heuristic;
    }

    private function findMaxMoveRating(array $moves): array {
        $max = $moves[0];

        foreach ($moves as $move) {
            if ($move[1] > $max[1]) {
                $max = $move;
            }
        }

        return $max;
    }

    private function invertRating(array $move): array {
        return [$move[0], -$move[1]];
    }

    private function maxWithHeuristic(Game $game): array {
        $moves = array_map(fn($move) => [
            $move,
            -$this->heuristic->ratePosition($game->apply($move))
        ], $game->getLegalMoves());

        return $this->findMaxMoveRating($moves);
    }

    private function maxWithoutHeuristic(Game $game, int $remaindingDepth): array {
        $moves = array_map(function ($move) use ($game, $remaindingDepth) {
            $future = $game->apply($move);

            if ($future->getGameState() == GameState::CHECKMATE) {
                return [$move, INF];
            }

            $opponentMove = $this->max($future, $remaindingDepth - 1);

            return $this->invertRating([$move, $opponentMove[1]]);
        }, $game->getLegalMoves());

        foreach ($moves as $move) {
            error_log($remaindingDepth . ": " . $move[0]->getLong() . ": " . $move[1]);
        }

        return $this->findMaxMoveRating($moves);
    }

    private function max(Game $game, int $remaindingDepth): array {
        if ($remaindingDepth <= 0) {
            return $this->maxWithHeuristic($game);
        } else {
            return $this->maxWithoutHeuristic($game, $remaindingDepth);
        }
    }

    public function nextMove(Game $game): Move {
        return $this->max($game, $this->depth)[0];
    }
}