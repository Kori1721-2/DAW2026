<?php
session_start();
include_once "../conexion/conex.php";

if (!isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

$id_movimiento = (int) $_POST['id_movimiento'];

try {
    $stmt = $conexion->prepare("DELETE FROM movimiento WHERE id_movimiento = ?");
    if ($stmt->execute([$id_movimiento])) {
        $_SESSION['mensaje'] = 'eliminado';
    } else {
        $_SESSION['mensaje'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'error';
}

header('Location: ../views/principal.php?vista=movimiento');
exit();

