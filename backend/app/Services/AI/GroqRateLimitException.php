<?php

namespace App\Services\AI;

use RuntimeException;

class GroqRateLimitException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $retryAfterSeconds = null,
        public readonly array $rateLimitHeaders = [],
        public readonly string $responseBody = '',
    ) {
        parent::__construct($message);
    }
}
