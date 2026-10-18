<?php
session_start();
require_once __DIR__ . '/../controller/AuthController.php';

$registroExitoso = !empty($_SESSION['registro_inicial_exitoso']);
unset($_SESSION['registro_inicial_exitoso']);
$mensaje = $registroExitoso ? 'Cuenta creada. Ya puedes iniciar sesión.' : '';
$correo = '';
$nombre = '';
$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if ($accion === 'registro') {
        $nombre = $_POST['nombre'] ?? '';
        if (!is_string($nombre) || !is_string($correo) || !is_string($clave)) {
            $mensaje = 'Completa nombre, correo y contraseña con datos válidos.';
        } else {
            $mensaje = $auth->registrarInicial($nombre, $correo, $clave);
            $registroExitoso = $mensaje === 'Cuenta creada. Ya puedes iniciar sesión.';
            if ($registroExitoso) {
                $_SESSION['registro_inicial_exitoso'] = true;
                header('Location: /views/login.php');
                exit;
            }
        }
    } elseif ($accion === 'login') {
        if (!is_string($correo) || !is_string($clave) || trim($correo) === '' || $clave === '') {
            $mensaje = 'Completa el correo y la contraseña.';
        } else {
            $correo = trim($correo);
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $mensaje = 'Ingresa un correo válido.';
            } elseif ($auth->login($correo, $clave)) {
                header('Location: /views/home.php');
                exit;
            } else {
                $mensaje = 'El correo o la contraseña son incorrectos.';
            }
        }
    } else {
        $mensaje = 'La solicitud no es válida.';
    }
}

$registroAbierto = !$auth->hayUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inicia sesión en la calculadora de paralelogramo.">
    <title>Iniciar sesión | Geometría fácil</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <img class="login-illustration"
             src="/Imagenes/Paralelogramo%20azul%20con%20%C3%A1ngulos%20marcados.png"
             alt="Ilustración de un paralelogramo">
        <p class="eyebrow">Geometría fácil</p>
        <h1>Calculadora de paralelogramo</h1>
        <p class="login-copy">
            <?= $registroAbierto
                ? 'Crea la primera cuenta para comenzar a usar el sistema.'
                : 'Inicia sesión para calcular y guardar tus resultados.' ?>
        </p>

        <?php if ($mensaje !== '') : ?>
            <p class="<?= $registroExitoso ? 'login-copy' : 'login-error' ?>"
               role="<?= $registroExitoso ? 'status' : 'alert' ?>">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($registroAbierto) : ?>
            <form action="/views/login.php" method="post">
                <input type="hidden" name="accion" value="registro">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" autocomplete="name" maxlength="150"
                       value="<?= htmlspecialchars(is_string($nombre) ? $nombre : '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
                <label for="correo">Correo</label>
                <input type="email" name="correo" id="correo" autocomplete="email" maxlength="254"
                       value="<?= htmlspecialchars(is_string($correo) ? $correo : '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
                <label for="clave">Contraseña (12 caracteres como mínimo)</label>
                <input type="password" name="clave" id="clave" autocomplete="new-password"
                       minlength="12" maxlength="72" required>
                <button class="button" type="submit">Crear primera cuenta</button>
            </form>
        <?php else : ?>
            <form action="/views/login.php" method="post">
                <input type="hidden" name="accion" value="login">
                <label for="correo">Correo</label>
                <input type="email" name="correo" id="correo" autocomplete="username"
                       value="<?= htmlspecialchars(is_string($correo) ? $correo : '', ENT_QUOTES, 'UTF-8') ?>"
                       required>
                <label for="clave">Contraseña</label>
                <input type="password" name="clave" id="clave" autocomplete="current-password" required>
                <button class="button" type="submit">Iniciar sesión</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
