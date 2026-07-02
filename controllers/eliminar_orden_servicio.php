<?php
session_start();
include_once "../conexion/conex.php";

if (!isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

$id_orden = (int) $_POST['id_orden'];

try {
    $stmt = $conexion->prepare("DELETE FROM orden_servicio WHERE id_orden = ?");
    if ($stmt->execute([$id_orden])) {
        $_SESSION['mensaje'] = 'eliminado';
    } else {
        $_SESSION['mensaje'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'error';
}

header('Location: ../views/principal.php?vista=orden_paquete');
exit();
