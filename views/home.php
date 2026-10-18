<?php
session_start();
require_once __DIR__ . '/../controller/AuthController.php';
require_once __DIR__ . '/../controller/ParalelogramoController.php';

AuthController::verificarSesion();

$idUsuario = (int) $_SESSION['id_usuario'];
$controller = new ParalelogramoController();
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = $controller->procesarFormulario($idUsuario);
    if (in_array($mensaje, ['Cálculo guardado.', 'Registro actualizado.', 'Registro eliminado.'], true)) {
        header('Location: home.php?mensaje=' . rawurlencode($mensaje));
        exit;
    }
}

if (isset($_GET['mensaje']) && is_string($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
}

$buscarParametro = $_GET['buscar'] ?? '';
$buscar = is_string($buscarParametro) ? trim(substr($buscarParametro, 0, 100)) : '';
$registroEditar = null;

if (isset($_GET['editar'])) {
    $parametroEditar = $_GET['editar'];
    $idEditar = is_string($parametroEditar) || is_int($parametroEditar)
        ? filter_var($parametroEditar, FILTER_VALIDATE_INT)
        : false;
    if ($idEditar !== false && $idEditar !== null) {
        $registroEditar = $controller->obtenerParaEditar($idEditar, $idUsuario);
    }
}

$registros = $controller->listar($idUsuario, $buscar);
$idActual = $registroEditar['id_paralelogramo'] ?? '';
if ($idActual === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $parametroId = $_POST['id_paralelogramo'] ?? 0;
    $idEnviado = is_string($parametroId) || is_int($parametroId)
        ? filter_var($parametroId, FILTER_VALIDATE_INT)
        : false;
    if ($idEnviado !== false && $idEnviado > 0) {
        $idActual = $idEnviado;
    }
}
$valorFormulario = static function (string $campo) use ($registroEditar): string {
    if ($registroEditar !== null && isset($registroEditar[$campo])) {
        return (string) $registroEditar[$campo];
    }

    $valor = $_POST[$campo] ?? '';
    return is_string($valor) ? $valor : '';
};
$baseActual = $valorFormulario('txtBase');
$ladoActual = $valorFormulario('txtLado');
$alturaActual = $valorFormulario('txtAltura');
$detalleActual = $valorFormulario('txtDetalle');
$areaActual = $registroEditar['area'] ?? '';
$perimetroActual = $registroEditar['perimetro'] ?? '';
$esc = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Calculadora de área y perímetro de un paralelogramo con historial SQLite.">
    <title>Calculadora de paralelogramo</title>
    <link rel="stylesheet" href="../assets/css/style_home.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="home.php">
            <img src="../Imagenes/Paralelogramo%20azul%20con%20%C3%A1ngulos%20marcados.png" alt="">
            <span>Geometría fácil</span>
        </a>
        <div class="header-user">
            <span>Hola, <?= $esc($_SESSION['usuario'] ?? 'Usuario') ?></span>
            <a class="button button-secondary" href="logout.php">Cerrar sesión</a>
        </div>
    </header>

    <main class="page">
        <section class="intro">
            <p class="eyebrow">Calculadora geométrica</p>
            <h1>Paralelogramo</h1>
            <p>Ingresa la base, el lado y la altura para calcular el área y el perímetro.</p>
        </section>

        <?php if ($mensaje !== '') : ?>
            <p class="notice" role="status"><?= $esc($mensaje) ?></p>
        <?php endif; ?>

        <section class="calculator-card" aria-labelledby="form-title">
            <div class="card-heading">
                <div>
                    <p class="eyebrow"><?= $idActual !== '' ? 'Editar cálculo' : 'Nuevo cálculo' ?></p>
                    <h2 id="form-title">Medidas del paralelogramo</h2>
                </div>
            </div>
            <form action="home.php" method="post" id="frmFormulario">
                <input type="hidden" name="id_paralelogramo" value="<?= $esc($idActual) ?>">
                <div class="form-grid">
                    <label>
                        <span>Base</span>
                        <input type="number" name="txtBase" id="txtBase" min="0.000001" step="any"
                               placeholder="Ej. 8" value="<?= $esc($baseActual) ?>" required>
                    </label>
                    <label>
                        <span>Lado</span>
                        <input type="number" name="txtLado" id="txtLado" min="0.000001" step="any"
                               placeholder="Ej. 5" value="<?= $esc($ladoActual) ?>" required>
                    </label>
                    <label>
                        <span>Altura</span>
                        <input type="number" name="txtAltura" id="txtAltura" min="0.000001" step="any"
                               placeholder="Ej. 4" value="<?= $esc($alturaActual) ?>" required>
                    </label>
                    <label class="wide-field">
                        <span>Detalle <small>(opcional)</small></span>
                        <input type="text" name="txtDetalle" id="txtDetalle" maxlength="500"
                               placeholder="Ej. Tarea de geometría" value="<?= $esc($detalleActual) ?>">
                    </label>
                </div>

                <div class="results" aria-live="polite">
                    <div class="result-box">
                        <span>Área <small>(base × altura)</small></span>
                        <output id="txtA"><?= $esc($areaActual) ?></output>
                    </div>
                    <div class="result-box">
                        <span>Perímetro <small>(2 × (base + lado))</small></span>
                        <output id="txtP"><?= $esc($perimetroActual) ?></output>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit" name="accion" value="guardar">
                        <?= $idActual !== '' ? 'Actualizar cálculo' : 'Guardar cálculo' ?>
                    </button>
                    <?php if ($idActual !== '') : ?>
                        <a class="button button-secondary" href="home.php">Cancelar edición</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="history" aria-labelledby="history-title">
            <div class="history-heading">
                <div>
                    <p class="eyebrow">Tus resultados</p>
                    <h2 id="history-title">Historial de cálculos</h2>
                </div>
                <form class="search-form" action="home.php" method="get">
                    <label class="visually-hidden" for="txtBuscar">Buscar en el historial</label>
                    <input type="search" name="buscar" id="txtBuscar" placeholder="Buscar medidas o detalle"
                           value="<?= $esc($buscar) ?>">
                    <button class="button button-secondary" type="submit">Buscar</button>
                    <?php if ($buscar !== '') : ?>
                        <a class="clear-link" href="home.php">Limpiar</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Base</th>
                            <th>Lado</th>
                            <th>Altura</th>
                            <th>Área</th>
                            <th>Perímetro</th>
                            <th>Detalle</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($registros !== []) : ?>
                            <?php foreach ($registros as $fila) : ?>
                                <tr>
                                    <td><?= $esc($fila['fecha_registro']) ?></td>
                                    <td><?= $esc($fila['base']) ?></td>
                                    <td><?= $esc($fila['lado']) ?></td>
                                    <td><?= $esc($fila['altura']) ?></td>
                                    <td><?= $esc($fila['area']) ?></td>
                                    <td><?= $esc($fila['perimetro']) ?></td>
                                    <td><?= $esc($fila['detalle']) ?></td>
                                    <td class="row-actions">
                                        <a class="action-link" href="home.php?editar=<?= (int) $fila['id_paralelogramo'] ?>">Editar</a>
                                        <form action="home.php" method="post"
                                              onsubmit="return confirm('¿Deseas eliminar este cálculo?');">
                                            <input type="hidden" name="id_paralelogramo"
                                                   value="<?= (int) $fila['id_paralelogramo'] ?>">
                                            <button class="action-link danger" type="submit" name="accion"
                                                    value="eliminar">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class="empty-state" colspan="8">Todavía no hay cálculos para mostrar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="../assets/js/app.js"></script>
</body>
</html>
