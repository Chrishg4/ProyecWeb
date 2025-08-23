<?php
$conexion = new mysqli("localhost", "root", "", "servicios_costa_rica");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
