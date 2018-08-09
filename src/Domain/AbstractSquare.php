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
abstract class AbstractSquare implements SquareInterface
{
    public const MAX_ENERGY = 100;

    /** @var Position $position */
    private $position;

    /** @var bool $outsideBoard */
    private $outsideBoard;

    /** @var int $energy */
    private $energy = 0;

    /** @var null|Lighthouse $lighthouse */
    private $lighthouse;

    /**
     * Square constructor.
     *
     * @param Position $position
     * @param bool     $outsideBoard
     */
    public function __construct(Position $position, $outsideBoard = false)
    {
        $this->position = $position;
        $this->outsideBoard = $outsideBoard;
    }

    /**
     * @param Lighthouse $lighthouse
     */
    public function setLighthouse(Lighthouse $lighthouse): void
    {
        $this->lighthouse = $lighthouse;
    }

    /**
     * @param int $energy
     */
    public function setEnergy(int $energy): void
    {
        if (0 !== static::MAX_ENERGY && $energy > static::MAX_ENERGY) {
            throw new \InvalidArgumentException('Max energy already.');
        }

        $this->energy = $energy;
    }

    /**
     * @param int $amount
     */
    public function increaseEnergy(int $amount): void
    {
        $energy = $this->energy + $amount;

        $this->setEnergy($energy);
    }

    /**
     * @return Position
     */
    public function position(): Position
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function energy(): int
    {
        return $this->energy;
    }

    /**
     * @return Lighthouse|null
     */
    public function lighthouse(): ?Lighthouse
    {
        return $this->lighthouse;
    }

    /**
     * @return bool
     */
    public function isOutsideBoard(): bool
    {
        return $this->outsideBoard;
    }
}
