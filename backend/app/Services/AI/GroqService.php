<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;
use RuntimeException;
use JsonException;

class GroqService
{
    /**
     * Send a chat completion request to Groq.
     */
    public function chat(array $messages, array $options = []): string
    {
        $apiKey = config('services.groq.api_key');
        $baseUrl = config('services.groq.base_url');
        $model = config('services.groq.model');

        if (empty($apiKey)) {
            throw new RuntimeException(
                'Groq API key is not configured.'
            );
        }

        if (empty($model)) {
            throw new RuntimeException(
                'Groq model is not configured.'
            );
        }

        $modelKey = sha1($model);

        $cooldownKey = 'jobconnect:groq:cooldown:' . $modelKey;
        $lockKey = 'jobconnect:groq:request-lock:' . $modelKey;
        $lastRequestKey = 'jobconnect:groq:last-request:' . $modelKey;

        /*
         * Acquire a shared lock before checking cooldown
         * or sending a request.
         *
         * Lock expires after 75 seconds.
         * HTTP request timeout is 60 seconds.
         */
        $lock = Cache::lock($lockKey, 75);

        if (! $lock->get()) {
            throw new GroqRateLimitException(
                message: 'Another Groq request is currently in progress.',
                retryAfterSeconds: 10,
            );
        }

        try {
            /*
             * Check shared cooldown while holding the lock.
             */
            $cooldownUntil = Cache::get($cooldownKey);

            if (
                is_numeric($cooldownUntil)
                && (int) $cooldownUntil > now()->timestamp
            ) {
                $remainingSeconds = max(
                    1,
                    (int) $cooldownUntil - now()->timestamp
                );

                throw new GroqRateLimitException(
                    message: 'Groq shared cooldown is active.',
                    retryAfterSeconds: $remainingSeconds,
                );
            }

            /*
             * Remove expired cooldown if one exists.
             */
            if ($cooldownUntil !== null) {
                Cache::forget($cooldownKey);
            }

            $payload = array_merge([
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0,
            ], $options);

            /*
             * Keep at least 3 seconds between Groq request starts.
             * The shared lock ensures only one worker updates
             * this timestamp.
             */
            $minimumInterval = 3.0;

            $lastRequestAt = Cache::get($lastRequestKey);

            if (is_numeric($lastRequestAt)) {
                $elapsed = microtime(true) - (float) $lastRequestAt;
                $remaining = $minimumInterval - $elapsed;

                if ($remaining > 0) {
                    usleep((int) ceil($remaining * 1_000_000));
                }
            }

            /*
             * Record the request start time.
             */
            $requestStartedAt = microtime(true);

            Cache::put(
                $lastRequestKey,
                $requestStartedAt,
                60
            );

            /*
             * Send request to Groq API.
             */
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->timeout(60)
                ->post(
                    rtrim($baseUrl, '/') . '/chat/completions',
                    $payload
                );

            /*
             * Handle Groq rate limits.
             */
            if ($response->status() === 429) {
                $headers = [
                    'retry-after' => $response->header('Retry-After'),
                    'remaining-requests' => $response->header(
                        'x-ratelimit-remaining-requests'
                    ),
                    'remaining-tokens' => $response->header(
                        'x-ratelimit-remaining-tokens'
                    ),
                    'reset-requests' => $response->header(
                        'x-ratelimit-reset-requests'
                    ),
                    'reset-tokens' => $response->header(
                        'x-ratelimit-reset-tokens'
                    ),
                ];

                $retryAfter = $headers['retry-after'];

                $delay = is_numeric($retryAfter)
                    ? max(1, (int) $retryAfter)
                    : 60;

                $cooldownUntil = now()->timestamp + $delay;

                /*
                 * Save the shared cooldown.
                 */
                Cache::put(
                    $cooldownKey,
                    $cooldownUntil,
                    $delay
                );

                logger()->warning(
                    'Groq rate limit reached. Shared cooldown activated.',
                    [
                        'model' => $model,
                        'retry_after_seconds' => $delay,
                        'rate_limit_headers' => $headers,
                    ]
                );

                throw new GroqRateLimitException(
                    message: 'Groq rate limit exceeded.',
                    retryAfterSeconds: $delay,
                    rateLimitHeaders: $headers,
                    responseBody: $response->body(),
                );
            }

            /*
             * Handle other API errors.
             */
            if ($response->failed()) {
                logger()->error('Groq API Error Details', [
                    'status' => $response->status(),
                    'model' => $model,
                    'body' => $response->body(),
                ]);

                throw new RuntimeException(
                    'Groq API request failed with status '
                        . $response->status()
                );
            }

            /*
             * Extract response content.
             */
            $content = $response->json(
                'choices.0.message.content'
            );

            if (! is_string($content) || trim($content) === '') {
                throw new RuntimeException(
                    'Groq returned an empty response.'
                );
            }

            /*
             * Log successful Groq API request.
             *
             * Do not log API keys, applicant personal data,
             * or prompt contents.
             */
            $usage = $response->json('usage', []);

            logger()->info(
                'Groq API request completed successfully.',
                [
                    'model' => $model,
                    'status' => $response->status(),
                    'response_time_ms' => (int) round(
                        (microtime(true) - $requestStartedAt) * 1000
                    ),
                    'prompt_tokens' => $usage['prompt_tokens'] ?? null,
                    'completion_tokens' => $usage['completion_tokens'] ?? null,
                    'total_tokens' => $usage['total_tokens'] ?? null,
                ]
            );

            return $content;
        } catch (RequestException $e) {
            throw new RuntimeException(
                'Groq API request failed: ' . $e->getMessage(),
                previous: $e
            );
        } finally {
            /*
             * Always release the lock.
             */
            $lock->release();
        }
    }

    /**
     * Generate a structured JSON response from Groq.
     *
     * @throws JsonException
     */
    public function chatJson(
        array $messages,
        array $options = []
    ): array {
        $options['response_format'] = [
            'type' => 'json_object',
        ];

        $content = $this->chat($messages, $options);

        return json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }


    /**
     * Generate a strictly structured JSON response from Groq.
     *
     * @throws JsonException
     */
    public function chatStructuredJson(
        array $messages,
        array $schema,
        array $options = []
    ): array {
        $options['response_format'] = [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'applicant_evaluation',
                'strict' => true,
                'schema' => $schema,
            ],
        ];

        $content = $this->chat($messages, $options);

        return json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
