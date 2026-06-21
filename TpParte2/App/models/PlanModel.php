<?php
require_once __DIR__ . '/Model.php';


class PlanModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllPlanes()
    {
        $query = $this->db->prepare('SELECT * FROM plan_de_cuotas ORDER BY nombre');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getPlanById($id)
    {
        $query = $this->db->prepare('SELECT * FROM plan_de_cuotas WHERE id_plan = ?');
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function insertPlan($nombre, $descripcion, $montoTotal, $imagenUrl)
    {
        $query = $this->db->prepare(
            'INSERT INTO plan_de_cuotas (nombre, descripcion, monto_total, imagen_url) VALUES (?, ?, ?, ?)'
        );
        $query->execute([$nombre, $descripcion, $montoTotal, $imagenUrl]);
        return $this->db->lastInsertId();
    }

    public function updatePlan($id, $nombre, $descripcion, $montoTotal, $imagenUrl)
    {
        $query = $this->db->prepare(
            'UPDATE plan_de_cuotas SET nombre = ?, descripcion = ?, monto_total = ?, imagen_url = ? WHERE id_plan = ?'
        );
        $query->execute([$nombre, $descripcion, $montoTotal, $imagenUrl, $id]);
    }

    public function deletePlan($id)
    {
        $query = $this->db->prepare('DELETE FROM plan_de_cuotas WHERE id_plan = ?');
        $query->execute([$id]);
    }
}
