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

if (!isset($_POST['id_proveedor']) || !is_numeric($_POST['id_proveedor'])) {
    die('ID de proveedor inválido');
}

$id_proveedor = (int) $_POST['id_proveedor'];

$sql = "SELECT activo FROM proveedor WHERE id_proveedor = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_proveedor]);
$proveedor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proveedor) {
    $_SESSION['mensaje'] = 'no_existe';
    header('Location: ../views/principal.php?vista=proveedor');
    exit();
}

if ((int)$proveedor['activo'] === 1) {
    $_SESSION['mensaje'] = 'ya_activo';
    header('Location: ../views/principal.php?vista=proveedor');
    exit();
}

$sql = "UPDATE proveedor SET activo = 1 WHERE id_proveedor = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_proveedor])) {
    $_SESSION['mensaje'] = 'reactivado';
} else {
    $_SESSION['mensaje'] = 'error_reactivar';
}

header('Location: ../views/principal.php?vista=proveedor');
exit();