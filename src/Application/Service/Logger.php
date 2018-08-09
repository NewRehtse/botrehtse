<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Application\Service;

use App\Domain\LoggerInterface;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class Logger implements LoggerInterface
{
    /** @var string */
    private const DEFAULT_NAME = 'BotRehtse';

    /** @var string */
    private $header;

    /**
     * Logger constructor.
     *
     * @param string $header
     */
    public function __construct(string $header = self::DEFAULT_NAME)
    {
        $this->header = $header;
    }

    /**
     * @param string $error
     */
    public function logError(string $error): void
    {
        \fwrite(STDERR, '['.$this->header.'] '.$error.PHP_EOL);
    }

    /**
     * @param string $header
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }
}
