<?php
require_once __DIR__ . '/../Models/CuotaModel.php';
require_once __DIR__ . '/../Helpers/Response.php';

class CuotaController
{
    private $model;

    public function __construct()
    {
        $this->model = new CuotaModel();
    }

    /**
     * GET /api/cuotas
     *
     * Responsable: Miembro A (listado + orden), Miembro B (filtrado, opcional),
     * ambos (paginado, opcional).
     *
     * Query params soportados:
     *  - sort: campo por el que ordenar (default: id_cuota)
     *  - dir: asc | desc (default: asc)
     *  - filter_field / filter_value: filtra por igualdad de campo (opcional)
     *  - page / limit: paginado (opcional)
     */
    public function index(): void
    {
        $sort = $_GET['sort'] ?? 'id_cuota';
        $dir = $_GET['dir'] ?? 'asc';

        $filterField = $_GET['filter_field'] ?? null;
        $filterValue = $_GET['filter_value'] ?? null;

        if ($filterField !== null && !in_array($filterField, CuotaModel::columnasValidas(), true)) {
            Response::badRequest('filter_field inválido. Valores permitidos: ' . implode(', ', CuotaModel::columnasValidas()));
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : null;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : null;

        if (($page !== null && $limit === null) || ($page === null && $limit !== null)) {
            Response::badRequest('Para paginar se deben enviar tanto "page" como "limit"');
        }
        if ($page !== null && ($page < 1 || $limit < 1)) {
            Response::badRequest('"page" y "limit" deben ser mayores a 0');
        }

        $resultado = $this->model->getAll($sort, $dir, $filterField, $filterValue, $page, $limit);

        $response = ['data' => $resultado['data']];

        if ($page !== null) {
            $response['pagination'] = [
                'page' => $page,
                'limit' => $limit,
                'total' => $resultado['total'],
                'total_pages' => (int) ceil($resultado['total'] / $limit),
            ];
        } else {
            $response['total'] = $resultado['total'];
        }

        Response::ok($response);
    }

    /**
     * GET /api/cuotas/{id}
     * Responsable: Miembro B
     */
    public function show(int $id): void
    {
        $cuota = $this->model->getById($id);

        if ($cuota === null) {
            Response::notFound("No existe una cuota con id $id");
        }

        Response::ok($cuota);
    }

    /**
     * POST /api/cuotas
     * Responsable: Miembro B
     * Requiere token (Authorization: Bearer <token>)
     */
    public function store(): void
    {
        $datos = $this->leerBody();
        $errores = $this->validar($datos, false);

        if (!empty($errores)) {
            Response::badRequest(implode(' | ', $errores));
        }

        $id = $this->model->insert($datos);
        $cuota = $this->model->getById($id);

        Response::created($cuota);
    }

    /**
     * PUT /api/cuotas/{id}
     * Responsable: Miembro A
     * Requiere token (Authorization: Bearer <token>)
     */
    public function update(int $id): void
    {
        $existente = $this->model->getById($id);
        if ($existente === null) {
            Response::notFound("No existe una cuota con id $id");
        }

        $datos = $this->leerBody();
        $errores = $this->validar($datos, true);

        if (!empty($errores)) {
            Response::badRequest(implode(' | ', $errores));
        }

        $this->model->update($id, $datos);
        Response::ok($this->model->getById($id));
    }

    private function leerBody(): array
    {
        $raw = file_get_contents('php://input');
        $datos = json_decode($raw, true);
        return is_array($datos) ? $datos : [];
    }

    private function validar(array $datos, bool $esUpdate): array
    {
        $errores = [];

        foreach (['id_plan', 'id_cliente', 'nro_cuota', 'monto'] as $campo) {
            if (!isset($datos[$campo]) || $datos[$campo] === '') {
                $errores[] = "El campo \"$campo\" es obligatorio";
            }
        }

        if (!empty($errores)) {
            return $errores; // no seguir validando si faltan campos básicos
        }

        if (!is_numeric($datos['monto']) || (float) $datos['monto'] <= 0) {
            $errores[] = 'El campo "monto" debe ser numérico y mayor a 0';
        }

        if (!is_numeric($datos['nro_cuota']) || (int) $datos['nro_cuota'] <= 0) {
            $errores[] = 'El campo "nro_cuota" debe ser un entero mayor a 0';
        }

        if (isset($datos['estado_pago']) && !in_array((int) $datos['estado_pago'], [0, 1], true)) {
            $errores[] = 'El campo "estado_pago" debe ser 0 o 1';
        }

        if (!is_numeric($datos['id_plan']) || !$this->model->planExiste((int) $datos['id_plan'])) {
            $errores[] = 'El "id_plan" indicado no existe';
        }

        if (!is_numeric($datos['id_cliente']) || !$this->model->clienteExiste((int) $datos['id_cliente'])) {
            $errores[] = 'El "id_cliente" indicado no existe';
        }

        return $errores;
    }
}
