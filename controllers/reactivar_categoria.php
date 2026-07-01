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

if (!isset($_POST['id_categoria']) || !is_numeric($_POST['id_categoria'])) {
    die('ID de categoría inválido');
}

$id_categoria = (int) $_POST['id_categoria'];

$sql = "SELECT activo FROM categoria WHERE id_categoria = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_categoria]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    $_SESSION['mensaje'] = 'no_existe';
    header('Location: ../views/principal.php?vista=categoria');
    exit();
}

if ((int)$categoria['activo'] === 1) {
    $_SESSION['mensaje'] = 'ya_activo';
    header('Location: ../views/principal.php?vista=categoria');
    exit();
}

$sql = "UPDATE categoria SET activo = 1 WHERE id_categoria = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_categoria])) {
    $_SESSION['mensaje'] = 'reactivado';
} else {
    $_SESSION['mensaje'] = 'error_reactivar';
}

header('Location: ../views/principal.php?vista=categoria');
exit();
