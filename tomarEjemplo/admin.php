<?php
include 'conexion.php';
$contacto = $conexion->query("SELECT * FROM contacto LIMIT 1")->fetch_assoc();
$info_adicional = $conexion->query("SELECT * FROM info_adicional");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Administrar Currículum</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Administrar Currículum</h1>
        <div class="section">
            <h2>Contacto</h2>
         
                <a href="editar_contacto.php" class="edit-btn"><button>Editar</button></a>
        
        </div>
        <div class="section">
            <h2>Información Adicional</h2>
            
            <a href="editar_info_adicional.php"><button>Editar</button></a>
        </div>
        <div class="section">
            <h2>Educación</h2>
            <a href="editar_educacion.php"><button>Editar</button></a>
        </div>
        <div class="section">
            <h2>Experiencia</h2>
            <a href="editar_experiencia.php"><button>Editar</button></a>
        </div>
        <div class="section">
            <h2>Conocimientos</h2>
            <a href="editar_habilidades.php"><button>Editar</button></a>
        </div>
        <div class="section">
            <h2>Mi Perfil</h2>
            <a href="editar_perfil.php"><button>Editar</button></a>
        </div>
        <div class="section">
            <h2>Idiomas</h2>
            <a href="editar_idiomas.php"><button>Editar</button></a>
        </div>
        <a href="index.php"><button class="btn-claro">Ver Currículum</button></a>

        <!-- Selector de color y botón para aplicar, al final del panel -->
        <div style="margin-top:40px; display:flex; align-items:center; gap:15px;">
            <label style="font-weight:bold; color:var(--main-blue);">Color principal del currículum:</label>
            <input type="color" id="colorPicker" value="#25344b" style="width:50px; height:35px; border:none;">
            <button id="aplicarColor" type="button">Aplicar color</button>
            <button id="resetColor" type="button">Restablecer color</button>
        </div>
        <script>
            document.getElementById('aplicarColor').onclick = function() {
                var color = document.getElementById('colorPicker').value;
                // Guarda el color SOLO para el currículum
                localStorage.setItem('cvColor', color);
                alert('¡Color aplicado! Ve al currículum para ver el cambio.');
            };
            document.getElementById('resetColor').onclick = function() {
                localStorage.removeItem('cvColor');
                document.getElementById('colorPicker').value = '#25344b';
                alert('Color restablecido. El currículum volverá al azul original.');
            };
            // Opcional: muestra el color guardado si existe
            window.onload = function() {
                var color = localStorage.getItem('cvColor');
                if (color) {
                    document.getElementById('colorPicker').value = color;
                }
            };
        </script>
    </div>
</body>
</html>