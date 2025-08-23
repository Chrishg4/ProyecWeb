<?php
require_once '../sesiones.php';

// Cerrar sesión y redirigir
cerrarSesion();
header('Location: ../index.php');
exit();
?>
