<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Application\Service;

use App\Domain\Game;
use App\Domain\RoundMessage;
use App\Domain\StrategyInterface;
use App\Domain\Position;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
abstract class AbstractStrategy implements StrategyInterface
{
    private const ENERGY_ATTACK = 30;

    /** @var Game $game */
    private $game;

    /**
     * @inheritdoc
     */
    final public function strategy(RoundMessage $gameMessage): array
    {
        $this->game()->update($gameMessage);

        return $this->runStrategy($gameMessage);
    }

    /**
     * @param RoundMessage $gameMessage
     *
     * @return array
     */
    abstract protected function runStrategy(RoundMessage $gameMessage): array;

    /**
     * @param int $energy
     *
     * @return array
     */
    protected function attack(int $energy): array
    {
        return [
            'command' => 'attack',
            'energy' => $energy,
        ];
    }

    /**
     * @return array
     */
    protected function pass(): array
    {
        return ['command' => 'pass'];
    }

    protected function connect(Position $lighthousePosition): array
    {
        return [
            'command' => 'connect',
            'destination' => [$lighthousePosition->x(), $lighthousePosition->y()],
        ];
    }

    /**
     * @param Position      $currentPosition
     * @param null|Position $selectedPosition
     *
     * @return array
     */
    protected function makeMove(Position $currentPosition, ?Position $selectedPosition): array
    {
        if (null === $selectedPosition) {
            try {
                $x = \random_int(-1, 1);
                $y = \random_int(-1, 1);

                return [
                    'command' => 'move',
                    'x' => $x,
                    'y' => $y,
                ];
            } catch (\Exception $e) {
                return ['command' => 'pass'];
            }
        }

        $position = static::getDirection($currentPosition, $selectedPosition);

        $this->game()->updatePlayerPosition($selectedPosition);

        $response = [
            'command' => 'move',
            'x' => $position['x'],
            'y' => $position['y'],
        ];

        return $response;
    }

    /**
     * @param Position $currentPosition
     * @param Position $selectedPosition
     *
     * @return array
     */
    private static function getDirection(Position $currentPosition, Position $selectedPosition): array
    {
        if ($currentPosition->x() === $selectedPosition->x()) {
            $x = 0;
        }

        if ($currentPosition->y() === $selectedPosition->y()) {
            $y = 0;
        }

        if ($currentPosition->x() < $selectedPosition->x()) {
            $x = 1;
        }

        if ($currentPosition->x() > $selectedPosition->x()) {
            $x = -1;
        }

        if ($currentPosition->y() < $selectedPosition->y()) {
            $y = 1;
        }

        if ($currentPosition->y() > $selectedPosition->y()) {
            $y = -1;
        }

        return ['x' => $x, 'y' => $y];
    }

    /**
     * @return int
     */
    protected function calculateEnergyToAttack(): int
    {
        return $this->game()->mePlayer()->energy();
    }

    /**
     * @return Game
     */
    public function game(): Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame(Game $game): void
    {
        $this->game = $game;
    }
}
