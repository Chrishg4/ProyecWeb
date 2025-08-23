
<?php
include 'conexion.php';
$id = $_GET['id'];
$conexion->query("DELETE FROM idiomas WHERE id=$id");
header("Location: editar_idiomas.php");
exit;
?>