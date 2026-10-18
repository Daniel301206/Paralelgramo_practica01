<?php
class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $directorio = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database';
        if (!is_dir($directorio)
            && !mkdir($directorio, 0775, true)
            && !is_dir($directorio)) {
            throw new RuntimeException('No se pudo crear el directorio de la base de datos.');
        }

        $ruta = $directorio . DIRECTORY_SEPARATOR . 'paralelogramo.sqlite';
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

    }
}
