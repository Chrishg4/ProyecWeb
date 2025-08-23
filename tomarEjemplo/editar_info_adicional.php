<?php
include 'conexion.php';
$info_adicional = $conexion->query("SELECT * FROM info_adicional");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Información Adicional</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px 8px;
            border-bottom: 1px solid #d1d1d1;
            text-align: left;
            vertical-align: middle;
        }
        th {
            color: var(--main-blue);
            font-size: 1.05em;
            background: #e9e9e9;
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none;
        }
        input[type="text"], textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            border: 1px solid #d1d9e6;
            border-radius: 8px;
            font-size: 1em;
            background: #f8fafc;
            transition: border 0.2s;
            margin: 4px 0;
            display: block;
            resize: vertical;
        }
        input[type="text"]:focus, textarea:focus {
            border: 1.5px solid var(--main-blue);
            outline: none;
            background: #fff;
        }
    
        input[name="dato"]::placeholder {
            font-size: 1.05em;
        }

        input[name="dato"] {
            width: 100%;
            padding: 14px 16px;
            font-size: 1.1em;
            border: 1px solid #d1d9e6;
            border-radius: 8px;
            background: #f8fafc;
            box-sizing: border-box;
            transition: border 0.2s;
            field-sizing: content;
        }

        button {
            background: var(--main-blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1em;
            cursor: pointer;
            margin-left: 5px;
            margin-top: 0;
            transition: background 0.2s;
        }
        button:hover {
            background: var(--main-accent);
        }
        a button {
            background: #e0eafc;
            color: var(--main-blue);
            font-weight: 500;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1em;
            cursor: pointer;
            margin-left: 0;
            margin-top: 10px;
            width: 100%;
            transition: background 0.2s;
        }
        a button:hover {
            background: #cfdef3;
        }
        td form {
            margin: 0;
        }
        td.acciones {
            display: flex;
            gap: 8px;
            align-items: center;
            white-space: nowrap;
        }
        td.acciones button,
        td.acciones a button {
            width: auto;
            min-width: 80px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Información Adicional</h1>
        <table>
            <tr>
                <th>Dato</th>
                <th>Acción</th>
            </tr>
            <?php while($info = $info_adicional->fetch_assoc()): ?>
            <tr>
                <form action="actualizar_info_adicional.php" method="POST">
                    <td>
                        <input name="dato" value="<?= htmlspecialchars($info['dato']) ?>">
                        <input type="hidden" name="id" value="<?= $info['id'] ?>">
                    </td>
                    <td class="acciones">
                        <button type="submit">Guardar</button>
                        <a href="eliminar_info_adicional.php?id=<?= $info['id'] ?>">
                            <button type="button">Eliminar</button>
                        </a>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
            <tr>
                <form action="agregar_info_adicional.php" method="POST">
                    <td>
                        <input name="dato" placeholder="Nuevo dato">
                    </td>
                    <td>
                        <button type="submit">Agregar</button>
                    </td>
                </form>
            </tr>
        </table>
        <a href="admin.php"><button>Volver</button></a>
    </div>
</body>
</html>

