<?php
include 'conexion.php';
$idiomas = $conexion->query("SELECT * FROM idiomas");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Idiomas</title>
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
            max-width: 500px;
            margin: 40px auto;
            padding: 32px 28px 24px 28px;
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
        input[type="text"] {
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
        }
        input[type="text"]:focus {
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
        .acciones {
            display: flex;
            gap: 8px;
            align-items: center;
            white-space: nowrap;
        }
        .acciones button {
            width: auto;
            min-width: 80px;
            margin: 0;
            background: var(--main-blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .acciones button:hover {
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

        .btn-claro {
    background: #e0eafc !important; 
    color: var(--main-blue)    !important;
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
        <h1>Editar Idiomas</h1>
        <table>
            <tr>
                <th>Idioma</th>
                <th>Acción</th>
            </tr>
            <?php while($idioma = $idiomas->fetch_assoc()): ?>
            <tr>
                <form action="actualizar_idiomas.php" method="POST">
                    <td>
                        <input name="idioma" value="<?= htmlspecialchars($idioma['idioma']) ?>">
                        <input type="hidden" name="id" value="<?= $idioma['id'] ?>">
                    </td>
                    <td class="acciones">
                        <button type="submit">Guardar</button>
                        <button type="submit" formaction="eliminar_idiomas.php?id=<?= $idioma['id'] ?>" formmethod="post" class="btn-claro">Eliminar</button>


                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
            <tr>
                <form action="agregar_idiomas.php" method="POST">
                    <td><input name="idioma" placeholder="Nuevo idioma"></td>
                    <td><button type="submit">Agregar</button></td>
                </form>
            </tr>
        </table>
        <a href="admin.php"><button>Volver</button></a>
    </div>
</body>
</html>