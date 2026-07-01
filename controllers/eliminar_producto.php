<?php
session_start();
include_once "../conexion/conex.php";

if (!isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

$id_producto = (int) $_POST['id_producto'];

try {
    $stmt = $conexion->prepare("SELECT imagen FROM producto WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    $imagen = $stmt->fetchColumn();

    if ($imagen && file_exists("../img/productos/$imagen")) {
        unlink("../img/productos/$imagen");
    }

    $stmt = $conexion->prepare("DELETE FROM producto WHERE id_producto = ?");
    if ($stmt->execute([$id_producto])) {
        $_SESSION['mensaje'] = 'eliminado';
    } else {
        $_SESSION['mensaje'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'error';
}

header('Location: ../views/principal.php?vista=producto');
exit();
