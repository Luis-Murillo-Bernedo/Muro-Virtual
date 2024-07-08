<?php
    include("conexion.php");
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuario</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<?php
    if(isset($_POST['registrar'])){
        $nombre=$_POST['nombre'];
        $contraseña=$_POST['contraseña'];

        include("conexion.php");

        $sql = "INSERT INTO usuarios(nombre,contraseña) VALUES('".$nombre."', '".$contraseña."')";

        $result = mysqli_query($conn, $sql);

        if($result){
            echo "<script language='JavaScript'>
                    alert('El proceso de registro se realizó con éxito');
                    location.assign('main.php');
                    </script>";
        }else{
            echo "<script language='JavaScript'>
                    alert('ERROR: Los datos no se pudieron registrar correctamente');
                    location.assign('registro.php');
                    </script>";
        }

        mysqli_close($conn);
    }else{

?>

    <div class="container">
        <h2>Regístrate</h2>
        <form id="userForm" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <input type="text" name="nombre" placeholder="Nombre de usuario" required>
            <input type="password" name="contraseña" placeholder="Contraseña" required>
            <input type="submit" name="registrar" value="Continuar">
        </form>
    </div>

<?php
    }
?>

</body>
</html>
