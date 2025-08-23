
<?php
include 'conexion.php';
$dato = $_POST['dato'];
if ($dato != "") {
    $conexion->query("INSERT INTO info_adicional (dato) VALUES ('$dato')");
}
header("Location: editar_info_adicional.php");
exit;
?>