<?php

declare(strict_types=1);

namespace Matchory\Herodot\Exceptions;

use JetBrains\PhpStorm\Pure;
use Matchory\Herodot\Contracts\Endpoint;
use RuntimeException;
use Stringable;
use Throwable;

final class PrinterException extends RuntimeException
{
    private ?Endpoint $endpoint;

    /**
     * @param string|Stringable $message
     * @param Endpoint|null     $endpoint
     * @param Throwable|null    $previous
     *
     * @noinspection PhpMissingParamTypeInspection
     */
    public function __construct(
        $message,
        ?Endpoint $endpoint = null,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            (string)$message,
            0,
            $previous
        );

        $this->endpoint = $endpoint;
    }

    #[Pure] public function getEndpoint(): ?Endpoint
    {
        return $this->endpoint;
    }
}
