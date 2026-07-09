<?php
session_start();
include_once "../conexion/verificar_acceso.php";
include_once "../conexion/conex.php";

if (!isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die('Token CSRF inválido');
}

$id_stock = (int) $_POST['id_stock'];

$sql = "DELETE FROM stock WHERE id_stock = ?";
$stmt = $conexion->prepare($sql);

if ($stmt->execute([$id_stock])) {
    $_SESSION['mensaje'] = 'eliminado';
} else {
    $_SESSION['mensaje'] = 'error';
}

header('Location: ../views/' . ($_SESSION['rol'] === 'Administrador' ? 'principal' : 'trabajador') . '.php?vista=stock');
exit();
