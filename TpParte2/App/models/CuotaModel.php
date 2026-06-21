<?php
require_once __DIR__ . '/Model.php';


class CuotaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private const SELECT_BASE = '
        SELECT cuota.*,
               plan_de_cuotas.nombre AS nombre_plan,
               cliente.nombre AS nombre_cliente
        FROM cuota
        JOIN plan_de_cuotas ON cuota.id_plan = plan_de_cuotas.id_plan
        JOIN cliente ON cuota.id_cliente = cliente.id_cliente
    ';

    public function getAllCuotas()
    {
        $query = $this->db->prepare(self::SELECT_BASE . ' ORDER BY cuota.id_cuota DESC');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCuotaById($id)
    {
        $query = $this->db->prepare(self::SELECT_BASE . ' WHERE cuota.id_cuota = ?');
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function getCuotasByPlan($idPlan)
    {
        $query = $this->db->prepare(self::SELECT_BASE . ' WHERE cuota.id_plan = ? ORDER BY cuota.nro_cuota');
        $query->execute([$idPlan]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCuotasByCliente($idCliente)
    {
        $query = $this->db->prepare(self::SELECT_BASE . ' WHERE cuota.id_cliente = ? ORDER BY cuota.id_cuota DESC');
        $query->execute([$idCliente]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function insertCuota($idPlan, $idCliente, $nroCuota, $monto, $estadoPago, $imagenUrl)
    {
        $query = $this->db->prepare(
            'INSERT INTO cuota (id_plan, id_cliente, nro_cuota, monto, estado_pago, imagen_url) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $query->execute([$idPlan, $idCliente, $nroCuota, $monto, $estadoPago, $imagenUrl]);
        return $this->db->lastInsertId();
    }

    public function updateCuota($id, $idPlan, $idCliente, $nroCuota, $monto, $estadoPago, $imagenUrl)
    {
        $query = $this->db->prepare(
            'UPDATE cuota SET id_plan = ?, id_cliente = ?, nro_cuota = ?, monto = ?, estado_pago = ?, imagen_url = ? WHERE id_cuota = ?'
        );
        $query->execute([$idPlan, $idCliente, $nroCuota, $monto, $estadoPago, $imagenUrl, $id]);
    }

    public function deleteCuota($id)
    {
        $query = $this->db->prepare('DELETE FROM cuota WHERE id_cuota = ?');
        $query->execute([$id]);
    }
}
