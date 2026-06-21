<?php
require_once __DIR__ . '/Model.php';

class AuthModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getUsuarioByUsername($username)
    {
        $query = $this->db->prepare('SELECT * FROM usuario WHERE username = ?');
        $query->execute([$username]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
}
