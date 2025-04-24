<?php

$nombre = "";
$imagen = "";
$precio = 10;
$descripcion = "";
$tipo = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<article class="producto">
    <h2 class="producto_nombre"><?php echo $producto_nombre; ?></h2>
    <img src="<?php echo $imagen; ?>" alt= <?php echo $nombre; ?>" width="200">
    <p><strong>Precio:</strong> €<?php echo number_format($precio, 2); ?></p>
    <p><?php echo $descripcion; ?></p>
    <p><?php echo $tipo; ?>
</article>

    
</body>
</html>