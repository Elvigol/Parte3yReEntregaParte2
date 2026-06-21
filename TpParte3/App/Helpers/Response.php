<?php

class Response
{
    public static function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function ok($data): void
    {
        self::json($data, 200);
    }

    public static function created($data): void
    {
        self::json($data, 201);
    }

    public static function badRequest(string $message): void
    {
        self::json(['error' => $message], 400);
    }

    public static function notFound(string $message = 'Recurso no encontrado'): void
    {
        self::json(['error' => $message], 404);
    }

    public static function unauthorized(string $message = 'Token inválido o ausente'): void
    {
        self::json(['error' => $message], 401);
    }
}
