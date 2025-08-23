<?php
include 'conexion.php';
$perfil = $conexion->query("SELECT * FROM perfil LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Perfil</title>
    <style>
        :root {
            --main-blue: #25344b;
            --main-bg: #f4f4f4;
            --main-white: #fff;
            --main-shadow: 0 0 20px #bbb;
            --main-radius: 16px;
            --main-accent: #3a4d6a;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--main-bg);
            margin: 0;
        }
        .form-container {
            background: var(--main-white);
            max-width: 800px;
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
        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 14px 16px;
            border: 1px solid #d1d9e6;
            border-radius: 8px;
            font-size: 1.05em;
            background: #f8fafc;
            transition: border 0.2s;
            resize: vertical;
            min-height: 180px;
            margin-bottom: 20px;
        }
        textarea:focus {
            border: 1.5px solid var(--main-blue);
            outline: none;
            background: #fff;
        }
        button {
            background: var(--main-blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
            margin-right: 12px;
        }
        button:hover {
            background: var(--main-accent);
        }
        .btn-volver {
            background: #e0eafc;
            color: var(--main-blue);
            font-weight: 500;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-volver:hover {
            background: #cfdef3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Mi Perfil</h1>
        <form action="actualizar_perfil.php" method="POST">
            <textarea name="descripcion" required><?= $perfil ? htmlspecialchars($perfil['descripcion']) : '' ?></textarea>
            <button type="submit">Guardar</button>
            <button type="button" class="btn-volver" onclick="window.location.href='admin.php'">Volver</button>
        </form>
    </div>
</body>
</html>
