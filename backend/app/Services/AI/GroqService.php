<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
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
            throw new RuntimeException('Groq API key is not configured.');
        }

        if (empty($model)) {
            throw new RuntimeException('Groq model is not configured.');
        }

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0,
        ], $options);

        try {
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->timeout(60)
                ->post(
                    rtrim($baseUrl, '/') . '/chat/completions',
                    $payload
                );

            if ($response->failed()) {
                logger()->error('Groq API Error Details', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new RuntimeException(
                    'Groq API request failed with status '
                        . $response->status()
                );
            }

            $content = $response->json('choices.0.message.content');

            if (! is_string($content) || trim($content) === '') {
                throw new RuntimeException(
                    'Groq returned an empty response.'
                );
            }

            return $content;
        } catch (RequestException $e) {
            throw new RuntimeException(
                'Groq API request failed: ' . $e->getMessage(),
                previous: $e
            );
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
}
