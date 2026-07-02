<?php
session_start();
include_once "../conexion/verificar_acceso.php";
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = (int) $_POST["id_producto"];
    $id_almacen = (int) $_POST["id_almacen"];
    $cantidad = (int) $_POST["cantidad"];
    $fecha_actualizacion = $_POST["fecha_actualizacion"];

    try {
        $sql = "INSERT INTO stock (id_producto, id_almacen, cantidad, fecha_actualizacion) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_producto, $id_almacen, $cantidad, $fecha_actualizacion]);

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/" . ($_SESSION['rol'] === 'Administrador' ? 'principal' : 'trabajador') . ".php?vista=stock");
    exit();
}
