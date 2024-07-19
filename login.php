<?php
    include("conexion.php");
    session_start();
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<?php
    if(isset($_POST['login'])){
        $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
        $contraseña = mysqli_real_escape_string($conn, $_POST['contraseña']);

        $sql = "SELECT id, nombre, contraseña FROM usuarios WHERE nombre = '".$nombre."'";

        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) == 1) {
            $fila = mysqli_fetch_array($result);
            $vresult = $fila['contraseña'];

            if($vresult == $contraseña){
                // Iniciar la sesión y guardar datos
                $_SESSION['usuario_id'] = $fila['id'];
                $_SESSION['nombre_usuario'] = $fila['nombre'];

                echo "<script language='JavaScript'>
                        alert('Se inició sesión exitosamente');
                        location.assign('principal.php');
                        </script>";
            } else {
                echo "<script language='JavaScript'>
                        alert('ERROR: Usuario o contraseña incorrecta');
                        location.assign('login.php');
                        </script>";
            }
        } else {
            echo "<script language='JavaScript'>
                    alert('ERROR: Usuario no encontrado');
                    location.assign('login.php');
                    </script>";
        }

        mysqli_close($conn);
    } else {
?>

    <div class="container">
        <h2>Inicie sesión</h2>
        <form id="userForm" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="text" name="nombre" placeholder="Nombre de usuario" required>
            <input type="password" name="contraseña" placeholder="Contraseña" required>
            <input type="submit" name="login" value="Confirmar">
        </form>
    </div>

<?php
    }
?>

</body>
</html>