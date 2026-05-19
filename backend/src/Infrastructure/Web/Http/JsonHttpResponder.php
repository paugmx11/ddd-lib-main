<?php

declare(strict_types=1);

namespace App\Infrastructure\Web\Http;

trait JsonHttpResponder
{
    /**
     * @return array<string, mixed>|null
     */
    protected function readJsonBody(): ?array
    {
        // Allow tests (or specialized environments) to inject a request body without relying on php://input.
        // This keeps controllers testable while production continues to read from php://input.
        $raw = (string) ($_SERVER['MOCK_JSON_BODY'] ?? '');
        if ($raw === '') {
            $raw = (string) file_get_contents('php://input');
        }
        if ($raw === '') {
            $this->jsonResponse(['error' => 'Empty request body'], 400);
            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $this->jsonResponse(['error' => 'Invalid JSON body'], 400);
            return null;
        }

        /** @var array<string, mixed> $data */
        return $data;
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $payload
     */
    protected function jsonResponse(array $payload, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    }
}
