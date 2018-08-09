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
class Game
{
    /** @var Player $mePlayer */
    private $mePlayer;

    /** @var int $playerCount */
    private $playerCount;

    /** @var MapInterface $map */
    private $map;

    /** @var int $score */
    private $score = 0;

    /**
     * Game constructor.
     *
     * @param Player       $player
     * @param int          $playerCount
     * @param MapInterface $map
     */
    public function __construct(Player $player, int $playerCount, MapInterface $map)
    {
        $this->mePlayer = $player;
        $this->playerCount = $playerCount;
        $this->map = $map;
    }

    /**
     * @param Player       $player
     * @param int          $playerCount
     * @param MapInterface $map
     *
     * @return Game
     */
    public static function build(Player $player, int $playerCount, MapInterface $map): self
    {
        return new self($player, $playerCount, $map);
    }

    /**
     * @param RoundMessage $gameMessage
     */
    public function update(RoundMessage $gameMessage): void
    {
        $this->setScore($gameMessage->score());
        $this->updatePlayerEnergy($gameMessage->energy());

        $position = Position::buildFromArray($gameMessage->position());

        $this->updatePlayerPosition($position);
        $this->map->updateView($position, $gameMessage->view());
        $this->map->updateLighthouses($gameMessage->lighthouses());
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    /**
     * @return Player
     */
    public function mePlayer(): Player
    {
        return $this->mePlayer;
    }

    /**
     * @param int $energy
     */
    public function updatePlayerEnergy(int $energy): void
    {
        $this->mePlayer->setEnergy($energy);
    }

    public function updatePlayerPosition(Position $position): void
    {
        $this->mePlayer->setPosition($position);
    }

    /**
     * @return int
     */
    public function playerCount(): int
    {
        return $this->playerCount;
    }

    /**
     * @return MapInterface
     */
    public function map(): MapInterface
    {
        return $this->map;
    }
}
