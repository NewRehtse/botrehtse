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
class GameResponse
{
    /** @var array $response */
    private $response;

    /**
     * GameResponse constructor.
     *
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * @param array $response
     *
     * @return GameResponse
     */
    public static function build(array $response): self
    {
        return new self($response);
    }

    /**
     * @return string
     */
    public function response(): string
    {
        return \json_encode($this->response).PHP_EOL;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->response();
    }
}
