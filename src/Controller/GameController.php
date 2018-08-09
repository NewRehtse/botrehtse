<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Controller;

use App\Application\Service\AStarStrategy;
use App\Application\Service\MaxEnergySquareStrategy;
use App\Application\Service\TriangularAStarStrategy;
use App\Domain\GameMessage;
use App\Domain\GameResponse;
use App\Domain\InitializationMessage;
use App\Domain\LoggerInterface;
use App\Domain\ResponseMessage;
use App\Domain\RoundMessage;
use App\Domain\StrategyInterface;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class GameController
{
    public const VALID_STRATEGIES = [
        'maxEnergySquare' => MaxEnergySquareStrategy::class,
        'AStar' => AStarStrategy::class,
        'triangularAStar' => TriangularAStarStrategy::class,
    ];

    /** @var string */
    private const DEFAULT_NAME = 'BartoloBot';

    /** @var StrategyInterface */
    private $strategy;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var ResponseMessage|null */
    private $previousResponse = null;

    /**
     * GameController constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
    }

    /**
     * @param string $robot
     * @param string $strategy
     */
    public function initialize(string $robot, string $strategy): void
    {
        $strategyObject = $this->strategy($strategy);

        $contents = \fgets(STDIN);
        $gameMessage = InitializationMessage::buildFromString($contents);

        $robotName = $robot ?: static::DEFAULT_NAME;
        $this->logger->setHeader($robotName);

        $strategyObject->initializeGame($gameMessage, $robotName);

        $response = GameResponse::build(['name' => $robot]);

        $this->renderResponse($response);
    }

    /**
     * @param string $strategy
     *
     * @return StrategyInterface
     */
    private function strategy(string $strategy): StrategyInterface
    {
        if (\array_key_exists($strategy, static::VALID_STRATEGIES)) {
            $class = static::VALID_STRATEGIES[$strategy];
            $this->strategy = new $class($this->logger);
        }

        if (null === $this->strategy) {
            $this->strategy = new AStarStrategy($this->logger);
        }

        return $this->strategy;
    }

    /**
     * Run the game.
     */
    public function run(): void
    {
        while ($contents = \fgets(STDIN)) {
            if (\feof(STDIN)) {
                $this->logger->logError('FIN');
                break;
            }

            $this->logger->logError('Mensaje: '.$contents);

            $gameMessage = GameMessage::build($contents);
            $message = $gameMessage->message();
            $response = null;

            if (isset($message['error'])) {
                $this->logger->logError('Error JSON: '.$message['error'].' original message: '.$message['original_msg']);
            }

            if (!isset($message['success'])) {
                $roundMessage = RoundMessage::buildFromArray($message);
                if (null !== $this->previousResponse) {
                    $roundMessage->setPreviousMessage($this->previousResponse);
                }
                $responseArray = $this->strategy->strategy($roundMessage);
                $response = GameResponse::build($responseArray);
            }

            if (isset($message['success'])) {
                $this->previousResponse = ResponseMessage::buildFromArray($message);

                $this->logger->logError((string) $this->previousResponse);
            }

            if (null !== $response) {
                $this->logger->logError('Respuesta '.$response->response());
                $this->renderResponse($response);
            }
        }
    }

    /**
     * @param GameResponse $response
     */
    private function renderResponse(GameResponse $response): void
    {
        \fwrite(STDOUT, $response->response());
    }
}
