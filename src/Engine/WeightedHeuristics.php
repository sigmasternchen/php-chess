<?php

namespace Engine;

use Game\Game;

class WeightedHeuristics implements Heuristic {
    private array $heuristics;
    private array $weights;

    public function __construct(array $heuristicsWithWeights) {
        $this->heuristics = array_map(fn($heuristicWithWeight) => $heuristicWithWeight[0], $heuristicsWithWeights);
        $this->weights = array_map(fn($heuristicWithWeight) => $heuristicWithWeight[1], $heuristicsWithWeights);
    }

    public function ratePosition(Game $game): float {
        return array_sum(
            array_map(
                fn($heuristic, $weight) => $weight * $heuristic->ratePosition($game),
                $this->heuristics,
                $this->weights,
            )
        );
    }
}