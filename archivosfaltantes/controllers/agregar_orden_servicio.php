<?php
session_start();
include_once "../conexion/verificar_acceso.php";
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_servicio = (int) $_POST["id_servicio"];
    $fecha = trim($_POST["fecha"]);
    $cliente = trim($_POST["cliente"]);
    $estado = trim($_POST["estado"]);
    $observaciones = trim($_POST["observaciones"] ?? "");

    try {
        $sql = "INSERT INTO orden_servicio (id_servicio, fecha, cliente, estado, observaciones) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_servicio, $fecha, $cliente, $estado, $observaciones]);

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/" . ($_SESSION['rol'] === 'Administrador' ? 'principal' : 'trabajador') . ".php?vista=orden_paquete");
    exit();
}
