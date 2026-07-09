<?php
session_start();
include_once "../conexion/verificar_acceso.php";
include_once "../conexion/conex.php";

if ($_SESSION['rol'] !== 'Administrador') {
    header("Location: ../views/trabajador.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_proveedor = (int) $_POST["id_proveedor"];
    $razon_social = trim($_POST["razon_social"]);
    $ruc = trim($_POST["ruc"]);
    $telefono = trim($_POST["telefono"] ?? "");
    $contacto = trim($_POST["contacto"] ?? "");

    try {
        $sql = "UPDATE proveedor SET razon_social = ?, ruc = ?, telefono = ?, contacto = ? WHERE id_proveedor = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$razon_social, $ruc, $telefono, $contacto, $id_proveedor]);

        $_SESSION['mensaje'] = 'editado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=proveedor");
    exit();
}
