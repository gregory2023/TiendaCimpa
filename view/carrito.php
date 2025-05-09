<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Tienda Ecológica CIMPA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6; /* Un fondo suave */
            font-family: 'Inter', sans-serif; /* Coherencia con otras vistas */
            padding-top: 20px; /* Espacio para el header */
        }
        .header-carrito {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
        }
        .logo-carrito {
            height: 60px;
        }
        .btn-volver {
            background-color: #6c757d; /* Gris Bootstrap */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-volver:hover {
            background-color: #5a6268;
        }

        .saldo-empleado {
            background-color: #e9f5ff;
            border: 1px solid #b3d7ff;
            color: #004085;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.1em;
        }
        .saldo-empleado strong {
            font-weight: 600;
        }

        .cart-section-title {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #198754; /* Verde CIMPA */
            display: inline-block;
        }

        .product-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            padding: 20px;
            display: flex;
            align-items: center;
            transition: box-shadow 0.3s ease;
        }
        .product-card:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .product-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }
        .product-info {
            flex-grow: 1;
        }
        .product-info h5 {
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }
        .product-info p {
            margin-bottom: 8px;
            color: #555;
            font-size: 0.95em;
        }
        .product-price {
            font-weight: bold;
            color: #198754; /* Verde CIMPA */
        }

        .counter {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .counter button {
            background-color: #0d6efd; /* Azul Bootstrap */
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .counter button:hover {
            background-color: #0b5ed7;
        }
        .counter span {
            margin: 0 15px;
            font-size: 1.1em;
            font-weight: 500;
            min-width: 20px; /* Para que no salte al cambiar número */
            text-align: center;
        }

        .payment-summary {
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        .payment-summary h3 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }
        .btn-confirmar-compra {
            background-color: #198754; /* Verde CIMPA */
            color: white;
            padding: 12px 25px;
            font-size: 1.1em;
            border-radius: 25px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .btn-confirmar-compra:hover {
            background-color: #157347;
        }
        #confirmacion-compra button {
            margin: 5px;
            border-radius: 20px;
            padding: 8px 15px;
        }
        .swal2-confirm, .swal2-cancel {
            border-radius: 20px !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-carrito">
        <img src="../assets/img/logo.png" alt="Logo CIMPA" class="logo-carrito">
        <a href="productos.php" class="btn btn-volver">
            <i class="fas fa-arrow-left me-2"></i>Volver a Productos
        </a>
    </div>

    <?php
    // Asegúrate de que esta ruta sea correcta a tu archivo de conexión
    // Para pruebas, si no tienes conexionBd.php, puedes comentar la siguiente línea
    // y descomentar la simulación de $conn más abajo.
    include '../config/conexionBd.php';

    $id_empleado_actual = 1; // Asumimos ID 1 para el ejemplo
    $saldo_empleado = 0;
    $nombre_empleado_actual = "Carlos"; // Valor por defecto

    // Simulación de $conn si no se incluye o falla conexionBd.php
    if (!isset($conn) || !($conn instanceof mysqli)) {
        // echo "<p style='color:red; text-align:center;'>Advertencia: No se pudo conectar a la BD, usando datos de simulación.</p>";
        class MockDBConnectionCarrito {
            public function query($sql) {
                if (strpos($sql, "FROM Productos") !== false) {
                    return new class {
                        public $num_rows = 2;
                        private $count = 0;
                        private $data = [
                            ["id_producto" => 1, "nombre" => "Camiseta Eco (Simulada)", "imagen" => "../assets/img/producto_default.jpg", "precio_cinpacoin" => "30.00"],
                            ["id_producto" => 2, "nombre" => "Gorra Sostenible (Simulada)", "imagen" => "../assets/img/producto_default.jpg", "precio_cinpacoin" => "25.00"]
                        ];
                        public function fetch_assoc(){ if ($this->count < $this->num_rows) { return $this->data[$this->count++]; } return null; }
                        public function free() {}
                    };
                } elseif (strpos($sql, "FROM productos_mascota") !== false) {
                    return new class {
                        public $num_rows = 1;
                        private $count = 0;
                        private $data = [
                            ["id_producto" => 101, "nombre" => "Collar Mascota Eco (Simulado)", "imagen" => "../assets/img/producto_default_mascota.jpg", "precio_cinpacoin" => "18.00"]
                        ];
                        public function fetch_assoc(){ if ($this->count < $this->num_rows) { return $this->data[$this->count++]; } return null; }
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
                        // Simular saldo para empleado 1
                        if (strpos($this->bound_sql, "FROM Monedero") !== false) {
                            return new class { public $num_rows = 1; public function fetch_assoc(){return ['saldo_cinpacoin' => 2500];} public function close(){} };
                        }
                        return new class { public $num_rows = 0; public function fetch_assoc(){return null;} public function close(){} };
                    }
                    public function close(){}
                    public $bound_sql = ''; // Para ayudar a la simulación de get_result
                    public function __construct() {
                        global $sql; // Acceder a la variable $sql global para simulación
                        $this->bound_sql = $sql;
                    }
                };
            }
            public function close(){}
            public function set_charset($charset) {} // Añadir para compatibilidad
        }
        $conn = new MockDBConnectionCarrito();
        $saldo_empleado = 2500; // Saldo simulado
        $nombre_empleado_actual = "Carlos (Simulado)";

    } else { // Si hay conexión real
        if ($conn instanceof mysqli) { $conn->set_charset("utf8mb4"); }

        // Obtener saldo actual del empleado
        $sql_saldo = "SELECT saldo_cinpacoin FROM Monedero WHERE id_empleado = ?";
        if ($stmt_saldo = $conn->prepare($sql_saldo)) {
            $stmt_saldo->bind_param("i", $id_empleado_actual);
            $stmt_saldo->execute();
            $result_saldo = $stmt_saldo->get_result();
            if ($result_saldo && $result_saldo->num_rows > 0) {
                $row_saldo = $result_saldo->fetch_assoc();
                $saldo_empleado = $row_saldo["saldo_cinpacoin"];
            }
            $stmt_saldo->close();
        }
    }

    // Mostrar saldo del empleado
    if ($saldo_empleado !== null) {
        echo '<div class="saldo-empleado">';
        echo 'Hola, <strong>' . htmlspecialchars($nombre_empleado_actual) . '</strong>. Tienes <strong>' . htmlspecialchars(number_format($saldo_empleado, 0)) . '</strong> CimpaCoins disponibles.';
        echo '</div>';
    }
    ?>

    <h2 class="cart-section-title">Tu Carrito <i class="fas fa-shopping-cart ms-2"></i></h2>

    <div class="cart-items mb-4">
        <?php
        // Asumimos que los productos en el carrito vienen de alguna fuente (sesión, BD temporal, etc.)
        // Por ahora, mostraremos algunos productos generales y uno de mascota como si estuvieran en el carrito.

        // Productos Generales (ejemplo, puedes adaptar esto para que lea de un carrito real)
        $sql_generales = "SELECT id_producto, nombre, imagen, precio_cinpacoin FROM Productos WHERE stock > 0 LIMIT 2"; // Tomamos 2 para el ejemplo
        $result_generales = $conn->query($sql_generales);
        $totalPuntos = 0;

        if ($result_generales && $result_generales->num_rows > 0) {
            while ($row = $result_generales->fetch_assoc()) {
                echo '<div class="product-card">';
                $ruta_imagen = (!empty($row["imagen"]) && file_exists($row["imagen"])) ? htmlspecialchars($row["imagen"]) : '../assets/img/producto_default.jpg';
                echo '<img src="' . $ruta_imagen . '" alt="' . htmlspecialchars($row["nombre"]) . '">';
                echo '<div class="product-info">';
                echo '<h5>' . htmlspecialchars($row["nombre"]) . '</h5>';
                echo '<p class="product-price">' . htmlspecialchars(number_format(floatval($row["precio_cinpacoin"]), 2)) . ' CimpaCoins</p>';
                echo '<div class="counter">';
                echo '<button onclick="restarPuntos(' . $row["id_producto"] . ', ' . floatval($row["precio_cinpacoin"]) . ')"><i class="fas fa-minus"></i></button>';
                echo '<span id="cantidad-' . $row["id_producto"] . '">1</span>';
                echo '<button onclick="actualizarPuntos(' . $row["id_producto"] . ', ' . floatval($row["precio_cinpacoin"]) . ')"><i class="fas fa-plus"></i></button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                $totalPuntos += floatval($row["precio_cinpacoin"]);
            }
        }

        // Producto para Mascota (ejemplo)
        $sql_mascota_carrito = "SELECT id_producto, nombre, imagen, precio_cinpacoin FROM productos_mascota LIMIT 1"; // Tomamos 1 para el ejemplo
        $result_mascota_carrito = $conn->query($sql_mascota_carrito);

        if ($result_mascota_carrito && $result_mascota_carrito->num_rows > 0) {
            $row_mascota = $result_mascota_carrito->fetch_assoc();
            echo '<div class="product-card">';
            $ruta_imagen_mascota = (!empty($row_mascota["imagen"]) && file_exists($row_mascota["imagen"])) ? htmlspecialchars($row_mascota["imagen"]) : '../assets/img/producto_default_mascota.jpg';
            echo '<img src="' . $ruta_imagen_mascota . '" alt="' . htmlspecialchars($row_mascota["nombre"]) . '">';
            echo '<div class="product-info">';
            echo '<h5>' . htmlspecialchars($row_mascota["nombre"]) . ' (Para Mascota)</h5>';
            echo '<p class="product-price">' . htmlspecialchars(number_format(floatval($row_mascota["precio_cinpacoin"]),2)) . ' CimpaCoins</p>';
            echo '<div class="counter">';
            echo '<button onclick="restarPuntos(\'mascota-' . $row_mascota["id_producto"] . '\', ' . floatval($row_mascota["precio_cinpacoin"]) . ')"><i class="fas fa-minus"></i></button>';
            // Usar un ID único para la cantidad del producto de mascota
            echo '<span id="cantidad-mascota-' . $row_mascota["id_producto"] . '">1</span>';
            echo '<button onclick="actualizarPuntos(\'mascota-' . $row_mascota["id_producto"] . '\', ' . floatval($row_mascota["precio_cinpacoin"]) . ')"><i class="fas fa-plus"></i></button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $totalPuntos += floatval($row_mascota["precio_cinpacoin"]);
        }

        if (($result_generales && $result_generales->num_rows == 0) && ($result_mascota_carrito && $result_mascota_carrito->num_rows == 0)) {
            echo "<p class='text-center lead mt-3'>Tu carrito está vacío.</p>";
        }

        if ($conn instanceof mysqli && !$modo_simulacion_activo) {
            // No cerramos la conexión aquí si la simulación la creó,
            // o si necesitas $conn más adelante en la página.
            // $conn->close();
        }
        ?>
    </div>

    <div class="payment-summary">
        <h3>Total del Carrito: <span id="total-puntos" class="text-success fw-bold"><?php echo htmlspecialchars(number_format($totalPuntos, 2)); ?></span> CimpaCoins</h3>
        <button class="btn btn-confirmar-compra w-100" onclick="mostrarConfirmacion()">
            <i class="fas fa-check-circle me-2"></i>CONFIRMAR COMPRA (<span id="confirmar-total"><?php echo htmlspecialchars(number_format($totalPuntos, 2)); ?></span> CimpaCoins)
        </button>
        <div id="confirmacion-compra" class="mt-3"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Almacena los precios originales para evitar recalcular desde el DOM
    const preciosOriginales = {};
    <?php
    // Reiniciar y re-ejecutar consultas para obtener precios para JS
    // Esto es un poco ineficiente, idealmente pasarías estos datos de otra forma (ej. JSON)
    // o los cargarías una vez y los usarías tanto en PHP como en JS.
    if ($conn instanceof mysqli || $modo_simulacion_activo) { // Solo si hay conexión o simulación
        if (!$modo_simulacion_activo) { // Si es conexión real, re-ejecutar
            $result_generales_js = $conn->query($sql_generales);
            $result_mascota_carrito_js = $conn->query($sql_mascota_carrito);
        } else { // Si es simulación, usar los resultados ya simulados
            $result_generales_js = $conn->query("SELECT * FROM Productos"); // Re-simular
            $result_mascota_carrito_js = $conn->query("SELECT * FROM productos_mascota"); // Re-simular
        }

        if ($result_generales_js && $result_generales_js->num_rows > 0) {
            while ($row_js = $result_generales_js->fetch_assoc()) {
                echo "preciosOriginales['{$row_js['id_producto']}'] = " . floatval($row_js['precio_cinpacoin']) . ";\n";
            }
        }
        if ($result_mascota_carrito_js && $result_mascota_carrito_js->num_rows > 0) {
            while ($row_mascota_js = $result_mascota_carrito_js->fetch_assoc()) { // Cambiado a $row_mascota_js
                echo "preciosOriginales['mascota-{$row_mascota_js['id_producto']}'] = " . floatval($row_mascota_js['precio_cinpacoin']) . ";\n";
            }
        }
        if (!$modo_simulacion_activo && $conn instanceof mysqli) {
            // No cerramos $conn aquí, se cierra al final del script PHP principal
        }
    }
    ?>

    let totalActualPuntos = <?php echo $totalPuntos; ?>;

    function actualizarTotalGlobal() {
        document.getElementById("total-puntos").innerText = totalActualPuntos.toFixed(2);
        document.getElementById("confirmar-total").innerText = totalActualPuntos.toFixed(2);
    }

    function actualizarPuntos(idProducto, precioUnitario) {
        let cantidadElemento = document.getElementById(`cantidad-${idProducto}`);
        let cantidad = parseInt(cantidadElemento.innerText);

        // precioUnitario = preciosOriginales[idProducto]; // Usar precio original

        if (cantidad < 20) { // Límite de cantidad
            cantidad++;
            cantidadElemento.innerText = cantidad;
            totalActualPuntos += precioUnitario;
            actualizarTotalGlobal();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Límite alcanzado',
                text: 'No puedes añadir más de 20 unidades de este producto.',
                confirmButtonColor: '#ffc107'
            });
        }
    }

    function restarPuntos(idProducto, precioUnitario) {
        let cantidadElemento = document.getElementById(`cantidad-${idProducto}`);
        let cantidad = parseInt(cantidadElemento.innerText);

        // precioUnitario = preciosOriginales[idProducto]; // Usar precio original

        if (cantidad > 1) { // Límite mínimo de cantidad
            cantidad--;
            cantidadElemento.innerText = cantidad;
            totalActualPuntos -= precioUnitario;
            actualizarTotalGlobal();
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Cantidad mínima',
                text: 'La cantidad mínima para este producto es 1.',
                confirmButtonColor: '#0dcaf0'
            });
        }
    }

    function mostrarConfirmacion() {
        let totalPuntosConfirmar = document.getElementById("total-puntos").innerText;
        let saldoDisponible = <?php echo $saldo_empleado ?? 0; ?>;

        if (parseFloat(totalPuntosConfirmar) > saldoDisponible) {
            Swal.fire({
                title: "¡Saldo Insuficiente!",
                text: `No tienes suficientes CimpaCoins para esta compra. Necesitas ${totalPuntosConfirmar} y tienes ${saldoDisponible}.`,
                icon: "error",
                confirmButtonText: "Entendido",
                confirmButtonColor: "#dc3545"
            });
            return;
        }

        Swal.fire({
            title: '¿Confirmar Compra?',
            html: `Vas a gastar <strong>${totalPuntosConfirmar} CimpaCoins</strong>. ¿Estás seguro?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-check"></i> Sí, confirmar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                confirmarCompraFinal();
            }
        });
    }

    function confirmarCompraFinal() {
        // Aquí iría la lógica para enviar la compra al servidor (AJAX)
        // Por ejemplo:
        // fetch('procesar_compra.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ items: obtenerItemsDelCarrito(), total: totalActualPuntos })
        // })
        // .then(response => response.json())
        // .then(data => {
        //     if(data.success) {
        //         Swal.fire("¡Compra Exitosa! 🎉", "Gracias por tu compra. Disfruta tus productos.", "success");
        //         // Aquí podrías redirigir, limpiar el carrito, actualizar saldo, etc.
        //     } else {
        //         Swal.fire("Error", data.message || "No se pudo procesar la compra.", "error");
        //     }
        // })
        // .catch(error => Swal.fire("Error", "Ocurrió un problema con la conexión.", "error"));

        // Simulación de éxito por ahora:
        Swal.fire({
            title: "¡Compra Exitosa! 🎉",
            text: "Gracias por tu compra. Disfruta tus productos.",
            icon: "success",
            confirmButtonText: "Aceptar",
            confirmButtonColor: "#198754"
        }).then(() => {
            // Opcional: Limpiar carrito o redirigir
            // window.location.href = 'gracias_por_tu_compra.php';
        });
    }
    // Si tienes una función global para cerrar la conexión en tu PHP principal, asegúrate que se llama
    // <?php if ($conn instanceof mysqli && !$modo_simulacion_activo) { $conn->close(); } ?>
</script>
</body>
</html>
