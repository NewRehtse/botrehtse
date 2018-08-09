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
class Position
{
    /** @var int $x */
    private $x;

    /** @var int $y */
    private $y;

    /**
     * Position constructor.
     *
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @param int $x
     * @param int $y
     *
     * @return Position
     */
    public static function build(int $x, int $y): self
    {
        return new self($x, $y);
    }

    /**
     * @param array $message
     *
     * @return Position
     */
    public static function buildFromArray(array $message): self
    {
        return static::build(
            (int) $message[0],
            (int) $message[1]
        );
    }

    /**
     * @return int
     */
    public function x(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function y(): int
    {
        return $this->y;
    }

    /**
     * @param Position $position
     *
     * @return bool
     */
    public function isNextTo(self $position): bool
    {
        $xDifference = \abs($position->x() - $this->x());
        $yDifference = \abs($position->y() - $this->y());

        //Si estan a una celda de distancia
        return
            (1 === $xDifference || 0 === $xDifference) &&
            (1 === $yDifference || 0 === $yDifference)
        ;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return '['.$this->x.', '.$this->y.']';
    }
}
