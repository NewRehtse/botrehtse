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
class GameMessage
{
    /** @var array */
    private $message;

    /**
     * GameMessage constructor.
     *
     * @param string $msg
     */
    public function __construct(string $msg)
    {
        $message = \json_decode($msg, true);

        $this->message = $message ?: [
            'error' => \json_last_error_msg(),
            'original_msg' => $msg,
        ];
    }

    /**
     * @param string $msg
     *
     * @return GameMessage
     */
    public static function build(string $msg): self
    {
        return new self($msg);
    }

    /**
     * @return array
     */
    public function message(): array
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \json_encode($this->message);
    }
}
