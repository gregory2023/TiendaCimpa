<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Disponibles - Tienda Ecológica CIMPA</title>
    <link rel="stylesheet" href="../assets/css/productos.css">
    <link rel="stylesheet" href="../assets/css/menu.css">
    <style>
        /* Estilo adicional para el nombre del empleado */
        .empleado-saludo {
            font-weight: 500;
            color: #495057;
        }
        .empleado-saludo .icono-usuario {
            margin-right: 0.3em;
            opacity: 0.7;
        }
        /* Estilos para la notificación de "Añadido al carrito" */
        .producto-feedback {
            font-size: 0.85em;
            color: green;
            text-align: center;
            margin-top: 8px;
            height: 20px; /* Para evitar saltos de layout */
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .producto-feedback.visible {
            opacity: 1;
        }
        .card-body { /* Para asegurar que el botón se alinea abajo si las descripciones varían */
            display: flex;
            flex-direction: column;
        }
        .card-text.producto-descripcion {
            flex-grow: 1; /* Empuja el contenido de abajo (precio, stock, botón) hacia abajo */
        }
        .card .d-grid { /* Para el botón */
            margin-top: auto; /* Alinea el botón al final de la card-body */
        }
    </style>
</head>
<body>
<?php
// Asegúrate de que esta ruta sea correcta a tu archivo de conexión
// Para pruebas, si no tienes conexionBd.php, puedes comentar la siguiente línea
// y descomentar la simulación de $conn más abajo.
include '../config/conexionBd.php';

$id_empleado_actual = 1; // ID del empleado
$nombre_empleado_actual = "Usuario"; // Valor por defecto
$saldo_empleado = null;

// Simulación de $conn si no se incluye o falla conexionBd.php
if (!isset($conn) || !($conn instanceof mysqli)) {
    // echo "<p style='color:red; text-align:center;'>Advertencia: No se pudo conectar a la BD, usando datos de simulación.</p>";
    class MockDBConnectionProducts { // Renombrar para evitar conflicto si se incluye en otra página con simulación
        public function query($sql) {
            // Simular num_rows para las consultas de productos
            if (strpos($sql, "FROM Productos") !== false || strpos($sql, "FROM productos_mascota") !== false) {
                return new class {
                    public $num_rows = 3; // Simular que hay 3 productos
                    private $count = 0;
                    private $data = [ // Datos de ejemplo
                        ["id_producto" => 1, "nombre" => "Camiseta Eco (S)", "descripcion" => "Algodón orgánico.", "precio_cinpacoin" => "30.00", "stock" => 10, "imagen" => "../assets/img/producto_default.jpg"],
                        ["id_producto" => 2, "nombre" => "Gorra Sostenible (S)", "descripcion" => "Materiales reciclados.", "precio_cinpacoin" => "25.00", "stock" => 5, "imagen" => ""],
                        ["id_producto" => 3, "nombre" => "Botella Reutilizable (S)", "descripcion" => "Conserva el planeta.", "precio_cinpacoin" => "20.00", "stock" => 12, "imagen" => "../assets/img/producto_default.jpg"]
                    ];
                    public function fetch_assoc() {
                        if ($this->count < $this->num_rows) {
                            return $this->data[$this->count++];
                        }
                        return null;
                    }
                    public function free() {}
                };
            }
            return new class { public $num_rows = 0; public function fetch_assoc() { return null; } public function free() {} };
        }
        public function prepare($sql) {
            return new class {
                public function bind_param(...$args){}
                public function execute(){}
                public function get_result(){
                    return new class {
                        public $num_rows = 0;
                        public function fetch_assoc(){return null;}
                        public function close(){}
                    };
                }
                public function close(){}
            };
        }
        public function close(){}
    }
    $conn = new MockDBConnectionProducts();

    // Datos simulados para que la página no se vea vacía
    $nombre_empleado_actual = "Carlos (Simulado)";
    $saldo_empleado = 1500;

    // Para que el bucle de productos funcione con la simulación
    $result_todos_productos = $conn->query("SELECT * FROM Productos");
    $result_productos_mascota = $conn->query("SELECT * FROM productos_mascota");


} else { // Si hay conexión real
    // Obtener el nombre del empleado (ID = 1)
    $sql_nombre = "SELECT nombre FROM Empleado WHERE id_empleado = ?";
    if ($stmt_nombre = $conn->prepare($sql_nombre)) {
        $stmt_nombre->bind_param("i", $id_empleado_actual);
        $stmt_nombre->execute();
        $result_nombre_db = $stmt_nombre->get_result();
        if ($result_nombre_db && $result_nombre_db->num_rows > 0) {
            $row_nombre = $result_nombre_db->fetch_assoc();
            $nombre_empleado_actual = $row_nombre["nombre"];
        }
        $stmt_nombre->close();
    }

    // Obtener el saldo del empleado
    $sql_monedero = "SELECT saldo_cinpacoin FROM Monedero WHERE id_empleado = ?";
    if ($stmt_monedero = $conn->prepare($sql_monedero)) {
        $stmt_monedero->bind_param("i", $id_empleado_actual);
        $stmt_monedero->execute();
        $result_monedero_db = $stmt_monedero->get_result();
        if ($result_monedero_db && $result_monedero_db->num_rows > 0) {
            $row_monedero = $result_monedero_db->fetch_assoc();
            $saldo_empleado = $row_monedero["saldo_cinpacoin"];
        }
        $stmt_monedero->close();
    }

    // Consulta para obtener todos los productos generales con stock > 0
    $sql_todos_productos = "SELECT id_producto, nombre, descripcion, precio_cinpacoin, stock, imagen FROM Productos WHERE stock > 0";
    $result_todos_productos = $conn->query($sql_todos_productos);

    // Consulta para obtener todos los productos para mascotas
    $sql_productos_mascota = "SELECT id_producto, nombre, descripcion, precio_cinpacoin, imagen FROM productos_mascota";
    $result_productos_mascota = $conn->query($sql_productos_mascota);
}
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
                    <a class="nav-link" aria-current="page" href="menu.php">INICIO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="menu.php#reciclaje">RECICLAJE</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="menu.php#contacto">CONTACTO</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3 empleado-saludo">
                    <span class="icono-usuario">👤</span>Hola, <?php echo htmlspecialchars($nombre_empleado_actual); ?>
                </span>

                <?php if ($saldo_empleado !== null): ?>
                    <a href="huellaCarbono.php" class="puntos-empleado bg-success text-white rounded-pill px-3 py-2 me-2 text-decoration-none">
                        <span class="icono-puntos">💰</span> <?php echo htmlspecialchars(number_format($saldo_empleado, 0)); ?> Puntos
                    </a>
                <?php else: ?>
                    <span class="puntos-empleado bg-secondary text-white rounded-pill px-3 py-2 me-2 text-decoration-none">
                        <span class="icono-puntos">💰</span> --- Puntos
                    </span>
                <?php endif; ?>
                <a href="carrito.php" class="icon-btn btn btn-outline-secondary rounded-circle" aria-label="Carrito">
                    <img src="../assets/img/carritologo.png" alt="Carrito" height="25">
                </a>
            </div>
        </div>
    </nav>
</header>

<main class="container py-5">
    <h1 class="mb-4 text-center">Productos Disponibles</h1>

    <section id="productos-generales" class="py-4">
        <h2 class="mb-3">Productos Generales</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            if ($result_todos_productos && $result_todos_productos->num_rows > 0) {
                while($row_producto = $result_todos_productos->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100 shadow producto-card">';
                    $ruta_imagen_general = '../assets/img/producto_default.jpg'; // Default
                    if (!empty($row_producto["imagen"])) {
                        $posible_ruta = $row_producto["imagen"];
                        if (strpos($posible_ruta, '../') !== 0 && file_exists('../' . $posible_ruta)) {
                            $ruta_imagen_general = '../' . htmlspecialchars($posible_ruta);
                        } elseif (file_exists($posible_ruta)) {
                            $ruta_imagen_general = htmlspecialchars($posible_ruta);
                        }
                    }
                    echo '<img src="' . $ruta_imagen_general . '" class="card-img-top producto-imagen" alt="' . htmlspecialchars($row_producto["nombre"]) . '">';
                    echo '<div class="card-body d-flex flex-column">';
                    echo '<h5 class="card-title producto-titulo">' . htmlspecialchars($row_producto["nombre"]) . '</h5>';
                    echo '<p class="card-text producto-descripcion flex-grow-1">' . htmlspecialchars($row_producto["descripcion"]) . '</p>';
                    echo '<p class="card-text"><small class="text-success fw-bold producto-precio">Precio: ' . htmlspecialchars(number_format(floatval($row_producto["precio_cinpacoin"]), 2)) . ' CimpaCoins</small></p>';
                    echo '<p class="card-text"><small class="text-muted producto-stock">Stock: ' . htmlspecialchars($row_producto["stock"]) . ' unidades</small></p>';
                    echo '<div class="producto-feedback"></div>';
                    echo '<div class="d-grid gap-2 mt-auto">';
                    echo '<button class="btn btn-outline-primary btn-sm agregar-carrito" data-id="' . htmlspecialchars($row_producto["id_producto"]) . '" data-nombre="' . htmlspecialchars($row_producto["nombre"]) . '">Añadir al Carrito</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="lead text-center">No hay productos generales disponibles en este momento.</p></div>';
            }
            if ($result_todos_productos instanceof mysqli_result) {
                $result_todos_productos->free();
            }
            ?>
        </div>
    </section>

    <hr class="my-5"> <section id="productos-mascota" class="py-4">
        <h2 class="mb-3">Productos para Mascotas</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php
            if ($result_productos_mascota && $result_productos_mascota->num_rows > 0) {
                while($row_mascota = $result_productos_mascota->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100 shadow producto-card">';
                    $ruta_imagen_mascota = '../assets/img/producto_default_mascota.jpg'; // Default
                    if (!empty($row_mascota["imagen"])) {
                        $posible_ruta = $row_mascota["imagen"];
                        if (strpos($posible_ruta, '../') !== 0 && file_exists('../' . $posible_ruta)) {
                            $ruta_imagen_mascota = '../' . htmlspecialchars($posible_ruta);
                        } elseif (file_exists($posible_ruta)) {
                            $ruta_imagen_mascota = htmlspecialchars($posible_ruta);
                        }
                    }
                    echo '<img src="' . $ruta_imagen_mascota . '" class="card-img-top producto-imagen" alt="' . htmlspecialchars($row_mascota["nombre"]) . '">';
                    echo '<div class="card-body d-flex flex-column">';
                    echo '<h5 class="card-title producto-titulo">' . htmlspecialchars($row_mascota["nombre"]) . '</h5>';
                    echo '<p class="card-text producto-descripcion flex-grow-1">' . htmlspecialchars($row_mascota["descripcion"]) . '</p>';
                    echo '<p class="card-text"><small class="text-success fw-bold producto-precio">Precio: ' . htmlspecialchars(number_format(floatval($row_mascota["precio_cinpacoin"]), 2)) . ' CimpaCoins</small></p>';
                    echo '<div class="producto-feedback"></div>';
                    echo '<div class="d-grid gap-2 mt-auto">';
                    echo '<button class="btn btn-outline-primary btn-sm agregar-carrito-mascota" data-id="' . htmlspecialchars($row_mascota["id_producto"]) . '" data-nombre="' . htmlspecialchars($row_mascota["nombre"]) . '">Añadir al Carrito</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="lead text-center">No hay productos para mascotas disponibles en este momento.</p></div>';
            }
            if ($result_productos_mascota instanceof mysqli_result) {
                $result_productos_mascota->free();
            }
            ?>
        </div>
    </section>
</main>

<footer class="footerz bg-dark text-light py-3 mt-5"> <div class="container text-center">
        <a href="https://twitter.com/cimpa_plm" target="_blank" rel="noopener" class="text-light mx-2">
            <img src="../assets/img/logo_x.png" alt="Twitter" width="34" height="34"></a>
        <a href="https://fr.linkedin.com/company/cimpa-plm-services" target="_blank" rel="noopener" class="text-light mx-2">
            <img src="../assets/img/logo_linkedin.png" alt="LinkedIn" width="34" height="34"></a>
        <a href="https://www.youtube.com/channel/UCvDeDvVG3vRIlao7eVTYt_A" target="_blank" rel="noopener" class="text-light mx-2">
            <img src="../assets/img/logo_youtube.png" alt="YouTube" width="34" height="34"></a>
    </div>
    <?php
    if ($conn instanceof mysqli && !$modo_simulacion_activo) {
        $conn->close();
    }
    ?>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const botonesCarrito = document.querySelectorAll('.agregar-carrito, .agregar-carrito-mascota');

        botonesCarrito.forEach(boton => {
            boton.addEventListener('click', function (event) {
                const botonActual = event.target;
                const productoId = botonActual.dataset.id;
                const productoNombre = botonActual.dataset.nombre || 'Producto';

                const textoOriginal = botonActual.innerHTML;
                botonActual.innerHTML = '¡Añadido!';
                botonActual.disabled = true;

                const cardBody = botonActual.closest('.card-body');
                let feedbackDiv = cardBody.querySelector('.producto-feedback');
                // Si no existe el div de feedback, lo crea (aunque debería estar)
                if (!feedbackDiv) {
                    feedbackDiv = document.createElement('div');
                    feedbackDiv.classList.add('producto-feedback');
                    // Insertar antes del div del botón
                    botonActual.closest('.d-grid').parentNode.insertBefore(feedbackDiv, botonActual.closest('.d-grid'));
                }

                feedbackDiv.textContent = `${productoNombre} añadido al carrito.`;
                feedbackDiv.classList.add('visible');

                setTimeout(() => {
                    botonActual.innerHTML = textoOriginal;
                    botonActual.disabled = false;
                    if (feedbackDiv) {
                        feedbackDiv.classList.remove('visible');
                        setTimeout(() => { feedbackDiv.textContent = ''; }, 500);
                    }
                }, 2000);

                console.log(`Producto (ID: ${productoId}, Nombre: ${productoNombre}) añadido al carrito.`);
                // Aquí llamarías a tu función de carrito.js si es necesario
                // Ejemplo: if (typeof anadirAlCarritoGlobal === 'function') { anadirAlCarritoGlobal(productoId, productoNombre); }
            });
        });
    });
</script>
</body>
</html>
