<?php
session_start();
include_once "../conexion/conex.php";

if (!isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

$id_almacen = (int) $_POST['id_almacen'];

$sql = "UPDATE almacen SET activo = 0 WHERE id_almacen = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_almacen])) {
    $_SESSION['mensaje'] = 'eliminado';
} else {
    $_SESSION['mensaje'] = 'error';
}

header('Location: ../views/principal.php?vista=almacen');
exit();
