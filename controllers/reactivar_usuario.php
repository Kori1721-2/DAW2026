<?php
session_start();
include_once "../conexion/conex.php";

include_once '../conexion/conex.php';

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

if (!isset($_POST['idusuario']) || !is_numeric($_POST['idusuario'])) {
    die('ID de usuario inválido');
}

$id = (int) $_POST['idusuario'];

/* Verificar si el usuario existe */
$sql = "SELECT estd_Usuario FROM usuarios WHERE idusuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['mensaje'] = 'no_existe';
    header('Location: ../views/principal.php?vista=usuarios');
    exit();
}

/* Si ya está activo, no hacer nada */
if ((int)$usuario['estd_Usuario'] === 1) {
    $_SESSION['mensaje'] = 'ya_activo';
    header('Location: ../views/principal.php?vista=usuarios');
    exit();
}

/* Reactivar usuario */
$sql = "UPDATE usuarios
        SET estd_Usuario = 1
        WHERE idusuario = ?";

$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id])) {
    $_SESSION['mensaje'] = 'reactivado';
} else {
    $_SESSION['mensaje'] = 'error_reactivar';
}

header('Location: ../views/principal.php?vista=usuarios');
exit();
?>