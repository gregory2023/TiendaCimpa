<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tienda Ecológica CIMPA</title>
  <link rel="stylesheet" href="../assets/css/menu.css">
</head>
<body>
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
        <a href="#" class="icon-btn" aria-label="Saldo">
          <img src="../assets/img/carteralogo.png" alt="Saldo" class="icon-img">
        </a>
        <a href="carrito.html" class="icon-btn" aria-label="Carrito">
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

         include '../config/conexionBd.php';

        // Consulta para obtener los dos primeros productos
        $sql = "SELECT nombre, descripcion, precio_cinpacoin, stock,imagen FROM Productos LIMIT 2";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          // Iterar sobre los resultados y generar el HTML
          while($row = $result->fetch_assoc()) {
            echo '<div class="producto-card">';
              if (!empty($row["imagen"])) {
                  echo '<img src="' . htmlspecialchars($row["imagen"]) . '" alt="' . htmlspecialchars($row["nombre"]) . '">';
              } else {
                  echo '<img src="assets/img/producto_default.jpg" alt="' . htmlspecialchars($row["nombre"]) . '"> ';
              }
            echo '<h3>' . htmlspecialchars($row["nombre"]) . '</h3>';
            echo '<p>' . htmlspecialchars($row["descripcion"]) . '</p>';
            echo '<p class="precio">Precio: ' . htmlspecialchars($row["precio_cinpacoin"]) . ' CinpaCoins</p>';
            echo '<p class="stock">Stock: ' . htmlspecialchars($row["stock"]) . ' unidades</p>';
            echo '</div>';
          }
        } else {
          echo "<p>No se encontraron productos.</p>";
        }

        // Cierra la conexión (si no se cierra en el archivo de conexión)
        // $conn->close();
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
    <img src="../assets/img/reciclaje.png.png" alt="Reciclaje CIMPA" class="imagen-secundaria">
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
    <img src="../assets/img/logo.png" alt="Contacto CIMPA" class="imagen-tienda">
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