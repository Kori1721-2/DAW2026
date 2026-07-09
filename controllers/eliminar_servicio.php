<?php
session_start();
include_once "../conexion/verificar_acceso.php";
include_once "../conexion/conex.php";

if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: ../views/trabajador.php");
    exit;
}

if (!isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

$id_servicio = (int) $_POST['id_servicio'];

$sql = "UPDATE servicio SET activo = 0 WHERE id_servicio = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_servicio])) {
    $_SESSION['mensaje'] = 'eliminado';
} else {
    $_SESSION['mensaje'] = 'error';
}

header('Location: ../views/principal.php?vista=paquete');
exit();
