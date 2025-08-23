<?php
session_start();
require_once 'conexion.php';

// Función para iniciar sesión
function iniciarSesion($usuario, $password) {
    global $conexion;
    
    try {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND usuario IS NOT NULL");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();
        
        if ($user && verificarPassword($password, $user['contrasena'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['privilegio'] = $user['privilegio'];
            $_SESSION['loggedin'] = true;
            return true;
        }
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

// Función para verificar si está logueado
function estaLogueado() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Función para verificar si es administrador
function esAdministrador() {
    return estaLogueado() && $_SESSION['privilegio'] === 'administrador';
}

// Función para verificar si es agente
function esAgente() {
    return estaLogueado() && $_SESSION['privilegio'] === 'agente';
}

// Función para cerrar sesión
function cerrarSesion() {
    session_unset();
    session_destroy();
}

// Función para requerir login
function requerirLogin() {
    if (!estaLogueado()) {
        header('Location: login.php');
        exit();
    }
}

// Función para requerir privilegios de administrador
function requerirAdmin() {
    if (!esAdministrador()) {
        header('Location: index.php');
        exit();
    }
}

// Función para obtener información del usuario actual
function obtenerUsuarioActual() {
    if (!estaLogueado()) {
        return null;
    }
    
    global $conexion;
    try {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        return null;
    }
}
?>
