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

$usuario_id = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['nombre_usuario'];

$sql = "SELECT * FROM muros WHERE usuario_id = '$usuario_id'";
$result = mysqli_query($conn, $sql);
$muros = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c2c2c;
            color: #f1f1f1;
        }
        .muro {
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
            background-color: #3a3a3a;
            border: 1px solid #555;
            color: #f1f1f1;
        }
        .muro:hover {
            transform: scale(1.05);
            background-color: #555;
        }
        .nav-link {
            color: #f1f1f1;
        }
        .nav-link.active {
            color: #ffca28;
        }
        .nav-link:hover {
            color: #ffca28;
        }
        .logout-btn {
            color: #f1f1f1;
            background-color: #d9534f;
            border: none;
        }
        .logout-btn:hover {
            background-color: #c9302c;
        }
        .navbar-nav {
            display: flex;
            flex-direction: column;
            height: 60vh; /* Cambiar altura a 80vh */
            justify-content: space-between;
        }
    </style>
    <script>
        function redirectToMuro(muroId) {
            var form = document.createElement("form");
            form.method = "post";
            form.action = "set_muro.php";

            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "muro_id";
            input.value = muroId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <h2>Hola, <?php echo htmlspecialchars($nombre_usuario); ?>!</h2>
                <p>¡Que tengas un feliz día!</p>
                <nav class="navbar-nav">
                    <a class="nav-link active" href="nuevo_muro.php">Crear</a>
                    <form action="logout.php" method="post" class="nav-item">
                        <button type="submit" class="btn logout-btn w-100">Cerrar Sesión</button>
                    </form>
                </nav>
            </div>
            <div class="col-md-9">
                <h3>Mis Muros</h3>
                <div class="row">
                    <?php foreach ($muros as $muro): ?>
                        <div class="col-md-4">
                            <div class="card mb-4 muro" onclick="redirectToMuro(<?php echo $muro['muro_id']; ?>)">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($muro['titulo']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($muro['descripcion']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>