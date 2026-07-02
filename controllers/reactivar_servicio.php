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

if (!isset($_POST['id_servicio']) || !is_numeric($_POST['id_servicio'])) {
    die('ID de servicio inválido');
}

$id_servicio = (int) $_POST['id_servicio'];

$sql = "SELECT activo FROM servicio WHERE id_servicio = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_servicio]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    $_SESSION['mensaje'] = 'no_existe';
    header('Location: ../views/principal.php?vista=servicio');
    exit();
}

if ((int)$servicio['activo'] === 1) {
    $_SESSION['mensaje'] = 'ya_activo';
    header('Location: ../views/principal.php?vista=servicio');
    exit();
}

$sql = "UPDATE servicio SET activo = 1 WHERE id_servicio = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_servicio])) {
    $_SESSION['mensaje'] = 'reactivado';
} else {
    $_SESSION['mensaje'] = 'error_reactivar';
}

header('Location: ../views/principal.php?vista=servicio');
exit();
