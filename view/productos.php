<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Disponibles - Tienda Ecológica CIMPA</title>
    <link rel="stylesheet" href="../assets/css/productos.css">
</head>
<body>
<?php
// Asegúrate de que esta ruta sea correcta a tu archivo de conexión
include '../config/conexionBd.php';

// C obtener el saldo del empleado (lo incluimos por si lo usas en el menú)
$sql_monedero = "SELECT saldo_cinpacoin FROM Monedero WHERE id_empleado = 1";
$result_monedero = $conn->query($sql_monedero);

$saldo_empleado = null;
if ($result_monedero && $result_monedero->num_rows > 0) {
    $row_monedero = $result_monedero->fetch_assoc();
    $saldo_empleado = $row_monedero["saldo_cinpacoin"];
}

// Consulta para obtener todos los productos generales con stock > 0
$sql_todos_productos = "SELECT id_producto, nombre, descripcion, precio_cinpacoin, stock, imagen FROM Productos WHERE stock > 0";
$result_todos_productos = $conn->query($sql_todos_productos);

// Consulta para obtener todos los productos para mascotas
$sql_productos_mascota = "SELECT id_producto, nombre, descripcion, precio_cinpacoin, imagen FROM productos_mascota";
$result_productos_mascota = $conn->query($sql_productos_mascota);
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
                    <a class="nav-link" href="#productos-mascota">MUNDO MASCOTAS</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <input class="form-control form-control-sm" type="search" placeholder="Buscar" aria-label="Buscar">
                </div>
                <?php if ($saldo_empleado !== null): ?>
                    <a href="huellaCarbono.php" class="puntos-empleado bg-success text-white rounded-pill px-3 py-2 me-2 text-decoration-none">
                        <span class="icono-puntos">💰</span> <?php echo htmlspecialchars(number_format($saldo_empleado, 0)); ?> Puntos
                    </a>
                <?php endif; ?>
                <a href="carrito.php" class="icon-btn btn btn-outline-secondary rounded-circle" aria-label="Carrito">
                    <img src="../assets/img/carritologo.png" alt="Carrito" height="25">
                </a>
            </div>
        </div>
    </nav>
</header>

<main class="container py-5">
    <h1 class="mb-4">Productos Disponibles</h1>

    <section id="productos-generales" class="py-3">
        <h2>Productos Generales</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            if ($result_todos_productos && $result_todos_productos->num_rows > 0) {
                while($row_producto = $result_todos_productos->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100 shadow producto-card">';
                    if (!empty($row_producto["imagen"])) {
                        echo '<img src="' . htmlspecialchars($row_producto["imagen"]) . '" class="card-img-top producto-imagen" alt="' . htmlspecialchars($row_producto["nombre"]) . '">';
                    } else {
                        echo '<img src="../assets/img/producto_default.jpg" class="card-img-top producto-imagen" alt="' . htmlspecialchars($row_producto["nombre"]) . '">';
                    }
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title producto-titulo">' . htmlspecialchars($row_producto["nombre"]) . '</h5>';
                    echo '<p class="card-text producto-descripcion">' . htmlspecialchars($row_producto["descripcion"]) . '</p>';
                    echo '<p class="card-text"><small class="text-success fw-bold producto-precio">Precio: ' . htmlspecialchars($row_producto["precio_cinpacoin"]) . ' CinpaCoins</small></p>';
                    echo '<p class="card-text"><small class="text-muted producto-stock">Stock: ' . htmlspecialchars($row_producto["stock"]) . ' unidades</small></p>';
                    echo '<div class="d-grid gap-2">';
                    echo '<button class="btn btn-outline-primary btn-sm agregar-carrito" data-id="' . htmlspecialchars($row_producto["id_producto"]) . '">Añadir al Carrito</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col"><p class="lead">No hay productos generales disponibles en este momento.</p></div>';
            }
            ?>
        </div>
    </section>

    <section id="productos-mascota" class="py-3">
        <h2>Productos para Mascotas</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            if ($result_productos_mascota && $result_productos_mascota->num_rows > 0) {
                while($row_mascota = $result_productos_mascota->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100 shadow producto-card">';
                    if (!empty($row_mascota["imagen"])) {
                        echo '<img src="' . htmlspecialchars($row_mascota["imagen"]) . '" class="card-img-top producto-imagen" alt="' . htmlspecialchars($row_mascota["nombre"]) . '">';
                    } else {
                        echo '<img src="../assets/img/producto_default_mascota.jpg" class="card-img-top producto-imagen" alt="' . htmlspecialchars($row_mascota["nombre"]) . '">';
                    }
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title producto-titulo">' . htmlspecialchars($row_mascota["nombre"]) . '</h5>';
                    echo '<p class="card-text producto-descripcion">' . htmlspecialchars($row_mascota["descripcion"]) . '</p>';
                    echo '<p class="card-text"><small class="text-success fw-bold producto-precio">Precio: ' . htmlspecialchars($row_mascota["precio_cinpacoin"]) . ' CinpaCoins</small></p>';
                    echo '<div class="d-grid gap-2">';
                    echo '<button class="btn btn-outline-primary btn-sm agregar-carrito-mascota" data-id="' . htmlspecialchars($row_mascota["id_producto"]) . '">Añadir al Carrito</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col"><p class="lead">No hay productos para mascotas disponibles en este momento.</p></div>';
            }

            // Cerrar conexión a la base de datos
            $conn->close();
            ?>
        </div>
    </section>
</main>

<footer class="bg-dark text-light py-3 fixed-bottom">
    <div class="container text-center">
        Síguenos en:
        <a href="https://twitter.com/cimpa_plm" target="_blank" rel="noopener" class="text-light mx-2"><img src="../assets/img/logox.webp" alt="Twitter" width="24" height="24"></a>
        <a href="https://fr.linkedin.com/company/cimpa-plm-services" target="_blank" rel="noopener" class="text-light mx-2"><img src="../assets/img/linkedin.png" alt="LinkedIn" width="24" height="24"></a>
        <a href="https://www.youtube.com/channel/UCvDeDvVG3vRIlao7eVTYt_A" target="_blank" rel="noopener" class="text-light mx-2"><img src="../assets/img/Youtube_logo.png" alt="YouTube" width="24" height="24"></a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="../assets/js/carrito.js"></script>
</body>
</html>