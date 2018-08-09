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
class ResponseMessage
{
    /** @var bool $success */
    private $success;

    /** @var string $message */
    private $message;

    /**
     * ResponseMessage constructor.
     *
     * @param bool   $success
     * @param string $message
     */
    public function __construct(bool  $success, string $message = '')
    {
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * @param array $msg
     *
     * @return ResponseMessage
     */
    public static function buildFromArray(array $msg): self
    {
        $success = $msg['success'];
        $message = $msg['message'] ?? '';

        return new self($success, $message);
    }

    /**
     * @param string $msg
     *
     * @return ResponseMessage
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
     * @return bool
     */
    public function success(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $text = 'Exito';

        if (!$this->success()) {
            $text = 'Fail: '.$this->message();
        }

        return $text;
    }
}
