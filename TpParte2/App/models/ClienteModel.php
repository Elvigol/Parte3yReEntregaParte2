<?php
require_once __DIR__ . '/Model.php';


class ClienteModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAllClientes()
    {
        $query = $this->db->prepare('SELECT * FROM cliente ORDER BY nombre');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getClienteById($id)
    {
        $query = $this->db->prepare('SELECT * FROM cliente WHERE id_cliente = ?');
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function insertCliente($nombre, $email, $imagenUrl)
    {
        $query = $this->db->prepare('INSERT INTO cliente (nombre, email, imagen_url) VALUES (?, ?, ?)');
        $query->execute([$nombre, $email, $imagenUrl]);
        return $this->db->lastInsertId();
    }

    public function updateCliente($id, $nombre, $email, $imagenUrl)
    {
        $query = $this->db->prepare('UPDATE cliente SET nombre = ?, email = ?, imagen_url = ? WHERE id_cliente = ?');
        $query->execute([$nombre, $email, $imagenUrl, $id]);
    }

    public function deleteCliente($id)
    {
        $query = $this->db->prepare('DELETE FROM cliente WHERE id_cliente = ?');
        $query->execute([$id]);
    }
}
