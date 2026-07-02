<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_stock = (int) $_POST["id_stock"];
    $id_orden = !empty($_POST["id_orden"]) ? (int) $_POST["id_orden"] : null;
    $tipo = trim($_POST["tipo"]);
    $cantidad = (int) $_POST["cantidad"];
    $fecha = trim($_POST["fecha"]);

    try {
        $sql = "INSERT INTO movimiento (id_stock, id_orden, tipo, cantidad, fecha) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_stock, $id_orden, $tipo, $cantidad, $fecha]);

        $_SESSION['mensaje'] = 'agregado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=movimiento");
    exit();
}

