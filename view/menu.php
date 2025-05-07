<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Ecológica CIMPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/menu.css">
    <style>
        /* Estilos adicionales para ajustar la visualización de los productos */
        .card-img-top {
            max-height: 150px; /* Ajusta esta altura según necesites */
            object-fit: contain; /* O cover, dependiendo de cómo quieras que se ajuste la imagen */
        }
        .card {
            display: flex;
            flex-direction: column;
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>
<?php
// Asegúrate de que esta ruta sea correcta a tu archivo de conexión
include '../config/conexionBd.php';

// C obtener el saldo del empleado
$sql_monedero = "SELECT saldo_cinpacoin FROM Monedero WHERE id_empleado = 1";
$result_monedero = $conn->query($sql_monedero);

$saldo_empleado = null;
if ($result_monedero && $result_monedero->num_rows > 0) {
    $row_monedero = $result_monedero->fetch_assoc();
    $saldo_empleado = $row_monedero["saldo_cinpacoin"];
}

// Consulta p productos
$sql_productos = "SELECT nombre, descripcion, precio_cinpacoin, stock, imagen FROM Productos LIMIT 3";
$result_productos = $conn->query($sql_productos);
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
                    <a class="nav-link active" aria-current="page" href="productos.php">PRODUCTOS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reciclaje">RECICLAJE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contacto">CONTACTO</a>
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

<main>
    <section id="inicio" class="tienda-info py-5 bg-light">
        <div class="container d-flex justify-content-around align-items-center">
            <img src="../assets/img/tienda cimpa.png" alt="Tienda CIMPA" class="img-fluid rounded shadow" style="max-width: 45%;">
            <div class="text-center text-md-start" style="max-width: 45%;">
                <h2>Tienda CIMPA</h2>
                <p class="lead">
                    Bienvenido a nuestra tienda ecológica CIMPA. Promovemos el reciclaje, la sostenibilidad
                    y productos responsables con el medio ambiente. ¡Únete a nuestro compromiso por un planeta más verde!
                </p>
            </div>
        </div>
    </section>

    <section id="quienes-somos" class="tienda-info-secundaria py-5">
        <div class="container d-flex justify-content-around align-items-center flex-row-reverse">
            <img src="../assets/img/reciclaje.png.png" alt="Compromiso CIMPA" class="img-fluid rounded shadow" style="max-width: 45%;">
            <div class="text-center text-md-end" style="max-width: 45%;">
                <h3>Nuestro Compromiso</h3>
                <p class="lead">
                    En CIMPA, nuestra pasión es ofrecerte productos que no solo sean de alta calidad, sino que también respeten nuestro planeta. Creemos en un consumo consciente y en el poder de las decisiones individuales para generar un impacto positivo.
                </p>
            </div>
        </div>
    </section>

    <section id="productos" class="productos-destacados py-5 bg-white">
        <div class="container text-center">
            <h2>Nuestros Productos Destacados</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-3 justify-content-center">
                <?php
                if ($result_productos && $result_productos->num_rows > 0) {
                    while($row = $result_productos->fetch_assoc()) {
                        echo '<div class="col">';
                        echo '<div class="card h-100 shadow">';
                        if (!empty($row["imagen"])) {
                            echo '<img src="' . htmlspecialchars($row["imagen"]) . '" class="card-img-top" alt="' . htmlspecialchars($row["nombre"]) . '">';
                        } else {
                            echo '<img src="../assets/img/producto_default.jpg" class="card-img-top" alt="' . htmlspecialchars($row["nombre"]) . '">';
                        }
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($row["nombre"]) . '</h5>';
                        echo '<p class="card-text">' . htmlspecialchars($row["descripcion"]) . '</p>';
                        echo '<p class="card-text"><small class="text-success fw-bold">Precio: ' . htmlspecialchars($row["precio_cinpacoin"]) . ' CinpaCoins</small></p>';
                        echo '<p class="card-text"><small class="text-muted">Stock: ' . htmlspecialchars($row["stock"]) . ' unidades</small></p>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No se encontraron productos destacados.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <section id="reciclaje" class="tienda-info-secundaria py-5 bg-light">
        <div class="container d-flex justify-content-around align-items-center">
            <div class="text-center text-md-start" style="max-width: 45%;">
                <h3>Reciclaje Responsable</h3>
                <p class="lead">
                    En CIMPA también ofrecemos espacios para la separación de residuos y educación ambiental.
                    Creemos que pequeñas acciones generan grandes cambios. ¡Infórmate sobre nuestros puntos de reciclaje!
                </p>
            </div>
            <img src="../assets/img/reciclaje.png" alt="Reciclaje CIMPA" class="img-fluid rounded shadow" style="max-width: 45%;">
        </div>
    </section>

    <section id="contacto" class="tienda-info py-5 bg-white">
        <div class="container d-flex justify-content-around align-items-center flex-row-reverse">
            <div class="text-center text-md-end" style="max-width: 45%;">
                <h2>¡Contáctanos!</h2>
                <p class="lead">
                    ¿Tienes alguna pregunta o sugerencia? No dudes en ponerte en contacto con nosotros. Estamos aquí para ayudarte a ser parte del cambio.
                </p>
                <p>Email: info@cimpatienda.com</p>
                <p>Teléfono: +34 123 456 789</p>
            </div>
            <img src="../assets/img/contacto.png" alt="Contacto" class="img-fluid rounded shadow" style="max-width: 45%;">
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