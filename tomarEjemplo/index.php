<?php
include 'conexion.php';
$contacto = $conexion->query("SELECT * FROM contacto LIMIT 1")->fetch_assoc();
$perfil = $conexion->query("SELECT * FROM perfil LIMIT 1")->fetch_assoc();
$educacion = $conexion->query("SELECT * FROM educacion");
$experiencia = $conexion->query("SELECT * FROM experiencia");
$conocimientos = $conexion->query("SELECT * FROM conocimientos");
$idiomas = $conexion->query("SELECT * FROM idiomas");
$info_adicional = $conexion->query("SELECT * FROM info_adicional");
$portafolio = $conexion->query("SELECT * FROM portafolio");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Currículum Vitae</title>
    <link rel="stylesheet" href="cv_style.css">
    <!-- Puedes usar FontAwesome para los íconos si lo deseas -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<div class="cv-main">
    <div class="cv-left">
        <img class="cv-photo" src="<?= htmlspecialchars($contacto && $contacto['foto'] ? $contacto['foto'] : 'foto_default.jpg') ?>" alt="Foto">
        <div class="cv-name"><?= $contacto ? htmlspecialchars($contacto['nombre']) : 'Nombre Apellido' ?></div>
        <div class="cv-title"><?= $contacto ? htmlspecialchars($contacto['titulo']) : 'Título Profesional' ?></div>
        <div class="cv-section-title">CONTACTO</div>
        <ul class="cv-contact-list">
            <li><i class="fa fa-phone"></i> <?= $contacto ? htmlspecialchars($contacto['telefono']) : 'XXXX-XX-XX' ?></li>
            <li><i class="fa fa-envelope"></i> <?= $contacto ? htmlspecialchars($contacto['email']) : 'correo@correo.com' ?></li>
            <li><i class="fa fa-globe"></i> <?= $contacto ? htmlspecialchars($contacto['web']) : 'www.sitio.com' ?></li>
            <li><i class="fa fa-map-marker-alt"></i> <?= $contacto ? htmlspecialchars($contacto['direccion']) : 'Dirección, Ciudad, CP' ?></li>
        </ul>
        <div class="cv-section-title">INFORMACIÓN ADICIONAL</div>
        <ul class="cv-info-list">
            <?php if ($info_adicional && $info_adicional->num_rows > 0): ?>
                <?php while($info = $info_adicional->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($info['dato']) ?></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>Carnet de conducir</li>
                <li>Coche propio</li>
                <li>Disponibilidad para viajar</li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="cv-right">
        <a href="admin.php" class="admin-btn"><button>Administrar</button></a>
        <div class="cv-block">
            <div class="cv-block-title">MI PERFIL</div>
            <div class="cv-block-content">
                <?= $perfil ? htmlspecialchars($perfil['descripcion']) : 'Descripción breve del perfil profesional...' ?>
            </div>
        </div>
        <div class="cv-block">
            <div class="cv-block-title">EXPERIENCIA</div>
            <div class="cv-block-content">
                <?php if ($experiencia && $experiencia->num_rows > 0): ?>
                    <?php while($exp = $experiencia->fetch_assoc()): ?>
                        <div style="margin-bottom:12px;">
                            <b><?= htmlspecialchars($exp['puesto']) ?></b>
                            <br>
                            <span style="font-weight:bold"><?= htmlspecialchars($exp['empresa']) ?> (<?= htmlspecialchars($exp['fecha']) ?>)</span>
                            <br>
                            <span><?= htmlspecialchars($exp['descripcion']) ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div>No hay datos de experiencia.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="cv-block">
            <div class="cv-block-title">FORMACIÓN ACADÉMICA</div>
            <div class="cv-block-content">
                <?php if ($educacion && $educacion->num_rows > 0): ?>
                    <?php while($edu = $educacion->fetch_assoc()): ?>
                        <div>
                            <b><?= htmlspecialchars($edu['institucion']) ?></b><br>
                            <?= htmlspecialchars($edu['titulo']) ?> (<?= htmlspecialchars($edu['fecha']) ?>)
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div>No hay datos de educación.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="cv-block">
            <div class="cv-block-title">CONOCIMIENTOS</div>
            <div class="cv-block-content">
                <?php if ($conocimientos && $conocimientos->num_rows > 0): ?>
                    <ul>
                        <?php while($con = $conocimientos->fetch_assoc()): ?>
                            <li><?= htmlspecialchars($con['conocimiento']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <ul>
                        <li>Contabilidad</li>
                        <li>Asesoría financiera</li>
                        <li>Hojas de cálculo</li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="cv-block">
            <div class="cv-block-title">IDIOMAS</div>
            <div class="cv-block-content">
                <?php if ($idiomas && $idiomas->num_rows > 0): ?>
                    <ul>
                        <?php while($idioma = $idiomas->fetch_assoc()): ?>
                            <li><?= htmlspecialchars($idioma['idioma']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <ul>
                        <li>Español</li>
                        <li>Inglés</li>
                        <li>Italiano</li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="cv-portfolio">
            PORTAFOLIO
        </div>
    </div>
</div>
<script>
    // Al cargar el currículum, aplica el color guardado (o azul por defecto)
    window.onload = function() {
        var color = localStorage.getItem('cvColor') || '#25344b';
        document.documentElement.style.setProperty('--main-blue', color);
    };
</script>
</body>
</html>