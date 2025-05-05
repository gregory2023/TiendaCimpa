<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../assets/css/carrito.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function actualizarPuntos(id, precio) {
            let cantidadElemento = document.getElementById(`cantidad-${id}`);
            let cantidad = parseInt(cantidadElemento.innerText);
            let totalElemento = document.getElementById("total-puntos");
            let totalBoton = document.getElementById("confirmar-total");

            let total = parseInt(totalElemento.innerText);

            if (cantidad < 20) {
                total += precio;
                cantidad++;
                cantidadElemento.innerText = cantidad;
                totalElemento.innerText = total;
                totalBoton.innerText = total;
            } else {
                console.log("No puedes superar los 20 puntos para el producto con ID " + id + ".");
            }
        }

        function restarPuntos(id, precio) {
            let cantidadElemento = document.getElementById(`cantidad-${id}`);
            let cantidad = parseInt(cantidadElemento.innerText);
            let totalElemento = document.getElementById("total-puntos");
            let totalBoton = document.getElementById("confirmar-total");

            let total = parseInt(totalElemento.innerText);

            if (cantidad > 1) {
                total -= precio;
                cantidad--;
                cantidadElemento.innerText = cantidad;
                totalElemento.innerText = total;
                totalBoton.innerText = total;
            } else {
                console.log("No puedes bajar de 1 punto para el producto con ID " + id + ".");
            }
        }

        function mostrarConfirmacion() {
            let confirmacionDiv = document.getElementById("confirmacion-compra");
            let totalPuntos = document.getElementById("total-puntos").innerText;

            confirmacionDiv.innerHTML = `
                <div style="
                    margin-top: 15px;
                    padding: 10px;
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                    border-radius: 5px;
                    text-align: center;
                    font-size: 16px;">
                    ¿Seguro que quieres confirmar la compra por <strong>${totalPuntos} CIMPA COINS</strong>?
                    <br>
                    <button onclick="confirmarCompra()" style="
                        margin-top: 10px;
                        padding: 8px 12px;
                        background-color: #28a745;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;">
                        ✅ Confirmar
                    </button>
                    <button onclick="cancelarCompra()" style="
                        margin-top: 10px;
                        padding: 8px 12px;
                        background-color: #dc3545;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;">
                        ❌ Cancelar
                    </button>
                </div>
            `
        }

        function cancelarCompra() {
            document.getElementById("confirmacion-compra").innerHTML = ""; // Oculta los botones
        }

        function confirmarCompra() {
            document.getElementById("confirmacion-compra").innerHTML = ""; // Oculta los botones

            // Aquí podrías enviar la información de la compra al servidor
            // (por ejemplo, usando AJAX)

            // Mostrar alerta chula con SweetAlert2
            Swal.fire({
                title: "¡Compra Exitosa! 🎉",
                text: "Gracias por tu compra. Disfruta tus productos.",
                icon: "success",
                confirmButtonText: "Aceptar",
                confirmButtonColor: "#28a745"
            });
        }
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="../assets/img/logo.png" alt="Logo" class="logo">
        <a href="menu.php">
            <button class="menu2">VOLVER</button>
        </a>
    </div>

    <div class="cart">
        <?php
                include '../config/conexionBd.php';

                $sql = "SELECT id_producto, nombre, imagen, precio_cinpacoin FROM Productos WHERE stock > 0";
        $result = $conn->query($sql);

        $totalPuntos = 0;

        if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
        echo '<div class="product">';
        echo '<img src="' . htmlspecialchars($row["imagen"]) . '" alt="' . htmlspecialchars($row["nombre"]) . '" class="clickable">';
        echo '<h3 class="clickable">' . htmlspecialchars($row["nombre"]) . '</h3>';
        echo '<div class="counter">';
            echo '<button onclick="restarPuntos(' . $row["id_producto"] . ', ' . $row["precio_cinpacoin"] . ')">-</button>';
            echo '<span id="cantidad-' . $row["id_producto"] . '">1</span>';
            echo '<button onclick="actualizarPuntos(' . $row["id_producto"] . ', ' . $row["precio_cinpacoin"] . ')">+</button>';
            echo '</div>';
        echo '<p>' . htmlspecialchars($row["precio_cinpacoin"]) . ' CIMPA COINS</p>';
        echo '</div>';
        $totalPuntos += $row["precio_cinpacoin"]; // Suma el precio inicial (asumiendo cantidad 1)
        }
        } else {
        echo "<p>No hay productos disponibles en este momento.</p>";
        }

        $conn->close();
        ?>
    </div>

    <div class="payment">
        <h3>Total: <span id="total-puntos"><?php echo htmlspecialchars($totalPuntos); ?></span> CIMPA COINS</h3>
        <button class="menu2" onclick="mostrarConfirmacion()">CONFIRMAR COMPRA (<span id="confirmar-total"><?php echo htmlspecialchars($totalPuntos); ?></span> CIMPA COINS)</button>

        <div id="confirmacion-compra"></div>
    </div>
</div>
</body>
</html>