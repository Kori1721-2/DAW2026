<?php
session_start();
include_once "../conexion/conex.php";

if (!isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

$id_categoria = (int) $_POST['id_categoria'];

$sql = "UPDATE categoria SET activo = 0 WHERE id_categoria = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_categoria])) {
    $_SESSION['mensaje'] = 'eliminado';
} else {
    $_SESSION['mensaje'] = 'error';
}

header('Location: ../views/principal.php?vista=categoria');
exit();
