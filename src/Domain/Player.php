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
class Player
{
    /** @var int */
    private const MAX_ENERGY = 0;

    /** @var int $id */
    private $id;

    /** @var string $name */
    private $name;

    /** @var int $energy */
    private $energy = 0;

    /** @var Position $position */
    private $position;

    /** @var string $lighthouseKey */
    private $lighthouseKey = '';

    /**
     * Player constructor.
     *
     * @param int      $id
     * @param Position $position
     * @param string   $name
     */
    public function __construct(int $id, Position $position, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->position = $position;
    }

    /**
     * @param int      $id
     * @param Position $position
     * @param string   $name
     *
     * @return Player
     */
    public static function build(int $id, Position $position, string $name): self
    {
        return new self($id, $position, $name);
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
     * @param Position $position
     */
    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function energy(): int
    {
        return $this->energy;
    }

    /**
     * @return Position
     */
    public function position(): Position
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function lighthouseKey(): string
    {
        return $this->lighthouseKey;
    }
}
