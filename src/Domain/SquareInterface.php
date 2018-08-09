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
interface SquareInterface
{
    /**
     * @param Lighthouse $lighthouse
     */
    public function setLighthouse(Lighthouse $lighthouse): void;

    /**
     * @param int $energy
     */
    public function setEnergy(int $energy): void;

    /**
     * @param int $amount
     */
    public function increaseEnergy(int $amount): void;

    /**
     * @return Position
     */
    public function position(): Position;

    /**
     * @return int
     */
    public function energy(): int;

    /**
     * @return Lighthouse|null
     */
    public function lighthouse(): ?Lighthouse;

    /**
     * @return bool
     */
    public function isOutsideBoard(): bool;
}
