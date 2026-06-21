<?php
require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ . '/../Helpers/Response.php';

/**
 * Verifica que la request traiga un token válido en el header Authorization.
 * Formato esperado: Authorization: Bearer <token>
 *
 * Se exige solo en los endpoints que modifican datos (POST y PUT),
 * tal como pide la consigna.
 */
class AuthMiddleware
{
    public static function handle(): void
    {
        $headers = self::getAuthHeader();

        if (!$headers || stripos($headers, 'Bearer ') !== 0) {
            Response::unauthorized('Falta el header Authorization: Bearer <token>');
        }

        $token = trim(substr($headers, 7));

        if ($token === '') {
            Response::unauthorized('Token vacío');
        }

        $db = Database::getConnection();
        $query = $db->prepare('SELECT id_token FROM api_token WHERE token = ?');
        $query->execute([$token]);

        if ($query->rowCount() === 0) {
            Response::unauthorized('Token inválido');
        }
    }

    private static function getAuthHeader(): ?string
    {
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            foreach ($headers as $name => $value) {
                if (strcasecmp($name, 'Authorization') === 0) {
                    return $value;
                }
            }
        }

        return null;
    }
}
