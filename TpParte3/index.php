<?php

require_once __DIR__ . '/App/Helpers/Response.php';
require_once __DIR__ . '/App/Controllers/CuotaController.php';
require_once __DIR__ . '/App/Controllers/PlanController.php';
require_once __DIR__ . '/App/Middlewares/AuthMiddleware.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Soporta tanto PATH_INFO (rewrite) como ?url= como fallback
$path = $_SERVER['PATH_INFO'] ?? ($_GET['url'] ?? '/');
$path = '/' . trim(parse_url($path, PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$segments = array_values(array_filter(explode('/', $path)));

// Se espera: /api/{recurso}[/{id}]
if (count($segments) < 2 || $segments[0] !== 'api') {
    Response::notFound('Ruta no encontrada. Use /api/cuotas o /api/planes');
}

$recurso = $segments[1];
$id = $segments[2] ?? null;

try {
    switch ($recurso) {
        case 'cuotas':
            $controller = new CuotaController();

            if ($method === 'GET' && $id === null) {
                $controller->index();
            } elseif ($method === 'GET' && $id !== null) {
                validarId($id);
                $controller->show((int) $id);
            } elseif ($method === 'POST' && $id === null) {
                AuthMiddleware::handle();
                $controller->store();
            } elseif ($method === 'PUT' && $id !== null) {
                validarId($id);
                AuthMiddleware::handle();
                $controller->update((int) $id);
            } else {
                Response::badRequest('Método o ruta no soportada para /api/cuotas');
            }
            break;

        case 'planes':
            $controller = new PlanController();

            if ($method === 'GET' && $id === null) {
                $controller->index();
            } elseif ($method === 'GET' && $id !== null) {
                validarId($id);
                $controller->show((int) $id);
            } else {
                Response::badRequest('Método o ruta no soportada para /api/planes');
            }
            break;

        default:
            Response::notFound("Recurso \"$recurso\" no existe");
    }
} catch (PDOException $e) {
    Response::json(['error' => 'Error de base de datos', 'detalle' => $e->getMessage()], 400);
}

function validarId($id): void
{
    if (!is_numeric($id)) {
        Response::badRequest('El id debe ser numérico');
    }
}
