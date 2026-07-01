<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $razon_social = trim($_POST["razon_social"]);
    $ruc = trim($_POST["ruc"]);
    $telefono = trim($_POST["telefono"] ?? "");
    $contacto = trim($_POST["contacto"] ?? "");

    try {
        $sql = "INSERT INTO proveedor (razon_social, ruc, telefono, contacto, activo) VALUES (?, ?, ?, ?, 1)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$razon_social, $ruc, $telefono, $contacto]);

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=proveedor");
    exit();
}