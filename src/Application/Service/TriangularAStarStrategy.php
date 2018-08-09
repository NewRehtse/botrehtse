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
use App\Domain\Player;
use App\Domain\Position;
use App\Domain\RoundMessage;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class TriangularAStarStrategy extends AbstractStrategy
{
    /** @var AStarAlgorithm $algorithm */
    private $algorithm;

    /** @var array $distances */
    private $distances;

    /** @var null|Lighthouse $selectedGoal */
    private $selectedGoal = null;

    /** @var array $selectedPath */
    private $selectedPath = [];

    /**
     * @param InitializationMessage $message
     * @param string                $robotName
     */
    public function initializeGame(InitializationMessage $message, string $robotName = ''): void
    {
        $playerPosition = Position::buildFromArray($message->position());
        $player = Player::build($message->playerNum(), $playerPosition, $robotName);

        $map = Map::buildFromArray($message->map(), $message->lighthouses());

        $this->algorithm = new AStarAlgorithm($map);
        $game = Game::build($player, $message->playerCount(), $map);
        $this->distances = $this->calculateDistancesBetweenLighthouses($map);

        $this->setGame($game);
    }

    /**
     * @param Map $map
     *
     * @return array
     */
    private function calculateDistancesBetweenLighthouses(Map $map): array
    {
        $distances = [];
        $lighthouses = $map->lighthouses();

        /** @var Lighthouse $lighthouse */
        foreach ($lighthouses as $lighthouse) {
            /** @var Node $lighthouseNode */
            $lighthouseNode = $map->getSquare($lighthouse->position());
            foreach ($lighthouses as $lighthouse2) {
                /** @var Node $lighthouse2Node */
                $lighthouse2Node = $map->getSquare($lighthouse2->position());
                $distances[$lighthouseNode->getId()] = $this->algorithm->calculateEstimatedCost($lighthouseNode, $lighthouse2Node);
            }
        }

        return $distances;
    }

    /**
     * @param RoundMessage $gameMessage
     *
     * @return array
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

        if ($inLighthouse) {
            $response = $this->inLighthouse($gameMessage);

            if (!empty($response)) {
                return $response;
            }
        }

        $doIHaveAPath = !empty($this->selectedPath) && null !== $this->selectedGoal &&
            $player->id() !== $this->selectedGoal->owner() &&
            $this->calculateEnergyToAttack() > $this->selectedGoal->energy();

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

    private function inLighthouse(RoundMessage $gameMessage): array
    {
        /** @var Map $map */
        $map = $this->game()->map();
        $player = $this->game()->mePlayer();
        $lighthouseWithKey = $map->getLighthouseWithKey();
        $currentPosition = $player->position();
        $playerId = $player->id();

        /** @var Node $currentNode */
        $currentNode = $map->getSquare($currentPosition);

        /** @var Lighthouse $nodeLighthouse */
        $nodeLighthouse = $currentNode->lighthouse();

        $previousMessageSucceded = (null === $gameMessage->previousMessage() ||
            (null !== $gameMessage->previousMessage() && $gameMessage->previousMessage()->success()));

        $canIConnect = $previousMessageSucceded &&
            null !== $lighthouseWithKey &&
            $nodeLighthouse !== $lighthouseWithKey &&
            $playerId === $nodeLighthouse->owner() &&
            $playerId === $lighthouseWithKey->owner();

        $canIAttack = $player->id() !== $nodeLighthouse->owner() &&
            $player->energy() >= $this->minimunEnergyToAtack() &&
            $this->calculateEnergyToAttack() > $nodeLighthouse->energy();

        $noEnergyShouldIMove = $player->id() !== $nodeLighthouse->owner();

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

        return [];
    }

    /**
     * @param Position $position
     *
     * @return array
     */
    private function getLighthousesOrderedByDistance(Position $position): array
    {
        $map = $this->game()->map();
        $lighthouses = $map->lighthouses();
        $ordered = [];

        /** @var Node $node */
        $node = $map->getSquare($position);

        /** @var Lighthouse $lighthouse */
        foreach ($lighthouses as $lighthouse) {
            /** @var Node $lighthouseNode */
            $lighthouseNode = $map->getSquare($lighthouse->position());
            $ordered[$lighthouseNode->getId()] = $this->algorithm->calculateEstimatedCost($node, $lighthouseNode);
        }

        \asort($ordered);

        $lighthouses = [];

        foreach ($ordered as $key => $distance) {
            $positionArray = \explode(',', $key);
            $position = Position::buildFromArray($positionArray);
            $nodeLighthouse = $map->getSquare($position);
            if (null !== $nodeLighthouse->lighthouse()) {
                $lighthouses[] = $nodeLighthouse->lighthouse();
            }
        }

        return $lighthouses;
    }

    /**
     * @return array
     */
    private function generatePathToNode(): array
    {
        $currentPosition = $this->game()->mePlayer()->position();

        /** @var Map $map */
        $map = $this->game()->map();

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
        $player = $this->game()->mePlayer();
        $currentPosition = $player->position();
        $lighthouses = $this->getLighthousesOrderedByDistance($currentPosition);

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

    private function getLigthousesOrderedByDistance(): array
    {
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
