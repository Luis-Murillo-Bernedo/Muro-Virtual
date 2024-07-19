<?php
include("conexion.php");
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo "<script language='JavaScript'>
            alert('Debe iniciar sesión primero');
            location.assign('login.php');
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
    $usuario_id = $_SESSION['usuario_id'];

    $sql = "INSERT INTO muros (usuario_id, titulo, descripcion) VALUES ('$usuario_id', '$titulo', '$descripcion')";

    if (mysqli_query($conn, $sql)) {
        echo "<script language='JavaScript'>
                alert('Muro creado exitosamente');
                location.assign('principal.php');
              </script>";
    } else {
        echo "<script language='JavaScript'>
                alert('ERROR: No se pudo crear el muro');
                location.assign('nuevo_muro.php');
              </script>";
    }

    mysqli_close($conn);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Muro</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c2c2c;
            color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Crear Nuevo Muro</h2>
        <form action="nuevo_muro.php" method="post">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Crear</button>
        </form>
    </div>
</body>
</html>