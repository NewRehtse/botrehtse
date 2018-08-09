<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Domain\AStar;

use App\Domain\Lighthouse;
use App\Domain\Position;
use App\Domain\SquareInterface;
use JMGQ\AStar\AbstractNode;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class Node extends AbstractNode implements SquareInterface
{
    private const MAX_ENERGY = 100;

    /** @var Position $position */
    private $position;

    /** @var bool $outsideBoard */
    private $outsideBoard;

    /** @var int $energy */
    private $energy = 0;

    /** @var null|Lighthouse $lighthouse */
    private $lighthouse;

    private $parent2;

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
     * @param Position $position
     * @param bool     $outsideBoard
     *
     * @return Square
     */
    public static function build(Position $position, $outsideBoard = false): self
    {
        return new self($position, $outsideBoard);
    }

    /**
     * @param Lighthouse $lighthouse
     */
    public function setLighthouse(Lighthouse $lighthouse): void
    {
        $this->lighthouse = $lighthouse;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(?\JMGQ\AStar\Node $parent): void
    {
        $this->parent2 = $parent;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this->parent2;
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->position()->__toString();
    }
}
