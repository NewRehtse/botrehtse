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
interface LoggerInterface
{
    /**
     * @param string $error
     */
    public function logError(string $error): void;

    /**
     * @param string $header
     */
    public function setHeader(string $header): void;
}
