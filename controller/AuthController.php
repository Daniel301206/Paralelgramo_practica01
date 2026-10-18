<?php
require_once __DIR__ . '/../model/Usuario.php';

class AuthController
{
    public function login(string $correo, string $clave): bool
    {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->login($correo, $clave);

        if ($usuario) {
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['usuario'] = $usuario['nombre'] ?: $usuario['correo'];
            $_SESSION['correo'] = $usuario['correo'];
            return true;
        }

        return false;
    }

    public function hayUsuarios(): bool
    {
        return (new Usuario())->cantidad() > 0;
    }

    public function registrarInicial(string $nombre, string $correo, string $clave): string
    {
        $nombre = trim($nombre);
        $correo = trim($correo);
        $longitudNombre = preg_match_all('/./us', $nombre);

        if ($nombre === '' || $longitudNombre === false || $longitudNombre > 150) {
            return 'Ingresa un nombre válido de hasta 150 caracteres.';
        }

        if (strlen($correo) > 254 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return 'Ingresa un correo electrónico válido.';
        }

        if (strlen($clave) < 12 || strlen($clave) > 72) {
            return 'La contraseña debe tener al menos 12 caracteres y no superar 72 bytes.';
        }

        $usuarioModel = new Usuario();
        return $usuarioModel->registrarInicial($nombre, $correo, $clave)
            ? 'Cuenta creada. Ya puedes iniciar sesión.'
            : 'El registro está cerrado porque ya existe un usuario.';
    }

    public static function verificarSesion(): void
    {
        if (empty($_SESSION['id_usuario'])) {
            header('Location: /views/login.php');
            exit;
        }
    }
}