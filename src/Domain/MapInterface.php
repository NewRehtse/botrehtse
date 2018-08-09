<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Domain;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
interface MapInterface
{
    /**
     * @param array $lighthouses
     */
    public function updateLighthouses(array $lighthouses): void;

    /**
     * @param Position $position
     *
     * @return SquareInterface
     */
    public function getSquare(Position $position): SquareInterface;

    /**
     * @param Position $currentPosition
     * @param array    $energyMatrix
     */
    public function updateView(Position $currentPosition, array $energyMatrix): void;

    /**
     * @return Position|null
     */
    public function positionMaxEnergy(): ?Position;

    /**
     * @return array
     */
    public function view(): array;

    /**
     * @return array
     */
    public function lighthouses(): array;
}
