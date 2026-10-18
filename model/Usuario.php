<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario
{
    private PDO $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->conectar();
    }

    public function login(string $correo, string $clave): ?array
    {
        $sql = "SELECT * FROM usuario WHERE correo = :correo LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            return null;
        }

        if (password_verify($clave, $usuario['clave'])) {
            return $usuario;
        }

        return null;
    }

    public function cantidad(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM usuario')->fetchColumn();
    }

    public function registrarInicial(string $nombre, string $correo, string $clave): bool
    {
        $this->pdo->exec('BEGIN IMMEDIATE');

        try {
            if ($this->cantidad() !== 0) {
                $this->pdo->exec('ROLLBACK');
                return false;
            }

            $stmt = $this->pdo->prepare(
                'INSERT INTO usuario (nombre, correo, clave)
                 VALUES (:nombre, :correo, :clave)'
            );
            $stmt->execute([
                'nombre' => $nombre,
                'correo' => $correo,
                'clave' => password_hash($clave, PASSWORD_DEFAULT),
            ]);

            $this->pdo->exec('COMMIT');
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->exec('ROLLBACK');
            }

            throw $e;
        }
    }
}
