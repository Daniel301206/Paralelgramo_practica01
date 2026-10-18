<?php
require_once __DIR__ . '/../config/Database.php';

class Paralelogramo
{
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->conectar();
    }

    public function guardar(int $idUsuario, string $detalle, float $base, float $lado, float $altura): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO paralelogramo
                (id_usuario, detalle, base, lado, altura, area, perimetro)
             VALUES
                (:id_usuario, :detalle, :base, :lado, :altura, :area, :perimetro)'
        );
        $stmt->execute([
            'id_usuario' => $idUsuario,
            'detalle' => $detalle,
            'base' => $base,
            'lado' => $lado,
            'altura' => $altura,
            'area' => $base * $altura,
            'perimetro' => 2 * ($base + $lado),
        ]);
    }

    public function actualizar(
        int $id,
        int $idUsuario,
        string $detalle,
        float $base,
        float $lado,
        float $altura
    ): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE paralelogramo
             SET detalle = :detalle,
                 base = :base,
                 lado = :lado,
                 altura = :altura,
                 area = :area,
                 perimetro = :perimetro
             WHERE id_paralelogramo = :id AND id_usuario = :id_usuario'
        );
        $stmt->execute([
            'id' => $id,
            'id_usuario' => $idUsuario,
            'detalle' => $detalle,
            'base' => $base,
            'lado' => $lado,
            'altura' => $altura,
            'area' => $base * $altura,
            'perimetro' => 2 * ($base + $lado),
        ]);

        return $stmt->rowCount() > 0;
    }

    public function eliminar(int $id, int $idUsuario): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM paralelogramo
             WHERE id_paralelogramo = :id AND id_usuario = :id_usuario'
        );
        $stmt->execute(['id' => $id, 'id_usuario' => $idUsuario]);

        return $stmt->rowCount() > 0;
    }

    public function listar(int $idUsuario, string $buscar = ''): array
    {
        $sql = 'SELECT id_paralelogramo, detalle, base, lado, altura, area, perimetro, fecha_registro
                FROM paralelogramo
                WHERE id_usuario = :id_usuario';
        $parametros = ['id_usuario' => $idUsuario];

        if ($buscar !== '') {
            $sql .= ' AND (detalle LIKE :buscar_detalle OR CAST(base AS TEXT) LIKE :buscar_base
                      OR CAST(lado AS TEXT) LIKE :buscar_lado OR CAST(altura AS TEXT) LIKE :buscar_altura)';
            $termino = '%' . $buscar . '%';
            $parametros['buscar_detalle'] = $termino;
            $parametros['buscar_base'] = $termino;
            $parametros['buscar_lado'] = $termino;
            $parametros['buscar_altura'] = $termino;
        }

        $sql .= ' ORDER BY id_paralelogramo DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id, int $idUsuario): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_paralelogramo, detalle, base, lado, altura, area, perimetro
             FROM paralelogramo
             WHERE id_paralelogramo = :id AND id_usuario = :id_usuario'
        );
        $stmt->execute(['id' => $id, 'id_usuario' => $idUsuario]);
        $fila = $stmt->fetch();

        return $fila ?: null;
    }
}
