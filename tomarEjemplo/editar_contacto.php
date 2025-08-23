<?php
include 'conexion.php';
$contacto = $conexion->query("SELECT * FROM contacto LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Contacto</title>
    <style>
        :root {
            --main-blue: #25344b;
            --main-bg: #f4f4f4;
            --main-white: #fff;
            --main-shadow: 0 0 20px #bbb;
            --main-radius: 18px;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--main-bg);
            margin: 0;
        }
        .form-container {
            background: var(--main-white);
            max-width: 480px;
            margin: 40px auto;
            padding: 36px 32px 28px 32px;
            border-radius: var(--main-radius);
            box-shadow: var(--main-shadow);
        }
        h1 {
            text-align: center;
            font-size: 2rem;
            color: var(--main-blue);
            margin-bottom: 24px;
            letter-spacing: 1px;
        }
        label {
            display: block;
            margin-top: 18px;
            font-weight: 500;
            color: var(--main-blue);
            letter-spacing: 0.5px;
        }
        .input-group {
            width: 100%;
            margin-bottom: 0;
        }
        .input-group input[type="text"],
        .input-group input[type="email"],
        .input-group input[type="file"] {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid #d1d9e6;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8fafc;
            transition: border 0.2s;
            display: block;
        }
        .input-group input[type="text"]:focus,
        .input-group input[type="email"]:focus {
            border: 1.5px solid var(--main-blue);
            outline: none;
            background: #fff;
        }
        .img-preview {
            display: block;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            margin: 18px auto 10px auto;
            box-shadow: 0 2px 8px rgba(60,60,120,0.10);
            border: 2px solid #e0eafc;
        }
        .input-group input[type="file"] {
            padding: 10px 0;
            background: #fff;
            border: 1px solid #c6d3e6;
        }
        button[type="submit"] {
            width: 100%;
            background: var(--main-blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 24px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: #3a4d6a;
        }
        a button {
            width: 100%;
            background: #e0eafc;
            color: var(--main-blue);
            border: none;
            border-radius: 8px;
            padding: 10px 0;
            font-size: 1rem;
            font-weight: 500;
            margin-top: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }
        a button:hover {
            background: #cfdef3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Contacto</h1>
        <form action="actualizar_contacto.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Nombre</label>
                <input name="nombre" type="text" value="<?= $contacto ? htmlspecialchars($contacto['nombre']) : '' ?>" required>
            </div>
            <div class="input-group">
                <label>Dirección</label>
                <input name="direccion" type="text" value="<?= $contacto ? htmlspecialchars($contacto['direccion']) : '' ?>" required>
            </div>
            <div class="input-group">
                <label>Teléfono</label>
                <input name="telefono" type="text" value="<?= $contacto ? htmlspecialchars($contacto['telefono']) : '' ?>" required>
            </div>
            <div class="input-group">
                <label>Email</label>
                <input name="email" type="email" value="<?= $contacto ? htmlspecialchars($contacto['email']) : '' ?>" required>
            </div>
            <div class="input-group">
                <label>Web</label>
                <input name="web" type="text" value="<?= $contacto ? htmlspecialchars($contacto['web']) : '' ?>">
            </div>
            <div class="input-group">
                <label>Título profesional</label>
                <input name="titulo" type="text" value="<?= $contacto ? htmlspecialchars($contacto['titulo']) : '' ?>">
            </div>
            <label>Imagen de perfil</label>
            <?php if ($contacto && $contacto['foto']): ?>
                <img src="<?= htmlspecialchars($contacto['foto']) ?>" class="img-preview" alt="Foto actual">
            <?php endif; ?>
            <div class="input-group">
                <input type="file" name="foto">
            </div>
            <button type="submit">Guardar</button>
        </form>
        <a href="admin.php"><button>Cancelar</button></a>
    </div>
</body>
</html>