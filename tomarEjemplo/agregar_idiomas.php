
<?php
include 'conexion.php';
$idioma = $_POST['idioma'];
$conexion->query("INSERT INTO idiomas (idioma) VALUES ('$idioma')");
header("Location: editar_idiomas.php");
exit;
?>