<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_stock = (int) $_POST["id_stock"];
    $id_producto = (int) $_POST["id_producto"];
    $id_almacen = (int) $_POST["id_almacen"];
    $cantidad = (int) $_POST["cantidad"];
    $fecha_actualizacion = $_POST["fecha_actualizacion"];

    try {
        $sql = "UPDATE stock SET id_producto = ?, id_almacen = ?, cantidad = ?, fecha_actualizacion = ? WHERE id_stock = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_producto, $id_almacen, $cantidad, $fecha_actualizacion, $id_stock]);

        $_SESSION['mensaje'] = 'editado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=stock");
    exit();
}

