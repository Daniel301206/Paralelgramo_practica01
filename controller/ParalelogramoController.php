<?php
require_once __DIR__ . '/../model/Paralelogramo.php';

class ParalelogramoController
{
    private Paralelogramo $modelo;

    public function __construct()
    {
        $this->modelo = new Paralelogramo();
    }

    public function procesarFormulario(int $idUsuario): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return '';
        }

        $accion = $_POST['accion'] ?? '';
        if (!is_string($accion)) {
            return 'Acción no válida.';
        }

        if ($accion === 'guardar') {
            return $this->guardarOActualizar($idUsuario);
        }

        if ($accion === 'eliminar') {
            $idEnviado = $_POST['id_paralelogramo'] ?? null;
            $id = is_string($idEnviado) || is_int($idEnviado)
                ? filter_var($idEnviado, FILTER_VALIDATE_INT)
                : false;
            if ($id === false || $id === null || $id <= 0) {
                return 'No se pudo eliminar el registro.';
            }

            return $this->modelo->eliminar($id, $idUsuario)
                ? 'Registro eliminado.'
                : 'No se encontró el registro para eliminar.';
        }

        return 'Acción no válida.';
    }

    private function guardarOActualizar(int $idUsuario): string
    {
        $idEnviado = $_POST['id_paralelogramo'] ?? '';
        if ($idEnviado === '' || $idEnviado === 0 || $idEnviado === '0') {
            $id = 0;
        } else {
            $id = is_string($idEnviado) || is_int($idEnviado)
                ? filter_var($idEnviado, FILTER_VALIDATE_INT)
                : false;
        }
        $detalle = $_POST['txtDetalle'] ?? '';
        $base = $this->leerMedida('txtBase');
        $lado = $this->leerMedida('txtLado');
        $altura = $this->leerMedida('txtAltura');

        if (!is_string($detalle) || strlen(trim($detalle)) > 2000) {
            return 'El detalle no puede superar los 500 caracteres.';
        }

        if ($id === false || $id === null || $id < 0) {
            return 'Identificador de registro no válido.';
        }

        if ($base === null || $lado === null || $altura === null) {
            return 'Ingrese medidas numéricas válidas mayores a cero.';
        }

        if (!is_finite($base * $altura) || !is_finite(2 * ($base + $lado))) {
            return 'Las medidas ingresadas son demasiado grandes para calcular el resultado.';
        }

        $detalle = trim($detalle);
        if ($id > 0) {
            try {
                $actualizado = $this->modelo->actualizar(
                    $id,
                    $idUsuario,
                    $detalle,
                    $base,
                    $lado,
                    $altura
                );
            } catch (PDOException $e) {
                error_log('Error SQLite al actualizar cálculo, SQLSTATE ' . $e->getCode() . '.');
                return 'No se pudo actualizar el cálculo por un error de base de datos.';
            }

            return $actualizado ? 'Registro actualizado.' : 'No se encontró el registro para actualizar.';
        }

        try {
            $this->modelo->guardar($idUsuario, $detalle, $base, $lado, $altura);
        } catch (PDOException $e) {
            error_log('Error SQLite al guardar cálculo, SQLSTATE ' . $e->getCode() . '.');
            return 'No se pudo guardar el cálculo por un error de base de datos.';
        }

        return 'Cálculo guardado.';
    }

    private function leerMedida(string $campo): ?float
    {
        $valor = $_POST[$campo] ?? null;
        if (!is_string($valor) || trim($valor) === '' || !is_numeric($valor)) {
            return null;
        }

        $numero = (float) $valor;
        return is_finite($numero) && $numero > 0 ? $numero : null;
    }

    public function listar(int $idUsuario, string $buscar = ''): array
    {
        return $this->modelo->listar($idUsuario, $buscar);
    }

    public function obtenerParaEditar(int $id, int $idUsuario): ?array
    {
        return $id > 0 ? $this->modelo->obtenerPorId($id, $idUsuario) : null;
    }
}