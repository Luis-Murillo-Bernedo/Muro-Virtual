<?php
    include("conexion.php");
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
        $nombre=$_POST['nombre'];
        $contraseña=$_POST['contraseña'];

        include("conexion.php");

        $sql = "SELECT contraseña FROM usuarios WHERE nombre = '".$nombre."'";

        $result = mysqli_query($conn, $sql);
        $fila = mysqli_fetch_array($result);

        $vresult = $fila['contraseña'];

        if($vresult == $contraseña){
            echo "<script language='JavaScript'>
                    alert('Se inició sesión exitosamente');
                    location.assign('main.php');
                    </script>";
        }else{
            echo "<script language='JavaScript'>
                    alert('ERROR: Usuario o contraseña incorrecta');
                    location.assign('login.php');
                    </script>";
        }

        mysqli_close($conn);
    }else{

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
