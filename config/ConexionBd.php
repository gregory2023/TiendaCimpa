<?php
$host = "localhost:3306";
$usuario = "root";       
$contrasena = ""; // contraseña del usuario
$base_de_datos = "BD_Tienda_Cimpa"; 

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {

}

// Opcional: establecer codificación
$conn->set_charset("utf8");
?>
