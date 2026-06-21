<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/App/Middlewares/SessionMiddleware.php';
require_once __DIR__ . '/App/controllers/PlanController.php';
require_once __DIR__ . '/App/controllers/CuotaController.php';
require_once __DIR__ . '/App/controllers/ClienteController.php';
require_once __DIR__ . '/App/controllers/AuthController.php';

define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

$action = 'home';
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$params = explode('/', $action);

$req = new StdClass();
SessionMiddleware::loadUser($req);

switch ($params[0]) {

    // ---------- Home / público ----------
    case 'home':
        $controller = new PlanController($req);
        $controller->showAllPlanes();
        break;

    // ---------- (B) Categorías (Plan de Cuotas) ----------
    case 'categorias':
        $controller = new PlanController($req);
        $controller->showAllPlanes();
        break;

    case 'categoria':
        if (isset($params[1])) {
            $controller = new PlanController($req);
            $controller->showPlanById($params[1]);
        } else {
            echo "Falta especificar el ID de la categoría.";
        }
        break;

    case 'nueva-categoria':
        $controller = new PlanController($req);
        $controller->showFormularioAlta();
        break;

    case 'add-categoria':
        $controller = new PlanController($req);
        $controller->add();
        break;

    case 'editar-categoria':
        if (isset($params[1])) {
            $controller = new PlanController($req);
            $controller->showFormularioEditar($params[1]);
        } else {
            echo "Falta especificar el ID de la categoría a editar.";
        }
        break;

    case 'update-categoria':
        if (isset($params[1])) {
            $controller = new PlanController($req);
            $controller->update($params[1]);
        } else {
            echo "Falta el ID de la categoría a actualizar.";
        }
        break;

    case 'eliminar-categoria':
        if (isset($params[1])) {
            $controller = new PlanController($req);
            $controller->delete($params[1]);
        } else {
            echo "Falta el ID de la categoría a eliminar.";
        }
        break;

    // ---------- (A) Ítems (Cuotas) ----------
    case 'items':
        $controller = new CuotaController($req);
        $controller->showAllCuotas();
        break;

    case 'item':
        if (isset($params[1])) {
            $controller = new CuotaController($req);
            $controller->showCuotaById($params[1]);
        } else {
            echo "Falta especificar el ID del ítem.";
        }
        break;

    case 'nuevo-item':
        $controller = new CuotaController($req);
        $controller->showFormularioAlta();
        break;

    case 'add-item':
        $controller = new CuotaController($req);
        $controller->add();
        break;

    case 'editar-item':
        if (isset($params[1])) {
            $controller = new CuotaController($req);
            $controller->showFormularioEditar($params[1]);
        } else {
            echo "Falta especificar el ID del ítem a editar.";
        }
        break;

    case 'update-item':
        if (isset($params[1])) {
            $controller = new CuotaController($req);
            $controller->update($params[1]);
        } else {
            echo "Falta el ID del ítem a actualizar.";
        }
        break;

    case 'eliminar-item':
        if (isset($params[1])) {
            $controller = new CuotaController($req);
            $controller->delete($params[1]);
        } else {
            echo "Falta el ID del ítem a eliminar.";
        }
        break;

    // ---------- Clientes ----------
    case 'clientes':
        $controller = new ClienteController($req);
        $controller->showAllClientes();
        break;

    case 'cliente':
        if (isset($params[1])) {
            $controller = new CuotaController($req);
            $controller->showCuotasByCliente($params[1]);
        } else {
            echo "Falta especificar el ID del cliente.";
        }
        break;

    case 'nuevo-cliente':
        $controller = new ClienteController($req);
        $controller->showFormularioAlta();
        break;

    case 'add-cliente':
        $controller = new ClienteController($req);
        $controller->add();
        break;

    case 'editar-cliente':
        if (isset($params[1])) {
            $controller = new ClienteController($req);
            $controller->showFormularioEditar($params[1]);
        } else {
            echo "Falta especificar el ID del cliente a editar.";
        }
        break;

    case 'update-cliente':
        if (isset($params[1])) {
            $controller = new ClienteController($req);
            $controller->update($params[1]);
        } else {
            echo "Falta el ID del cliente a actualizar.";
        }
        break;

    case 'eliminar-cliente':
        if (isset($params[1])) {
            $controller = new ClienteController($req);
            $controller->delete($params[1]);
        } else {
            echo "Falta el ID del cliente a eliminar.";
        }
        break;

    // ---------- Autenticación ----------
    case 'login':
        $controller = new AuthController($req);
        $controller->showLogin();
        break;

    case 'verify':
        $controller = new AuthController($req);
        $controller->auth();
        break;

    case 'logout':
        $controller = new AuthController($req);
        $controller->logout();
        break;

    default:
        http_response_code(404);
        echo '404 - Página no encontrada';
        break;
}
