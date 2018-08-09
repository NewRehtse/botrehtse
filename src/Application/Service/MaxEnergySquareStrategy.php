<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Application\Service;

use App\Domain\Game;
use App\Domain\InitializationMessage;
use App\Domain\LoggerInterface;
use App\Domain\Map;
use App\Domain\Player;
use App\Domain\Position;
use App\Domain\RoundMessage;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class MaxEnergySquareStrategy extends AbstractStrategy
{
    private const ENERGY_ATTACK = 30;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MaxEnergySquareStrategy constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function initializeGame(InitializationMessage $gameMessage, string $robotName = ''): void
    {
        $playerPosition = Position::buildFromArray($gameMessage->position());
        $player = Player::build($gameMessage->playerNum(), $playerPosition, $robotName);

        $map = Map::buildFromArray($gameMessage->map(), $gameMessage->lighthouses());

        $game = Game::build($player, $gameMessage->playerCount(), $map);
        $this->setGame($game);
    }

    /**
     * @inheritdoc
     */
    public function runStrategy(RoundMessage $gameMessage): array
    {
        $currentPosition = $this->game()->mePlayer()->position();
        $selectedPosition = $this->game()->map()->positionMaxEnergy();

        //Si hay faro y su energia es menor que la nuestra+1, atacar con suenergia+1 sino mover

        $square = $this->game()->map()->getSquare($currentPosition);
        $player = $this->game()->mePlayer();
        $playerEnergy = $player->energy();
        $squareLighthouse = $square->lighthouse();

        if (
            null !== $squareLighthouse &&
            $playerEnergy + static::ENERGY_ATTACK > $squareLighthouse->energy() &&
            $player->id() !== $squareLighthouse->owner()
        ) {
            $squareLighthouse = $square->lighthouse();
            if (null === $squareLighthouse) {
                return $this->pass();
            }

            $energy = $this->calculateEnergyToAttack();

            return $this->attack($energy);
        }

        return $this->makeMove($currentPosition, $selectedPosition);
    }
}
