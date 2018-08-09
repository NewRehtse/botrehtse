<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Application\Service;

use App\Domain\MapInterface;
use App\Domain\AStar\Node as MyNode;
use JMGQ\AStar\AStar;
use JMGQ\AStar\Node;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class AStarAlgorithm extends AStar
{
    /** @var MapInterface $map */
    private $map;

    /*
     * AStarAlgorithm constructor.
     *
     * @param MapInterface $map
     */
    public function __construct(MapInterface $map)
    {
        $this->map = $map;
    }

    /**
     * @param Node $node
     *
     * @return array
     */
    public function generateAdjacentNodes(Node $node): array
    {
        if (!$node instanceof Mynode) {
            return [];
        }

        $adjacent = [];
        $view = $this->map->view();

        /** @var MyNode $node */
        $currentPosition = $node->position();

        /** @var MyNode $upNode */
        $upNode = $view[$currentPosition->x()][$currentPosition->y() + 1];
        if (!$upNode->isOutsideBoard()) {
            $adjacent[] = $upNode;
        }

        /** @var MyNode $downNode */
        $downNode = $view[$currentPosition->x()][$currentPosition->y() - 1];
        if (!$downNode->isOutsideBoard()) {
            $adjacent[] = $downNode;
        }

        /** @var MyNode $leftNode */
        $leftNode = $view[$currentPosition->x() - 1][$currentPosition->y()];
        if (!$leftNode->isOutsideBoard()) {
            $adjacent[] = $leftNode;
        }

        /** @var MyNode $rightNode */
        $rightNode = $view[$currentPosition->x() + 1][$currentPosition->y()];
        if (!$rightNode->isOutsideBoard()) {
            $adjacent[] = $rightNode;
        }

        /** @var MyNode $rightUpNode */
        $rightUpNode = $view[$currentPosition->x() + 1][$currentPosition->y() + 1];
        if (!$rightUpNode->isOutsideBoard()) {
            $adjacent[] = $rightUpNode;
        }
        /** @var MyNode $rigthDownNode */
        $rigthDownNode = $view[$currentPosition->x() + 1][$currentPosition->y() - 1];
        if (!$rigthDownNode->isOutsideBoard()) {
            $adjacent[] = $rigthDownNode;
        }

        /** @var MyNode $leftUpNode */
        $leftUpNode = $view[$currentPosition->x() - 1][$currentPosition->y() + 1];
        if (!$leftUpNode->isOutsideBoard()) {
            $adjacent[] = $leftUpNode;
        }
        /** @var MyNode $leftDownNode */
        $leftDownNode = $view[$currentPosition->x() - 1][$currentPosition->y() - 1];
        if (!$leftDownNode->isOutsideBoard()) {
            $adjacent[] = $leftDownNode;
        }

        return $adjacent;
    }

    /**
     * @param Node $start
     * @param Node $goal
     *
     * @return int
     */
    public function calculateRealCost(Node $start, Node $goal): int
    {
        /** @var MyNode $start */
        $startPosition = $start->position();

        /** @var MyNode $goal */
        $goalPosition = $goal->position();

        $movementCost = 14;

        if ($startPosition->x() === $goalPosition->x() || $startPosition->y() === $goalPosition->y()) {
            $movementCost = 10;
        }

        return $movementCost;
    }

    /**
     * @param Node $start
     * @param Node $targetNode
     *
     * @return int
     */
    public function calculateEstimatedCost(Node $start, Node $targetNode): int
    {
        /** @var MyNode $targetNode */
        $targetPosition = $targetNode->position();

        /** @var MyNode $start */
        $startPosition = $start->position();

        $distance = \abs($targetPosition->x() - $startPosition->x()) + \abs($targetPosition->y() - $startPosition->y());

        return $distance;
    }
}
