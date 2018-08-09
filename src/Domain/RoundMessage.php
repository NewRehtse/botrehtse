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
class RoundMessage
{
    /** @var array $position */
    private $position;

    /** @var int $score */
    private $score;

    /** @var int $energy */
    private $energy;

    /** @var array $view */
    private $view;

    /** @var array $lighthouses */
    private $lighthouses;

    /** @var ResponseMessage $previousMessage */
    private $previousMessage;

    /**
     * RoundMessage constructor.
     *
     * @param array $position
     * @param int   $score
     * @param int   $energy
     * @param array $view
     * @param array $lighthouses
     */
    public function __construct(array $position, int $score, int $energy, array $view, array $lighthouses)
    {
        $this->position = $position;
        $this->score = $score;
        $this->energy = $energy;
        $this->view = $view;
        $this->lighthouses = $lighthouses;
    }

    /**
     * @param array $msg
     *
     * @return RoundMessage
     */
    public static function buildFromArray(array $msg): self
    {
        $position = $msg['position'];
        $score = $msg['score'];
        $view = $msg['view'];
        $energy = $msg['energy'];
        $lighthouses = $msg['lighthouses'];

        return new self($position, $score, $energy, $view, $lighthouses);
    }

    /**
     * @param string $msg
     *
     * @return RoundMessage
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
     * @return array
     */
    public function position(): array
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * @return int
     */
    public function energy(): int
    {
        return $this->energy;
    }

    /**
     * @return array
     */
    public function view(): array
    {
        return $this->view;
    }

    /**
     * @return array
     */
    public function lighthouses(): array
    {
        return $this->lighthouses;
    }

    /**
     * @return ResponseMessage
     */
    public function previousMessage(): ?ResponseMessage
    {
        return $this->previousMessage;
    }

    /**
     * @param ResponseMessage $previous
     */
    public function setPreviousMessage(ResponseMessage $previous): void
    {
        $this->previousMessage = $previous;
    }
}
