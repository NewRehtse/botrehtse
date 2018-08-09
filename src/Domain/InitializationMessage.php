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
class InitializationMessage
{
    /** @var int $playerNum */
    private $playerNum;

    /** @var int $playerCount */
    private $playerCount;

    /** @var array $position */
    private $position;

    /** @var array $map */
    private $map;

    /** @var array $lighthouses */
    private $lighthouses;

    /**
     * InitializationMessage constructor.
     *
     * @param int   $playerNum
     * @param int   $playerCount
     * @param array $position
     * @param array $map
     * @param array $lighthouses
     */
    public function __construct(int $playerNum, int $playerCount, array $position, array $map, array $lighthouses)
    {
        $this->playerNum = $playerNum;
        $this->playerCount = $playerCount;
        $this->position = $position;
        $this->map = $map;
        $this->lighthouses = $lighthouses;
    }

    /**
     * @param array $msg
     *
     * @return InitializationMessage
     */
    private static function buildFromArray(array $msg): self
    {
        $position = $msg['position'];
        $map = $msg['map'];
        $playerNum = $msg['player_num'];
        $playerCount = $msg['player_count'];
        $lighthouses = $msg['lighthouses'];

        return new self($playerNum, $playerCount, $position, $map, $lighthouses);
    }

    /**
     * @param string $msg
     *
     * @return InitializationMessage
     */
    public static function buildFromString(string $msg): self
    {
        $message = \json_decode($msg, true);

        if (null === $message) {
            throw new \InvalidArgumentException('JSON Is not well formed: '.\json_last_error_msg());
        }

        return static::buildFromArray($message);
    }

    /**
     * @return int
     */
    public function playerNum(): int
    {
        return $this->playerNum;
    }

    /**
     * @return int
     */
    public function playerCount(): int
    {
        return $this->playerCount;
    }

    /**
     * @return array
     */
    public function position(): array
    {
        return $this->position;
    }

    /**
     * @return array
     */
    public function map(): array
    {
        return $this->map;
    }

    /**
     * @return array
     */
    public function lighthouses(): array
    {
        return $this->lighthouses;
    }
}
