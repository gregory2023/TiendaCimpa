<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Huella de Carbono y Retos - CIMPA</title>
    <link rel="stylesheet" href="../assets/css/menu.css">
    <link rel="stylesheet" href="../assets/css/huellaCarbono.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>
<?php
// 0. Habilitar errores para depuración (opcional, quitar en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// 1. CONEXIÓN A LA BASE DE DATOS
$conn = null;
if (file_exists('../config/conexionBd.php')) {
    include '../config/conexionBd.php'; // Asegúrate que $conn se establece aquí

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !! IMPORTANTE PARA MOSTRAR EMOJIS/ICONOS CORRECTAMENTE DESDE LA BASE DE DATOS !!
    // !! Asegúrate de que tu conexión a MySQL esté usando UTF-8 (preferiblemente utf8mb4).
    // !! Si no lo has configurado en tu archivo 'conexionBd.php', DESCOMENTA la siguiente línea:
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    if ($conn instanceof mysqli) {
        $conn->set_charset("utf8mb4"); // <--- ¡DESCOMENTA ESTO SI ES NECESARIO!
    }
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !! También verifica que tu base de datos, tabla 'TiposReto' y columna 'icono_accion'
    // !! estén configuradas con codificación utf8mb4 y collation utf8mb4_unicode_ci.
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
}

$modo_simulacion_activo = false;

// --- Simulación de conexión y datos ---
if (!isset($conn) || !($conn instanceof mysqli)) {
    $modo_simulacion_activo = true;

    class MockMysqliResult {
        private $data = [];
        private $pointer = 0;
        public $num_rows = 0;

        public function __construct(array $data_set) {
            $this->data = $data_set;
            $this->num_rows = count($data_set);
        }
        public function fetch_assoc() {
            if ($this->pointer < $this->num_rows) {
                return $this->data[$this->pointer++];
            }
            return null;
        }
        public function close() { /* No hace nada en simulación */ }
    }

    class MockDBConnection {
        public $data_sets = [];
        private $current_query_type = null;
        private $affected_id_empleado = null;
        public $error = '';

        public function __construct() {
            $this->data_sets['empleado_1'] = [['nombre' => 'Carlos Pérez (Simulado)']];
            $this->data_sets['monedero_1'] = [['saldo_cinpacoin' => 1250.00]];
            $this->data_sets['huellacarbonoresumenempleado_1'] = [[
                'huella_actual_toneladas_co2e' => 2.8,
                'huella_objetivo_toneladas_co2e' => 2.2,
                'huella_inicial_referencia_co2e' => 3.5,
                'nivel_eco' => 'Eco-Guerrero (Simulado)'
            ]];
            // Emojis estándar para la simulación
            $this->data_sets['AccionesRealizadas_TiposReto_1'] = [
                ['nombre_reto' => 'Reciclaje Pro (Simulado)', 'puntos_otorgados' => 15, 'icono_accion' => '♻️', 'descripcion_especifica_accion' => 'Separación avanzada de residuos.', 'fecha_realizacion' => '2025-05-08', 'estado_verificacion' => 'aprobado'],
                ['nombre_reto' => 'Ciclista Urbano (Simulado)', 'puntos_otorgados' => 20, 'icono_accion' => '🚲', 'descripcion_especifica_accion' => '50km recorridos esta semana.', 'fecha_realizacion' => '2025-05-07', 'estado_verificacion' => 'aprobado'],
                ['nombre_reto' => 'Guardián de la Energía (Simulado)', 'puntos_otorgados' => 5, 'icono_accion' => '💡', 'descripcion_especifica_accion' => 'Auditoría energética del hogar.', 'fecha_realizacion' => '2025-05-05', 'estado_verificacion' => 'pendiente'],
                ['nombre_reto' => 'Compra Local (Simulado)', 'puntos_otorgados' => 10, 'icono_accion' => '🛍️', 'descripcion_especifica_accion' => 'Compra de vegetales en mercado local.', 'fecha_realizacion' => '2025-05-01', 'estado_verificacion' => 'rechazado']
            ];
        }
        public function prepare($sql) {
            $this->error = '';
            if (strpos($sql, "SELECT nombre FROM Empleado WHERE id_empleado = ?") !== false) { $this->current_query_type = 'empleado';}
            elseif (strpos($sql, "FROM Monedero WHERE id_empleado = ?") !== false) { $this->current_query_type = 'monedero'; }
            elseif (strpos(strtolower($sql), "from huellacarbonoresumenempleado where id_empleado = ?") !== false) { $this->current_query_type = 'huellacarbonoresumenempleado'; }
            elseif (strpos($sql, "FROM AccionesRealizadas ar JOIN TiposReto tr") !== false) { $this->current_query_type = 'AccionesRealizadas_TiposReto'; }
            else { $this->current_query_type = null; }
            return $this;
        }
        public function bind_param($types, ...$params) { if (isset($params[0])) { $this->affected_id_empleado = $params[0]; } return true; }
        public function execute() { return true; }
        public function get_result() {
            $key_to_fetch = $this->current_query_type . '_' . $this->affected_id_empleado;
            if (isset($this->data_sets[$key_to_fetch])) { return new MockMysqliResult($this->data_sets[$key_to_fetch]); }
            return new MockMysqliResult([]);
        }
        public function close() { return true; }
        public function set_charset($charset){} // Para la simulación
    }
    $conn = new MockDBConnection();
}
// --- Fin de Simulación ---

if ($modo_simulacion_activo) {
    // echo "<p style='background:orange;color:white;text-align:center;padding:10px;font-weight:bold;'>MODO SIMULACIÓN ACTIVO.</p>";
}

// 2. ID DEL EMPLEADO
$id_empleado_actual = 1;
$nombre_empleado_actual = "Empleado Desconocido";

// 2.1 OBTENER NOMBRE DEL EMPLEADO
$sql_nombre_empleado = "SELECT nombre FROM Empleado WHERE id_empleado = ?";
if ($stmt_nombre = $conn->prepare($sql_nombre_empleado)) {
    $stmt_nombre->bind_param("i", $id_empleado_actual);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    if ($result_nombre && $result_nombre->num_rows > 0) {
        $row_nombre = $result_nombre->fetch_assoc();
        $nombre_empleado_actual = $row_nombre["nombre"];
    }
    $stmt_nombre->close();
}


// 3. OBTENER SALDO DEL MONEDERO
$saldo_empleado = null;
$sql_monedero = "SELECT saldo_cinpacoin FROM Monedero WHERE id_empleado = ?";
if ($stmt_monedero = $conn->prepare($sql_monedero)) {
    $stmt_monedero->bind_param("i", $id_empleado_actual);
    $stmt_monedero->execute();
    $result_monedero = $stmt_monedero->get_result();
    if ($result_monedero && $result_monedero->num_rows > 0) {
        $row_monedero = $result_monedero->fetch_assoc();
        $saldo_empleado = $row_monedero["saldo_cinpacoin"];
    }
    $stmt_monedero->close();
}

// 4. OBTENER DATOS DE HUELLA DE CARBONO
$huella_actual = 2.8;
$huella_objetivo = 2.2;
$huella_inicial_referencia = 3.5;
$nivel_eco = "Eco-Guerrero";

if (!$modo_simulacion_activo) {
    $sql_huella = "SELECT huella_actual_toneladas_co2e, huella_objetivo_toneladas_co2e, huella_inicial_referencia_co2e, nivel_eco
                   FROM huellacarbonoresumenempleado WHERE id_empleado = ?";
    if ($stmt_huella = $conn->prepare($sql_huella)) {
        $stmt_huella->bind_param("i", $id_empleado_actual);
        $stmt_huella->execute();
        $result_huella = $stmt_huella->get_result();
        if ($result_huella && $result_huella->num_rows > 0) {
            $row_huella = $result_huella->fetch_assoc();
            $huella_actual = floatval($row_huella["huella_actual_toneladas_co2e"]);
            $huella_objetivo = floatval($row_huella["huella_objetivo_toneladas_co2e"]);
            $huella_inicial_referencia = floatval($row_huella["huella_inicial_referencia_co2e"]);
            $nivel_eco = $row_huella["nivel_eco"];
        }
        $stmt_huella->close();
    }
} else {
    $sim_huella_data = $conn->data_sets['huellacarbonoresumenempleado_1'][0] ?? null;
    if ($sim_huella_data) {
        $huella_actual = floatval($sim_huella_data["huella_actual_toneladas_co2e"]);
        $huella_objetivo = floatval($sim_huella_data["huella_objetivo_toneladas_co2e"]);
        $huella_inicial_referencia = floatval($sim_huella_data["huella_inicial_referencia_co2e"]);
        $nivel_eco = $sim_huella_data["nivel_eco"];
    }
}


// 5. CÁLCULOS DE HUELLA
$reduccion_porcentaje = 0;
if ($huella_inicial_referencia > 0) {
    $reduccion_porcentaje = (1 - ($huella_actual / $huella_inicial_referencia)) * 100;
}

$color_nivel = "success";
if ($huella_actual > $huella_inicial_referencia && $huella_inicial_referencia > 0) { $color_nivel = "danger"; }
elseif ($reduccion_porcentaje < 15) { $color_nivel = "danger"; }
elseif ($reduccion_porcentaje < 30) { $color_nivel = "warning"; }
elseif ($reduccion_porcentaje < 50) { $color_nivel = "info"; }

$porcentaje_hacia_objetivo = 0;
if ($huella_objetivo > 0) {
    if ($huella_actual <= $huella_objetivo) {
        $porcentaje_hacia_objetivo = 100;
    } elseif ($huella_inicial_referencia > $huella_objetivo) {
        $reduccion_lograda = $huella_inicial_referencia - $huella_actual;
        $reduccion_total_necesaria = $huella_inicial_referencia - $huella_objetivo;
        if ($reduccion_total_necesaria > 0) {
            $porcentaje_hacia_objetivo = ($reduccion_lograda / $reduccion_total_necesaria) * 100;
        }
    } elseif ($huella_actual > 0) {
        $porcentaje_hacia_objetivo = ($huella_objetivo / $huella_actual) * 100;
    }
}
$porcentaje_hacia_objetivo = max(0, min(100, round($porcentaje_hacia_objetivo)));


// 6. OBTENER TODAS LAS ACCIONES/RETOS REALIZADOS (INCLUYENDO ESTADO)
$acciones_eco = [];
$sql_acciones = "SELECT tr.nombre_reto, ar.puntos_otorgados, tr.icono_accion, ar.descripcion_especifica_accion, ar.fecha_realizacion, ar.estado_verificacion
                 FROM AccionesRealizadas ar
                 JOIN TiposReto tr ON ar.id_tipo_reto = tr.id_tipo_reto
                 WHERE ar.id_empleado = ? 
                 ORDER BY ar.fecha_realizacion DESC, tr.id_tipo_reto";
if ($stmt_acciones = $conn->prepare($sql_acciones)) {
    $stmt_acciones->bind_param("i", $id_empleado_actual);
    $stmt_acciones->execute();
    $result_acciones = $stmt_acciones->get_result();
    if ($result_acciones) {
        while($row_accion = $result_acciones->fetch_assoc()) {
            $acciones_eco[] = $row_accion;
        }
        $result_acciones->close();
    }
}
?>

<header class="site-header bg-light shadow-sm sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light container">
        <a class="navbar-brand" href="menu.php">
            <img src="../assets/img/logo.png" alt="Logo CIMPA" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" aria-current="page" href="productos.php">PRODUCTOS</a></li>
                <li class="nav-item"><a class="nav-link" href="#reciclaje">RECICLAJE</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">CONTACTO</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="me-3"><input class="form-control form-control-sm" type="search" placeholder="Buscar" aria-label="Buscar"></div>
                <?php if ($saldo_empleado !== null): ?>
                    <a href="huellaCarbono.php" class="puntos-empleado bg-primary text-white rounded-pill px-3 py-2 me-2 text-decoration-none">
                        <span class="icono-puntos">💰</span> <?php echo htmlspecialchars(number_format($saldo_empleado, 0)); ?> Puntos
                    </a>
                <?php else: ?>
                    <span class="puntos-empleado bg-secondary text-white rounded-pill px-3 py-2 me-2"><span class="icono-puntos">💰</span> --- Puntos</span>
                <?php endif; ?>
                <a href="carrito.php" class="icon-btn btn btn-outline-primary rounded-circle" aria-label="Carrito">
                    <img src="../assets/img/carritologo.png" alt="Carrito" height="25">
                </a>
            </div>
        </div>
    </nav>
</header>

<main class="py-4">
    <section class="container-fluid px-md-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">Impacto Ecológico de <?php echo htmlspecialchars($nombre_empleado_actual); ?></h1>
            <p class="lead text-muted">Descubre tu huella y cómo tus acciones marcan la diferencia.</p>
        </div>

        <?php if ($modo_simulacion_activo): ?>
            <div class="alert alert-warning text-center mx-auto" role="alert" style="max-width: 800px;">
                <strong>Atención:</strong> Estás viendo datos de simulación. La conexión con la base de datos real no está activa o falló.
            </div>
        <?php endif; ?>

        <div class="row justify-content-center mb-5">
            <div class="col-xl-8 col-lg-10 col-md-12">
                <div class="card eco-stats shadow-lg">
                    <div class="card-body p-4 p-md-5 text-center">
                        <h3 class="card-title mb-4 section-title">Resumen de Huella</h3>
                        <div class="display-2 text-<?php echo $color_nivel; ?> mb-3 fw-bold"><?php echo number_format($huella_actual, 1); ?> <small class="text-muted fs-4">toneladas CO₂</small></div>
                        <p class="card-text mb-1 fs-5">Tu objetivo: <strong class="text-primary"><?php echo number_format($huella_objetivo, 1); ?> toneladas CO₂</strong></p>
                        <div class="progress my-4">
                            <div class="progress-bar bg-<?php echo $color_nivel; ?> progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $porcentaje_hacia_objetivo; ?>%"
                                 aria-valuenow="<?php echo $porcentaje_hacia_objetivo; ?>" aria-valuemin="0" aria-valuemax="100">
                                <?php echo $porcentaje_hacia_objetivo; ?>%
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="eco-badge bg-<?php echo $color_nivel; ?> text-white shadow-sm">
                                Nivel Eco: <?php echo htmlspecialchars($nivel_eco); ?>
                            </span>
                        </div>
                        <?php
                        if ($huella_inicial_referencia > 0 && $huella_inicial_referencia != $huella_actual) {
                            echo "<p class='mt-4 mb-0 fs-5'>";
                            if ($reduccion_porcentaje > 0) {
                                echo "¡Has <strong class='text-success'>reducido</strong> tu huella en un <strong>" . round($reduccion_porcentaje) . "%</strong>!";
                            } elseif ($reduccion_porcentaje < 0) {
                                echo "Tu huella ha <strong class='text-danger'>aumentado</strong> en un <strong>" . round(abs($reduccion_porcentaje)) . "%</strong>.";
                            } else {
                                echo "Tu huella se ha mantenido igual.";
                            }
                            echo " <small class='text-muted'>(Respecto a " . number_format($huella_inicial_referencia,1) . " t)</small></p>";
                        } elseif ($huella_inicial_referencia == 0 && $huella_actual > 0 && $huella_objetivo > 0 && !$modo_simulacion_activo) {
                            echo "<p class='mt-4 mb-0 fs-5 text-muted'>Aún no tenemos una referencia inicial para comparar tu progreso.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-xl-10 col-lg-12 col-md-12">
                <h3 class="text-center mb-4 section-title">Todas las Actividades Registradas</h3>
                <?php if (!empty($acciones_eco)): ?>
                    <div class="list-group">
                        <?php foreach ($acciones_eco as $accion): ?>
                            <?php
                            $estado_clase = 'status-pendiente';
                            $estado_texto = 'Pendiente';
                            if (isset($accion['estado_verificacion'])) {
                                switch (strtolower($accion['estado_verificacion'])) {
                                    case 'aprobado':
                                        $estado_clase = 'status-aprobado';
                                        $estado_texto = 'Aprobado';
                                        break;
                                    case 'rechazado':
                                        $estado_clase = 'status-rechazado';
                                        $estado_texto = 'Rechazado';
                                        break;
                                    case 'pendiente':
                                    default:
                                        $estado_clase = 'status-pendiente';
                                        $estado_texto = 'Pendiente';
                                        break;
                                }
                            }
                            ?>
                            <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 shadow-sm p-3">
                                <div class="d-flex w-100 justify-content-start align-items-center">
                                    <div class="eco-icon-list me-3">
                                        <?php
                                        // Usar htmlspecialchars para asegurar que el emoji se imprima correctamente
                                        // ENT_QUOTES es bueno para atributos, ENT_HTML5 para contenido HTML5
                                        echo htmlspecialchars($accion["icono_accion"] ?? '⭐', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                        ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h5 class="mb-1 fw-semibold"><?php echo htmlspecialchars($accion["nombre_reto"] ?? 'Reto Desconocido'); ?></h5>
                                            <span class="status-badge <?php echo $estado_clase; ?>">
                                                <?php echo htmlspecialchars($estado_texto); ?>
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mb-1">
                                            <?php
                                            if (!empty($accion["fecha_realizacion"])) {
                                                try { $fecha = date_create($accion["fecha_realizacion"]); echo $fecha ? htmlspecialchars(date_format($fecha, "d M Y")) : "Fecha inválida"; }
                                                catch (Exception $e) { echo "Error en fecha"; }
                                            } else { echo "Sin fecha"; }
                                            ?>
                                        </small>
                                        <?php if (!empty($accion["descripcion_especifica_accion"])): ?>
                                            <p class="mb-1 text-muted" style="font-size: 0.9rem;"><?php echo htmlspecialchars($accion["descripcion_especifica_accion"]); ?></p>
                                        <?php else: ?>
                                            <p class="mb-1 text-muted fst-italic" style="font-size: 0.9rem;">Sin descripción detallada.</p>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (strtolower($accion['estado_verificacion'] ?? '') === 'aprobado'): ?>
                                        <span class="badge bg-primary rounded-pill ms-3 fs-6" style="padding: 0.6em 0.8em;">
                                            +<?php echo htmlspecialchars($accion["puntos_otorgados"] ?? 0); ?> pts
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center alert alert-light border shadow-sm p-4">
                        <p class="lead mb-2">No hay actividades registradas para este empleado.</p>
                        <p class="text-muted">Participa en nuestras iniciativas para reducir tu huella y ganar puntos.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-12">
                <h3 class="mb-4 section-title text-center">Reduce Aún Más Tu Huella</h3>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card eco-tip h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="fs-1 mb-3 text-success">🚲</div>
                                <h5 class="card-title">Transporte Sostenible</h5>
                                <p class="card-text text-muted small">Usa bici, transporte público o comparte vehículo. Cada día sin coche reduce ~2.4 kg de CO₂.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card eco-tip h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="fs-1 mb-3 text-success">💡</div>
                                <h5 class="card-title">Ahorro Energético</h5>
                                <p class="card-text text-muted small">Usa LED y apaga aparatos. Puedes reducir hasta un 10% de tu huella anual.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card eco-tip h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="fs-1 mb-3 text-success">🥕</div>
                                <h5 class="card-title">Alimentación Consciente</h5>
                                <p class="card-text text-muted small">Menos carne, más productos locales. Una dieta así puede reducir tu huella hasta un 30%.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="footerz bg-dark text-light py-3">
    <div class="container text-center">
        <a href="https://twitter.com/cimpa_plm" target="_blank" rel="noopener" class="text-light mx-2">
            <img src="../assets/img/logo_x.png" alt="Twitter" width="34" height="34"></a>
        <a href="https://fr.linkedin.com/company/cimpa-plm-services" target="_blank" rel="noopener" class="text-light mx-2">
            <img src="../assets/img/logo_linkedin.png" alt="LinkedIn" width="34" height="34"></a>
        <a href="https://www.youtube.com/channel/UCvDeDvVG3vRIlao7eVTYt_A" target="_blank" rel="noopener" class="text-light mx-2">
            <img src="../assets/img/logo_youtube.png" alt="YouTube" width="34" height="34"></a>
    </div>
</footer>
</body>
</html>
