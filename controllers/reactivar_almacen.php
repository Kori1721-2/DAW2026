<?php
session_start();
include_once "../conexion/conex.php";

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

if (!isset($_POST['id_almacen']) || !is_numeric($_POST['id_almacen'])) {
    die('ID de almacén inválido');
}

$id_almacen = (int) $_POST['id_almacen'];

$sql = "SELECT activo FROM almacen WHERE id_almacen = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_almacen]);
$almacen = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$almacen) {
    $_SESSION['mensaje'] = 'no_existe';
    header('Location: ../views/principal.php?vista=almacen');
    exit();
}

if ((int)$almacen['activo'] === 1) {
    $_SESSION['mensaje'] = 'ya_activo';
    header('Location: ../views/principal.php?vista=almacen');
    exit();
}

$sql = "UPDATE almacen SET activo = 1 WHERE id_almacen = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_almacen])) {
    $_SESSION['mensaje'] = 'reactivado';
} else {
    $_SESSION['mensaje'] = 'error_reactivar';
}

header('Location: ../views/principal.php?vista=almacen');
exit();
