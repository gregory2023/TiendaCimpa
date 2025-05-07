<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huella de Carbono - CIMPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/menu.css">

    <!-- Estilos independientes para la huella de carbono -->
    <link rel="stylesheet" href="../assets/css/huellaCarbono.css">

</head>
<body>
<?php
// Asegúrate de que esta ruta sea correcta a tu archivo de conexión
include '../config/conexionBd.php';

// Obtener el saldo del empleado
$sql_monedero = "SELECT saldo_cinpacoin FROM Monedero WHERE id_empleado = 1";
$result_monedero = $conn->query($sql_monedero);

$saldo_empleado = null;
if ($result_monedero && $result_monedero->num_rows > 0) {
    $row_monedero = $result_monedero->fetch_assoc();
    $saldo_empleado = $row_monedero["saldo_cinpacoin"];
}

// Datos ficticios para la huella de carbono
$huella_actual = 2.5; // toneladas CO2
$huella_objetivo = 2.0;
$reduccion_porcentaje = (1 - ($huella_actual / 3.0)) * 100; // Asumiendo que 3.0 es la huella inicial
$nivel_eco = "Consciente Ecológico"; // Niveles: Principiante, Consciente, Activista, Eco-Guerrero

// Calcular color de nivel basado en la reducción
$color_nivel = "success";
if ($reduccion_porcentaje < 15) {
    $color_nivel = "danger";
} elseif ($reduccion_porcentaje < 30) {
    $color_nivel = "warning";
} elseif ($reduccion_porcentaje < 50) {
    $color_nivel = "info";
}

// Calcular acciones eco
$acciones_eco = [
    ["nombre" => "Reciclaje", "cantidad" => 45, "icono" => "♻️"],
    ["nombre" => "Transporte Sostenible", "cantidad" => 32, "icono" => "🚲"],
    ["nombre" => "Ahorro Energético", "cantidad" => 28, "icono" => "💡"]
];
?>

<header class="site-header bg-light shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light container">
        <a class="navbar-brand" href="menu.php">
            <img src="../assets/img/logo.png" alt="Logo" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="productos.php">PRODUCTOS</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <input class="form-control form-control-sm" type="search" placeholder="Buscar" aria-label="Buscar">
                </div>
                <?php if ($saldo_empleado !== null): ?>
                    <div class="puntos-empleado bg-success text-white rounded-pill px-3 py-2 me-2">
                        <span class="icono-puntos">💰</span> <?php echo htmlspecialchars(number_format($saldo_empleado, 0)); ?> Puntos
                    </div>
                <?php endif; ?>
                <a href="carrito.php" class="icon-btn btn btn-outline-secondary rounded-circle" aria-label="Carrito">
                    <img src="../assets/img/carritologo.png" alt="Carrito" height="25">
                </a>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="huella-header text-center">
        <div class="container">
            <h1>Tu Huella de Carbono</h1>
            <p class="lead">Conoce tu impacto ambiental y aprende cómo reducirlo</p>
        </div>
    </section>

    <section class="container mb-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card eco-stats mb-4">
                    <div class="card-body text-center">
                        <h3 class="card-title">Tu huella actual</h3>
                        <div class="display-4 text-success mb-3"><?php echo $huella_actual; ?> toneladas CO₂</div>
                        <p class="card-text mb-1">Objetivo: <?php echo $huella_objetivo; ?> toneladas CO₂</p>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo (($huella_objetivo / $huella_actual) * 100); ?>%"
                                 aria-valuenow="<?php echo (($huella_objetivo / $huella_actual) * 100); ?>" aria-valuemin="0" aria-valuemax="100">
                                <?php echo round((($huella_objetivo / $huella_actual) * 100)); ?>% del objetivo
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <span class="eco-badge bg-<?php echo $color_nivel; ?> text-white">
                                Nivel: <?php echo $nivel_eco; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <h4 class="text-center mb-4">Has reducido tu huella en un <?php echo round($reduccion_porcentaje); ?>%</h4>

                <div class="row mb-4">
                    <?php foreach ($acciones_eco as $accion): ?>
                        <div class="col-md-4">
                            <div class="card eco-stats h-100">
                                <div class="card-body text-center">
                                    <div class="eco-icon"><?php echo $accion["icono"]; ?></div>
                                    <h5><?php echo $accion["nombre"]; ?></h5>
                                    <p class="mb-0"><?php echo $accion["cantidad"]; ?> acciones</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="carbon-info">
                    <h4>¿Cómo puedes reducir más tu huella?</h4>

                    <div class="eco-tip">
                        <h5>Transporte Sostenible</h5>
                        <p>Utiliza bicicleta, transporte público o comparte vehículo. Cada día que no uses tu coche, reduces aproximadamente 2.4 kg de CO₂.</p>
                    </div>

                    <div class="eco-tip">
                        <h5>Ahorro Energético</h5>
                        <p>Cambia a bombillas LED y apaga los aparatos electrónicos cuando no los uses. Puedes reducir hasta un 10% de tu huella anual.</p>
                    </div>

                    <div class="eco-tip">
                        <h5>Alimentación Consciente</h5>
                        <p>Reduce el consumo de carne y opta por productos locales. Una dieta con menos carne puede reducir tu huella hasta en un 30%.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footerz bg-dark text-light py-3 fixed-bottom">
    <div class="container text-center">
        Síguenos en:
        <a href="https://twitter.com/cimpa_plm" target="_blank" rel="noopener" class="text-light mx-2"><img src="../assets/img/logox.webp" alt="Twitter" width="24" height="24"></a>
        <a href="https://fr.linkedin.com/company/cimpa-plm-services" target="_blank" rel="noopener" class="text-light mx-2"><img src="../assets/img/linkedin.png" alt="LinkedIn" width="24" height="24"></a>
        <a href="https://www.youtube.com/channel/UCvDeDvVG3vRIlao7eVTYt_A" target="_blank" rel="noopener" class="text-light mx-2"><img src="../assets/img/Youtube_logo.png" alt="YouTube" width="24" height="24"></a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
