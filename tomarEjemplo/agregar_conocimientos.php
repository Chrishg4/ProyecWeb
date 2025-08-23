
<?php
include 'conexion.php';
$conocimiento = $_POST['conocimiento'];
$conexion->query("INSERT INTO conocimientos (conocimiento) VALUES ('$conocimiento')");
header("Location: editar_habilidades.php");
exit;
?>