<?php
require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ . '/../Helpers/Response.php';

/**
 * Endpoints de solo lectura para "plan_de_cuotas" (la categoría).
 * No forman parte de los requerimientos obligatorios, pero permiten que
 * un consumidor externo de la API pueda armar selects/menús sin acceder
 * directamente a la base de datos.
 */
class PlanController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function index(): void
    {
        $query = $this->db->query('SELECT * FROM plan_de_cuotas ORDER BY nombre');
        Response::ok(['data' => $query->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function show(int $id): void
    {
        $query = $this->db->prepare('SELECT * FROM plan_de_cuotas WHERE id_plan = ?');
        $query->execute([$id]);
        $plan = $query->fetch(PDO::FETCH_ASSOC);

        if ($plan === false) {
            Response::notFound("No existe un plan con id $id");
        }

        Response::ok($plan);
    }
}
