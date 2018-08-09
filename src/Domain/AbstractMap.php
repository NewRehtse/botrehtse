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
abstract class AbstractMap implements MapInterface
{
    /**
     * @return array
     */
    public static function energyFromMatrixFunctions(): array
    {
        return [
            [
                function ($x, $y) { return [$x - 3, $y - 3]; },
                function ($x, $y) { return [$x - 2, $y - 3]; },
                function ($x, $y) { return [$x - 1, $y - 3]; },
                function ($x, $y) { return [$x, $y - 3]; },
                function ($x, $y) { return [$x + 1, $y - 3]; },
                function ($x, $y) { return [$x + 2, $y - 3]; },
                function ($x, $y) { return [$x + 3, $y - 3]; },
            ],
            [
                function ($x, $y) { return [$x - 3, $y - 2]; },
                function ($x, $y) { return [$x - 2, $y - 2]; },
                function ($x, $y) { return [$x - 1, $y - 2]; },
                function ($x, $y) { return [$x, $y - 2]; },
                function ($x, $y) { return [$x + 1, $y - 2]; },
                function ($x, $y) { return [$x + 2, $y - 2]; },
                function ($x, $y) { return [$x + 3, $y - 2]; },
            ],
            [
                function ($x, $y) { return [$x - 3, $y - 1]; },
                function ($x, $y) { return [$x - 2, $y - 1]; },
                function ($x, $y) { return [$x - 1, $y - 1]; },
                function ($x, $y) { return [$x, $y - 1]; },
                function ($x, $y) { return [$x + 1, $y - 1]; },
                function ($x, $y) { return [$x + 2, $y - 1]; },
                function ($x, $y) { return [$x + 3, $y - 1]; },
            ],
            [
                function ($x, $y) { return [$x - 3, $y]; },
                function ($x, $y) { return [$x - 2, $y]; },
                function ($x, $y) { return [$x - 1, $y]; },
                function ($x, $y) { return [$x, $y]; },
                function ($x, $y) { return [$x + 1, $y]; },
                function ($x, $y) { return [$x + 2, $y]; },
                function ($x, $y) { return [$x + 3, $y]; },
            ],
            [
                function ($x, $y) { return [$x - 3, $y + 1]; },
                function ($x, $y) { return [$x - 2, $y + 1]; },
                function ($x, $y) { return [$x - 1, $y + 1]; },
                function ($x, $y) { return [$x, $y + 1]; },
                function ($x, $y) { return [$x + 1, $y + 1]; },
                function ($x, $y) { return [$x + 2, $y + 1]; },
                function ($x, $y) { return [$x + 3, $y + 1]; },
            ],
            [
                function ($x, $y) { return [$x - 3, $y + 2]; },
                function ($x, $y) { return [$x - 2, $y + 2]; },
                function ($x, $y) { return [$x - 1, $y + 2]; },
                function ($x, $y) { return [$x, $y + 2]; },
                function ($x, $y) { return [$x + 1, $y + 2]; },
                function ($x, $y) { return [$x + 2, $y + 2]; },
                function ($x, $y) { return [$x + 3, $y + 2]; },
            ],
            [
                function ($x, $y) { return [$x - 3, $y + 3]; },
                function ($x, $y) { return [$x - 2, $y + 3]; },
                function ($x, $y) { return [$x - 1, $y + 3]; },
                function ($x, $y) { return [$x, $y + 3]; },
                function ($x, $y) { return [$x + 1, $y + 3]; },
                function ($x, $y) { return [$x + 2, $y + 3]; },
                function ($x, $y) { return [$x + 3, $y + 3]; },
            ],
        ];
    }
}
