<?php
class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $ruta = __DIR__ . '/../database/paralelogramo.sqlite';
        $this->pdo = new PDO('sqlite:' . $ruta, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('PRAGMA foreign_keys = ON');
        $this->inicializarEsquema();
    }

    public function conectar(): PDO
    {
        return $this->pdo;
    }

    private function inicializarEsquema(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS usuario (
                id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                correo TEXT NOT NULL UNIQUE,
                clave TEXT NOT NULL
            )'
        );

        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS paralelogramo (
                id_paralelogramo INTEGER PRIMARY KEY AUTOINCREMENT,
                id_usuario INTEGER NOT NULL,
                detalle TEXT NOT NULL DEFAULT \'\',
                base REAL NOT NULL CHECK (base > 0),
                lado REAL NOT NULL CHECK (lado > 0),
                altura REAL NOT NULL CHECK (altura > 0),
                area REAL NOT NULL,
                perimetro REAL NOT NULL,
                fecha_registro TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            )'
        );

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM usuario WHERE correo = :correo');
        $stmt->execute(['correo' => 'admin@paralelogramo.local']);

        if ((int) $stmt->fetchColumn() === 0) {
            $insertar = $this->pdo->prepare(
                'INSERT OR IGNORE INTO usuario (nombre, correo, clave)
                 VALUES (:nombre, :correo, :clave)'
            );
            $insertar->execute([
                'nombre' => 'Administrador',
                'correo' => 'admin@paralelogramo.local',
                'clave' => password_hash('admin123', PASSWORD_DEFAULT),
            ]);
        }
    }
}
