<?php
session_start();

if (isset($_POST['muro_id'])) {
    $_SESSION['muro_id'] = $_POST['muro_id'];
    header("Location: http://localhost:3000/?muro_id=" . $_POST['muro_id']);
    exit();
} else {
    echo "<script language='JavaScript'>
            alert('ERROR: Muro no seleccionado');
            location.assign('principal.php');
          </script>";
    exit();
}
?>