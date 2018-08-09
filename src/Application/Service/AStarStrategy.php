<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Application\Service;

use App\Domain\AStar\Map;
use App\Domain\AStar\Node;
use App\Domain\Game;
use App\Domain\InitializationMessage;
use App\Domain\Lighthouse;
use App\Domain\LoggerInterface;
use App\Domain\Player;
use App\Domain\Position;
use App\Domain\RoundMessage;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class AStarStrategy extends AbstractStrategy
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var null|Lighthouse $selectedGoal */
    private $selectedGoal = null;

    /** @var array $selectedPath */
    private $selectedPath = [];

    /** @var AStarAlgorithm */
    private $algorithm;

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

        $this->algorithm = new AStarAlgorithm($map);
        $game = Game::build($player, $gameMessage->playerCount(), $map);

        $this->setGame($game);
    }

    /**
     * @inheritdoc
     */
    public function runStrategy(RoundMessage $gameMessage): array
    {
        $currentPosition = $this->game()->mePlayer()->position();

        /** @var Map $map */
        $map = $this->game()->map();

        /** @var Node $currentNode */
        $currentNode = $map->getSquare($currentPosition);

        /** @var Lighthouse $nodeLighthouse */
        $nodeLighthouse = $currentNode->lighthouse();

        //Estemos en un faro y tenemos energía para atacar
        //Estemos en un faro y no tenemos energía para atacar
        //No estemos en un faro y no tengamos camino decidido
        //No tengamos suficiente energía para atacar ningún faro
        //Estemos de camino a un faro y sigamos teniendo energía para atacarlo
        //Estemos de camino a un faro y ya no tengamos energía para atacarlo
        //LLeguemos a un faro y ya no tengamos energía para atacarlo

        $player = $this->game()->mePlayer();

        $inLighthouse = null !== $nodeLighthouse;
        $lighthouseWithKey = $map->getLighthouseWithKey();
        $playerId = $player->id();

        $previousMessageSucceded = (null === $gameMessage->previousMessage() ||
            (null !== $gameMessage->previousMessage() && $gameMessage->previousMessage()->success()));

        $canIConnect = $inLighthouse && $previousMessageSucceded &&
            null !== $lighthouseWithKey &&
            $nodeLighthouse !== $lighthouseWithKey &&
            $playerId === $nodeLighthouse->owner() &&
            $playerId === $lighthouseWithKey->owner();

        $canIAttack = $inLighthouse &&
            $player->id() !== $nodeLighthouse->owner() &&
            $player->energy() >= $this->minimunEnergyToAtack() &&
            $this->calculateEnergyToAttack() > $nodeLighthouse->energy();

        $noEnergyShouldIMove = $inLighthouse && $player->id() !== $nodeLighthouse->owner();

        $doIHaveAPath = !empty($this->selectedPath) && null !== $this->selectedGoal &&
            $player->id() !== $this->selectedGoal->owner() &&
            $this->calculateEnergyToAttack() > $this->selectedGoal->energy();

        //Si estamos en un faro y somos dueños y tenemos clave de un faro diferente intentamos conectar
        //Y además, el mensaje anterior fallo, porque sino es posible que intenemos conectar for ever
        if ($canIConnect) {
            //Vamos a intentar conectar
            return $this->connect($map->getLighthouseWithKey()->position());
        }

        if ($canIAttack) {
            //Estamos en un faro y podemos atacar si tenemos energía
            $energy = $this->calculateEnergyToAttack();

            return $this->attack($energy);
        }

        if ($noEnergyShouldIMove) {
            //Estamos en un faro y no tenemos energía suficiente, nos movemos a la que más cercana este más potente
            $selectedPosition = $map->positionMaxEnergy();

            return $this->makeMove($currentPosition, $selectedPosition);
        }

        //Por defecto, nos movemos a la que más energía tenga
        $selectedPosition = $map->positionMaxEnergy();

        //No estamos en un faro
        if ($doIHaveAPath) {
            //Estamos de camino a un faro y tenemos energía para atacar
            $selectedPosition = $this->selectNextPositionInMap();

            return $this->makeMove($currentPosition, $selectedPosition);
        }

        if (!empty($this->selectedPath)) {
            //Estamos de camino a un faro pero ya no tenemos energía para atacar
            $this->selectedPath = [];
        }

        //Buscamos si tenemos faro que podamos atacar
        $this->selectedGoal = $this->getNextLighthouse();

        if (null !== $this->selectedGoal) {
            $this->selectedPath = $this->generatePathToNode();
            $selectedPosition = $this->selectNextPositionInMap();
        }

        //No tenemos objetivo seleccionado
        return $this->makeMove($currentPosition, $selectedPosition);
    }

    /**
     * @return array
     */
    private function generatePathToNode(): array
    {
        $currentPosition = $this->game()->mePlayer()->position();

        /** @var Map $map */
        $map = $this->game()->map();

        /** @var Node $currentNode */
        $currentNode = $map->getSquare($currentPosition);

        $this->clearParents();

        /** @var Node $playerNode */
        $playerNode = $map->getSquare($currentPosition);

        /** @var Node $selectedNode */
        $selectedNode = $map->getSquare($this->selectedGoal->position());

        $selectedPath = $this->algorithm->run($playerNode, $selectedNode);
        \array_shift($this->selectedPath); //Sacamos el current

        return $selectedPath;
    }

    /**
     * @return Lighthouse|null
     */
    private function getNextLighthouse(): ?Lighthouse
    {
        $goal = null;
        $lighthouses = $this->game()->map()->lighthouses();
        $player = $this->game()->mePlayer();
        $currentPosition = $player->position();

        /** @var Lighthouse $lighthouse */
        foreach ($lighthouses as $lighthouse) {
            //Si estamos en un faro, hay que mirar si en el que estamos tenemos la clave
            if (
                $player->id() !== $lighthouse->owner() &&
                $this->calculateEnergyToAttack() > $lighthouse->energy() &&
                $lighthouse->position()->x() !== $currentPosition->x() &&
                $lighthouse->position()->y() !== $currentPosition->y()
            ) {
                $goal = $lighthouse;
                break;
            }
        }

        return $goal;
    }

    /**
     * @return Position
     */
    private function selectNextPositionInMap(): Position
    {
        /** @var Node $selectedNode */
        $selectedNode = \array_shift($this->selectedPath);

        return $selectedNode->position();
    }

    /**
     * Clear parents in the game map so next AStarAlgorithm is empty.
     */
    private function clearParents(): void
    {
        $map = $this->game()->map();

        foreach ($map->view() as $row) {
            /** @var Node $column */
            foreach ($row as $column) {
                $column->setParent(null);
            }
        }
    }

    /**
     * @return int
     */
    private function minimunEnergyToAtack(): int
    {
        return 2000;
    }

    /**
     * @return int
     */
    protected function calculateEnergyToAttack(): int
    {
        return \abs(0.20 * $this->game()->mePlayer()->energy());
    }
}
