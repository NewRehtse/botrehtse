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
interface StrategyInterface
{
    /**
     * @return Game
     */
    public function game(): Game;

    /**
     * @param InitializationMessage $message
     * @param string                $robotName
     */
    public function initializeGame(InitializationMessage $message, string $robotName = ''): void;

    /**
     * @param RoundMessage $gameMessage
     *
     * @return array
     */
    public function strategy(RoundMessage $gameMessage): array;
}
