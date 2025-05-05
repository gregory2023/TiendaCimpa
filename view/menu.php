<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Ecológica CIMPA</title>
    <link rel="stylesheet" href="../assets/css/menu.css">
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
$sql_productos = "SELECT nombre, descripcion, precio_cinpacoin, stock, imagen FROM Productos LIMIT 2";
$result_productos = $conn->query($sql_productos);
?>

<header class="site-header">
    <div class="container">
        <a href="menu.php" class="logo">
            <img src="../assets/img/logo.png" alt="Logo">
        </a>
        <nav class="main-nav">
            <ul>
                <li><a href="#productos">PRODUCTOS</a></li>
                <li><a href="#quienes-somos">QUIÉNES SOMOS</a></li>
                <li><a href="#reciclaje">RECICLAJE</a></li>
                <li><a href="#contacto">CONTACTO</a></li>
            </ul>
        </nav>
        <div class="actions">
            <div class="search">
                <input type="text" placeholder="Buscar">
                <button aria-label="Buscar"></button>
            </div>
            <?php if ($saldo_empleado !== null): ?>
                <div class="puntos-empleado">
                    <span class="icono-puntos">💰</span> <?php echo htmlspecialchars(number_format($saldo_empleado, 0)); ?> Puntos
                </div>
            <?php endif; ?>
            <a href="carrito.php" class="icon-btn" aria-label="Carrito">
                <img src="../assets/img/carritologo.png" alt="Carrito" class="icon-img">
            </a>
        </div>
    </div>
</header>

<section id="inicio" class="tienda-info">
    <img src="../assets/img/tienda cimpa.png" alt="Tienda CIMPA" class="imagen-tienda">
    <div class="descripcion-tienda">
        <h2>Tienda CIMPA</h2>
        <p>
            Bienvenido a nuestra tienda ecológica CIMPA. Promovemos el reciclaje, la sostenibilidad
            y productos responsables con el medio ambiente. ¡Únete a nuestro compromiso por un planeta más verde!
        </p>
    </div>
</section>

<section id="quienes-somos" class="tienda-info-secundaria">
    <div class="descripcion-secundaria">
        <h3>Nuestro Compromiso</h3>
        <p>
            En CIMPA, nuestra pasión es ofrecerte productos que no solo sean de alta calidad, sino que también respeten nuestro planeta. Creemos en un consumo consciente y en el poder de las decisiones individuales para generar un impacto positivo.
        </p>
    </div>
    <img src="../assets/img/reciclaje.png.png" alt="Compromiso CIMPA" class="imagen-secundaria">
</section>

<section id="productos" class="productos-destacados">
    <h2>Nuestros Productos Destacados</h2>
    <div class="lista-productos">
        <?php
        if ($result_productos && $result_productos->num_rows > 0) {
            while($row = $result_productos->fetch_assoc()) {
                echo '<div class="producto-card">';
                if (!empty($row["imagen"])) {
                    echo '<img src="' . htmlspecialchars($row["imagen"]) . '" alt="' . htmlspecialchars($row["nombre"]) . '">';
                } else {
                    echo '<img src="../assets/img/producto_default.jpg" alt="' . htmlspecialchars($row["nombre"]) . '"> ';
                }
                echo '<h3>' . htmlspecialchars($row["nombre"]) . '</h3>';
                echo '<p>' . htmlspecialchars($row["descripcion"]) . '</p>';
                echo '<p class="precio">Precio: ' . htmlspecialchars($row["precio_cinpacoin"]) . ' CinpaCoins</p>';
                echo '<p class="stock">Stock: ' . htmlspecialchars($row["stock"]) . ' unidades</p>';
                echo '</div>';
            }
        } else {
            echo "<p>No se encontraron productos destacados.</p>";
        }
        ?>
    </div>
</section>

<section id="reciclaje" class="tienda-info-secundaria">
    <div class="descripcion-secundaria">
        <h3>Reciclaje Responsable</h3>
        <p>
            En CIMPA también ofrecemos espacios para la separación de residuos y educación ambiental.
            Creemos que pequeñas acciones generan grandes cambios. ¡Infórmate sobre nuestros puntos de reciclaje!
        </p>
    </div>
    <img src="../assets/img/reciclaje.png" alt="Reciclaje CIMPA" class="imagen-secundaria">
</section>

<section id="contacto" class="tienda-info">
    <div class="descripcion-tienda">
        <h2>¡Contáctanos!</h2>
        <p>
            ¿Tienes alguna pregunta o sugerencia? No dudes en ponerte en contacto con nosotros. Estamos aquí para ayudarte a ser parte del cambio.
        </p>
        <p>Email: info@cimpatienda.com</p>
        <p>Teléfono: +34 123 456 789</p>
    </div>
    <img src="../assets/img/contacto.png" alt="Contacto CIMPA" class="imagen-tienda">
</section>

<footer class="footerz">
    <p>
        Síguenos en
        <a href="https://twitter.com/cimpa_plm" target="_blank" rel="noopener">Twitter</a> |
        <a href="https://fr.linkedin.com/company/cimpa-plm-services" target="_blank" rel="noopener">LinkedIn</a> |
        <a href="https://www.youtube.com/channel/UCvDeDvVG3vRIlao7eVTYt_A" target="_blank" rel="noopener">YouTube</a>
    </p>
</footer>
</body>
</html>