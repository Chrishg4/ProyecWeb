<?php
include 'conexion.php';
$experiencia = $conexion->query("SELECT * FROM experiencia");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Experiencia</title>
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
            margin-left: 0;
            margin-top: 10px;
            width: 100%;
            font-weight: 500;
        }
        a button:hover {
            background: #cfdef3;
        }
        td form {
            margin: 0;
        }
        td[style*="white-space:nowrap;"], td.acciones {
            display: flex;
            gap: 8px;
            align-items: center;
            white-space: nowrap;
        }
        td[style*="white-space:nowrap;"] button,
        td.acciones button,
        td[style*="white-space:nowrap;"] a button,
        td.acciones a button {
            width: auto;
            min-width: 80px;
            margin: 0;
        }
        .btn-claro {
    background: #e0eafc;
    color: var(--main-blue);
    font-weight: 500;
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-size: 1em;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-claro:hover {
    background: #cfdef3;
}

    </style>
</head>
<body>
    <div class="form-container">
        <h1>Editar Experiencia</h1>
        <table>
            <tr>
                <th>Puesto</th>
                <th>Empresa</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Acción</th>
            </tr>
            <?php while($exp = $experiencia->fetch_assoc()): ?>
            <tr>
                <form action="actualizar_experiencia.php" method="POST">
                    <td><input name="puesto" type="text" value="<?= htmlspecialchars($exp['puesto']) ?>"></td>
                    <td><input name="empresa" type="text" value="<?= htmlspecialchars($exp['empresa']) ?>"></td>
                    <td><input name="fecha" type="text" value="<?= htmlspecialchars($exp['fecha']) ?>"></td>
                    <td><textarea name="descripcion"><?= htmlspecialchars($exp['descripcion']) ?></textarea></td>
                    <td class="acciones">
                        <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                        <button type="submit">Guardar</button>
                        <a href="eliminar_experiencia.php?id=<?= $exp['id'] ?>"><button type="button" class="btn-claro">Eliminar</button></a>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
            <tr>
                <form action="agregar_experiencia.php" method="POST">
                    <td><input name="puesto" type="text" placeholder="Nuevo puesto"></td>
                    <td><input name="empresa" type="text" placeholder="Empresa"></td>
                    <td><input name="fecha" type="text" placeholder="Fecha"></td>
                    <td><textarea name="descripcion" placeholder="Descripción"></textarea></td>
                    <td><button type="submit">Agregar</button></td>
                </form>
            </tr>
        </table>
        <a href="admin.php"><button>Volver</button></a>
    </div>
</body>
</html>