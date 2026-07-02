<?php
session_start();
include_once "../conexion/conex.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_movimiento = (int) $_POST["id_movimiento"];
    $id_stock = (int) $_POST["id_stock"];
    $id_orden = !empty($_POST["id_orden"]) ? (int) $_POST["id_orden"] : null;
    $tipo = trim($_POST["tipo"]);
    $cantidad = (int) $_POST["cantidad"];
    $fecha = trim($_POST["fecha"]);

    try {
        $sql = "UPDATE movimiento SET id_stock = ?, id_orden = ?, tipo = ?, cantidad = ?, fecha = ? WHERE id_movimiento = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_stock, $id_orden, $tipo, $cantidad, $fecha, $id_movimiento]);

        $_SESSION['mensaje'] = 'editado';
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = 'error';
    }

    header("Location: ../views/principal.php?vista=movimiento");
    exit();
}
