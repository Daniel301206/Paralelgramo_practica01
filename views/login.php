<?php
session_start();
require_once __DIR__ . '/../controller/AuthController.php';

if (!empty($_SESSION['id_usuario'])) {
    header('Location: home.php');
    exit;
}

$mensaje = '';
$correo = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (!is_string($correo) || !is_string($clave) || trim($correo) === '' || $clave === '') {
        $mensaje = 'Completa el correo y la contraseña.';
    } else {
        $correo = trim($correo);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $mensaje = 'Ingresa un correo válido.';
        } else {
            $auth = new AuthController();
            if ($auth->login($correo, $clave)) {
                header('Location: home.php');
                exit;
            }

            $mensaje = 'El correo o la contraseña son incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inicia sesión en la calculadora de paralelogramo.">
    <title>Iniciar sesión | Geometría fácil</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <img class="login-illustration"
             src="../Imagenes/Paralelogramo%20azul%20con%20%C3%A1ngulos%20marcados.png"
             alt="Ilustración de un paralelogramo">
        <p class="eyebrow">Geometría fácil</p>
        <h1>Calculadora de paralelogramo</h1>
        <p class="login-copy">Inicia sesión para calcular y guardar tus resultados.</p>

        <?php if ($mensaje !== '') : ?>
            <p class="login-error" role="alert"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" autocomplete="username"
                   value="<?= htmlspecialchars(is_string($correo) ? $correo : '', ENT_QUOTES, 'UTF-8') ?>" required>
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" autocomplete="current-password" required>
            <button class="button" type="submit">Iniciar sesión</button>
        </form>
        <p class="login-help">Acceso inicial: <strong>admin@paralelogramo.local</strong> / <strong>admin123</strong></p>
    </main>
</body>
</html>
