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
class Square extends AbstractSquare
{
    /**
     * @param Position $position
     * @param bool     $outsideBoard
     *
     * @return Square
     */
    public static function build(Position $position, $outsideBoard = false): self
    {
        return new self($position, $outsideBoard);
    }
}
