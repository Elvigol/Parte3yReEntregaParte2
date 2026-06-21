<?php
require_once __DIR__ . '/Database.php';

class CuotaModel
{
    /** Columnas válidas de la tabla, usadas para ordenar y filtrar de forma segura (whitelist). */
    private const COLUMNAS_VALIDAS = [
        'id_cuota', 'id_plan', 'id_cliente', 'nro_cuota', 'monto', 'estado_pago', 'imagen_url'
    ];

    /** Columna por defecto para el ordenamiento obligatorio (un solo campo). */
    private const COLUMNA_ORDEN_DEFAULT = 'id_cuota';

    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lista cuotas con:
     *  - orden (obligatorio: por id_cuota asc/desc; opcional: por cualquier campo válido)
     *  - filtro opcional por campo=valor
     *  - paginado opcional (page, limit)
     *
     * @return array{data: array, total: int}
     */
    public function getAll(string $orderBy, string $orderDir, ?string $filterField, ?string $filterValue, ?int $page, ?int $limit): array
    {
        $orderBy = in_array($orderBy, self::COLUMNAS_VALIDAS, true) ? $orderBy : self::COLUMNA_ORDEN_DEFAULT;
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $where = '';
        $params = [];

        if ($filterField !== null && in_array($filterField, self::COLUMNAS_VALIDAS, true) && $filterValue !== null) {
            $where = "WHERE `$filterField` = ?";
            $params[] = $filterValue;
        }

        // Total de registros (para info de paginado)
        $countQuery = $this->db->prepare("SELECT COUNT(*) FROM cuota $where");
        $countQuery->execute($params);
        $total = (int) $countQuery->fetchColumn();

        $sql = "SELECT * FROM cuota $where ORDER BY `$orderBy` $orderDir";

        if ($page !== null && $limit !== null) {
            $offset = ($page - 1) * $limit;
            $sql .= ' LIMIT ? OFFSET ?';
        }

        $query = $this->db->prepare($sql);

        $i = 1;
        foreach ($params as $param) {
            $query->bindValue($i++, $param);
        }
        if ($page !== null && $limit !== null) {
            $query->bindValue($i++, $limit, PDO::PARAM_INT);
            $query->bindValue($i++, $offset, PDO::PARAM_INT);
        }

        $query->execute();

        return [
            'data' => $query->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
        ];
    }

    public function getById(int $id): ?array
    {
        $query = $this->db->prepare('SELECT * FROM cuota WHERE id_cuota = ?');
        $query->execute([$id]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function planExiste(int $idPlan): bool
    {
        $query = $this->db->prepare('SELECT 1 FROM plan_de_cuotas WHERE id_plan = ?');
        $query->execute([$idPlan]);
        return $query->rowCount() > 0;
    }

    public function clienteExiste(int $idCliente): bool
    {
        $query = $this->db->prepare('SELECT 1 FROM cliente WHERE id_cliente = ?');
        $query->execute([$idCliente]);
        return $query->rowCount() > 0;
    }

    public function insert(array $datos): int
    {
        $query = $this->db->prepare(
            'INSERT INTO cuota (id_plan, id_cliente, nro_cuota, monto, estado_pago, imagen_url)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $query->execute([
            $datos['id_plan'],
            $datos['id_cliente'],
            $datos['nro_cuota'],
            $datos['monto'],
            $datos['estado_pago'] ?? 0,
            $datos['imagen_url'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $datos): void
    {
        $query = $this->db->prepare(
            'UPDATE cuota
             SET id_plan = ?, id_cliente = ?, nro_cuota = ?, monto = ?, estado_pago = ?, imagen_url = ?
             WHERE id_cuota = ?'
        );
        $query->execute([
            $datos['id_plan'],
            $datos['id_cliente'],
            $datos['nro_cuota'],
            $datos['monto'],
            $datos['estado_pago'] ?? 0,
            $datos['imagen_url'] ?? null,
            $id,
        ]);
    }

    public static function columnasValidas(): array
    {
        return self::COLUMNAS_VALIDAS;
    }
}
