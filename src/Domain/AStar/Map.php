<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Domain\AStar;

use App\Domain\AbstractMap;
use App\Domain\Lighthouse;
use App\Domain\Position;
use App\Domain\SquareInterface;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class Map extends AbstractMap
{
    /** @var Node[] */
    private $view;

    /** @var Lighthouse[] */
    private $lighthouses;

    /** @var null|Position */
    private $currentPositionMaxEnergy;

    /** @var Lighthouse $lighthouseWithKey */
    private $lighthouseWithKey;

    /**
     * Map constructor.
     *
     * @param array $view
     * @param array $lighthouses
     */
    public function __construct(array $view, array $lighthouses)
    {
        $this->view = $view;
        $this->lighthouses = $lighthouses;
    }

    /**
     * @param array $view
     * @param array $lighthouses
     *
     * @return Map
     */
    public static function build(array $view, array $lighthouses): self
    {
        return new self($view, $lighthouses);
    }

    /**
     * @param array $map
     * @param array $lighthouses
     *
     * @return Map
     */
    public static function buildFromArray(array $map, array $lighthouses): self
    {
        $lighthousesPositions = [];
        $lighthousesArray = [];
        $view = [];

        foreach ($lighthouses as $lighthouse) {
            $x = $lighthouse[0];
            $y = $lighthouse[1];

            $position = Position::build($x, $y);
            $lighthousesArray[] = Lighthouse::build($position);
            $lighthousesPositions[] = "$x-$y";
        }

        foreach ($map as $y => $row) {
            foreach ($row as $x => $outsideBoard) {
                $position = Position::build($x, $y);
                $square = Node::build($position, 1 !== $outsideBoard);

                if (\in_array("$x-$y", $lighthousesPositions, true)) {
                    $lighthouse = Lighthouse::build($position);
                    $square->setLighthouse($lighthouse);
                }

                $view[$x][$y] = $square;
            }
        }

        return self::build($view, $lighthousesArray);
    }

    /**
     * @param array $lighthouses
     */
    public function updateLighthouses(array $lighthouses): void
    {
        $mapLighthouses = [];

        foreach ($lighthouses as $lighthouse) {
            $lighthousePosition = Position::buildFromArray($lighthouse['position']);
            $square = $this->getSquare($lighthousePosition);
            $lighthouseObject = $square->lighthouse();

            if (null === $lighthouseObject) {
                $lighthouseObject = Lighthouse::build($lighthousePosition);
            }

            $lighthouseObject->setOwner($lighthouse['owner'] ?? -1);
            $lighthouseObject->setEnergy($lighthouse['energy']);

            foreach ($lighthouse['connections'] as $connection) {
                $connectionPosition = Position::buildFromArray($connection);
                $lighthouseObject->addConnections($connectionPosition);
            }

            $lighthouseObject->setHasKey($lighthouse['have_key']);

            if ($lighthouseObject->hasKey()) {
                $this->lighthouseWithKey = $lighthouseObject;
            }

            $square->setLighthouse($lighthouseObject);
            $mapLighthouses[] = $lighthouseObject;

            $this->view[$lighthousePosition->x()][$lighthousePosition->y()] = $square;
        }

        $this->lighthouses = $mapLighthouses;
    }

    /**
     * @return Lighthouse|null
     */
    public function getLighthouseWithKey(): ?Lighthouse
    {
        return $this->lighthouseWithKey;
    }

    /**
     * @param Position $position
     *
     * @return Node
     */
    public function getSquare(Position $position): SquareInterface
    {
        return $this->view[$position->x()][$position->y()];
    }

    /**
     * @param Position $currentPosition
     * @param array    $energyMatrix
     */
    public function updateView(Position $currentPosition, array $energyMatrix): void
    {
        /** @var array $view */
        $view = $this->view();

        /** @var Node $currentPositionSquare */
        $currentPositionSquare = $view[$currentPosition->x()][$currentPosition->y()];
        $currentPositionSquare->setEnergy(0);
        $view[$currentPosition->x()][$currentPosition->y()] = $currentPositionSquare;

        $currentPositionX = $currentPosition->x();
        $currentPositionY = $currentPosition->y();

        $maxEnergy = 0;
        $positionMaxEnergy = [];
        $energyFromMatrixFunctions = static::energyFromMatrixFunctions();
        $this->currentPositionMaxEnergy = null;

        foreach ($energyMatrix as $x => $row) {
            foreach ($row as $y => $currentEnergy) {
                if (-1 === $currentEnergy) {
                    //Celda fuera de alcance
                    continue;
                }

                $positionArray = $energyFromMatrixFunctions[$x][$y]($currentPositionX, $currentPositionY);

                if (-1 === $positionArray[0] || -1 === $positionArray[1]) {
                    //NOs hemos salido
                    continue;
                }

                if (!isset($view[$positionArray[0]][$positionArray[1]])) {
                    //No sé que ha pasado ¿nos hemos salido?
                    continue;
                }

                /** @var Node $positionSquare */
                $positionSquare = $view[$positionArray[0]][$positionArray[1]];

                if ($positionSquare->isOutsideBoard()) {
                    continue;
                }

                if ($currentEnergy > $maxEnergy) {
                    $positionSugested = Position::buildFromArray($positionArray);

                    if ($currentPosition->isNextTo($positionSugested)) {
                        $maxEnergy = $currentEnergy;
                        $positionMaxEnergy = $positionArray;
                    }
                }

                $positionSquare->setEnergy($currentEnergy);
                $view[$positionArray[0]][$positionArray[1]] = $positionSquare;
            }
        }

        if (!empty($positionMaxEnergy)) {
            $this->currentPositionMaxEnergy = Position::build($positionMaxEnergy[0], $positionMaxEnergy[1]);
        }
    }

    /**
     * @return Position|null
     */
    public function positionMaxEnergy(): ?Position
    {
        return $this->currentPositionMaxEnergy;
    }

    /**
     * @return array
     */
    public function view(): array
    {
        return $this->view;
    }

    /**
     * @return Lighthouse[]
     */
    public function lighthouses(): array
    {
        return $this->lighthouses;
    }
}
