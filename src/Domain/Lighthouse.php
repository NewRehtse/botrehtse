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
class Lighthouse
{
    private const NO_OWNER = -1;

    /** @var Position $position */
    private $position;

    /** @var bool $key */
    private $key;

    /** @var int $energy */
    private $energy = 0;

    /** @var Position[] $connections */
    private $connections;

    /** @var int $owner */
    private $owner;

    /**
     * Lighthouse constructor.
     *
     * @param Position $position
     */
    public function __construct(Position $position)
    {
        $this->position = $position;
        $this->owner = static::NO_OWNER;
        $this->connections = [];
    }

    /**
     * @param Position $position
     *
     * @return Lighthouse
     */
    public static function build(Position $position): self
    {
        return new self($position);
    }

    /**
     * @param int $owner
     */
    public function setOwner(int $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return int
     */
    public function owner(): int
    {
        return $this->owner;
    }

    /**
     * @return Position
     */
    public function position(): Position
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function hasKey(): bool
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function energy(): int
    {
        return $this->energy;
    }

    /**
     * @return Position[]
     */
    public function connections(): array
    {
        return $this->connections;
    }

    /**
     * @param int $energy
     */
    public function setEnergy(int $energy): void
    {
        $this->energy = $energy;
    }

    /**
     * @param bool $hasKey
     */
    public function setHasKey(bool $hasKey): void
    {
        $this->key = $hasKey;
    }

    /**
     * @param Position $connection
     */
    public function addConnections(Position $connection): void
    {
        $this->connections[] = $connection;
    }

    /**
     * @param Position $connection
     */
    public function removeConnection(Position $connection): void
    {
        $this->connections = \array_diff($this->connections, [$connection]);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'Owner: '.$this->owner.', Energy: '.$this->energy.', HasKey: '.($this->hasKey ? 'yes' : 'no');
    }
}
