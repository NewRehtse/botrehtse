<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Tests\Application\Service;

use App\Application\Service\AStarAlgorithm;
use App\Domain\AStar\Node;
use App\Domain\AStar\Map;
use App\Domain\Position;
use PHPUnit\Framework\TestCase;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 *
 * @covers \App\Application\Service\AStarAlgorithm
 */
class AStarAlgorithmTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider getDataMap
     *
     * @param array $arrayMap
     * @param array $startPositionArray
     * @param array $endPositionArray
     * @param array $expectedResult
     */
    public function shouldRunAlgorithm(array $arrayMap, array $startPositionArray, array $endPositionArray, array $expectedResult): void
    {
        $map = Map::buildFromArray($arrayMap);

        $aStarAlgorithm = new AStarAlgorithm($map);

        $startPosition = Position::buildFromArray($startPositionArray);
        $endPosition = Position::buildFromArray($endPositionArray);

        $startNode = Node::build($startPosition);
        $endNode = Node::build($endPosition);

        $result = $aStarAlgorithm->run($startNode, $endNode);

        /** @var Node $adjNode */
        foreach ($result as $adjNode) {
            $position = [$adjNode->position()->x(), $adjNode->position()->y()];
            static::assertContains($position, $expectedResult);
        }
    }

    /**
     * @test
     *
     * @dataProvider getDataForGenerateAdjacentNodes
     *
     * @param array $arrayMap
     * @param array $positionNode
     * @param array $expectedNodes
     */
    public function shouldGenerateAdjacentNodes(array $arrayMap, array $positionNode, array $expectedNodes): void
    {
        $map = Map::buildFromArray($arrayMap);

        $aStarAlgorithm = new AStarAlgorithm($map);

        $nodePosition = Position::buildFromArray($positionNode);

        $node = Node::build($nodePosition);

        $adjacentNodes = $aStarAlgorithm->generateAdjacentNodes($node);

        /** @var Node $adjNode */
        foreach ($adjacentNodes as $adjNode) {
            $position = [$adjNode->position()->x(), $adjNode->position()->y()];
            static::assertContains($position, $expectedNodes);
        }
    }

    /**
     * @return array
     */
    public function getDataForGenerateAdjacentNodes(): array
    {
        return [
            [
                [
                    'map' => $this->getArrayMap(),
                    'lighthouses' => [],
                ],
                [1, 1],
                [[1, 2], [2, 2], [2, 1]],
            ],
        ];
    }

    public function getDataMap(): array
    {
        return [
            [
                [
                    'map' => $this->getArrayMap(),
                    'lighthouses' => [],
                ],
                [1, 3],
                [5, 3],
                [[1, 3], [2, 4], [3, 5], [4, 4], [5, 3]],
            ],
            [
                [
                    'map' => $this->getIslandMap(),
                    'lighthouses' => $this->getIslandLighthouses(),
                ],
                [8, 9],
                [16, 16],
                [[16, 16], [15, 15], [13, 14], [14, 15], [12, 13], [11, 12], [10, 11], [9, 10], [8, 9]],
            ],
        ];
    }

    /**
     * @return array
     */
    private function getArrayMap(): array
    {
        return [
                [0, 0, 0, 0, 0, 0, 0],
                [0, 1, 1, 1, 1, 1, 0],
                [0, 1, 1, 0, 1, 1, 0],
                [0, 1, 1, 0, 1, 1, 0],
                [0, 1, 1, 0, 1, 1, 0],
                [0, 1, 1, 1, 1, 1, 0],
                [0, 1, 1, 1, 1, 1, 0],
                [0, 0, 0, 0, 0, 0, 0],
            ];
    }

    /**
     * @return array
     */
    private function getIslandMap(): array
    {
        return [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0],
            [0, 0, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 1, 1, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0],
            [0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 0, 0, 0, 1, 0, 0, 0, 0],
            [0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0],
            [0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0],
            [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0],
            [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            [0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0],
            [0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0],
            [0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0],
            [0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0],
            [0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        ];
    }

    /**
     * @return array
     */
    private function getIslandLighthouses(): array
    {
        return  [[8, 9], [16, 16], [2, 8], [1, 16], [16, 1], [10, 14], [4, 11], [4, 3], [5, 1], [14, 9], [11, 6]];
    }
}
