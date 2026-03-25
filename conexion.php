<?php
// Configuración de conexión a la base de datos
$host = "localhost";
$user = "root";
$password = ""; // XAMPP no pide contraseña por defecto
$database = "robotixlab_db";

// Crear conexión
$conexion = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer charset a UTF-8
$conexion->set_charset("utf8");

// Configurar para mostrar errores (solo para desarrollo)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>