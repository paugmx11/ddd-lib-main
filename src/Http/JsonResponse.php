<?php

declare(strict_types=1);

namespace App\Http;

final class JsonResponse
{
    public static function ok(array $body): array
    {
        return self::make(200, $body);
    }

    public static function created(array $body): array
    {
        return self::make(201, $body);
    }

    public static function noContent(): array
    {
        return self::make(204, null);
    }

    public static function error(int $status, string $message): array
    {
        return self::make($status, ['error' => $message]);
    }

    public static function make(int $status, ?array $body): array
    {
        return [
            'status' => $status,
            'body' => $body,
        ];
    }
}
